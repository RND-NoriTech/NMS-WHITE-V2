<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Link;
use App\Models\NmsSite;
use App\Models\NmsSiteDevice;
use App\Models\Port;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use LibreNMS\Util\Number;

/*
|--------------------------------------------------------------------------
| SITE TOPOLOGY CONTROLLER (NMS-WHITE DEVICE MAP)
|--------------------------------------------------------------------------
|
| Device Map / network topology for a NMS-WHITE Site.
|
| LibreNMS stays the source of truth:
|   - site membership comes from nms_site_devices
|   - LLDP/CDP links come from the native LibreNMS links table
|     (populated by LibreNMS discovery from LLDP-MIB and CISCO-CDP-MIB)
|   - device status, port status, speed and RX/TX come from native
|     LibreNMS devices/ports data
|
| This module never polls SNMP and never stores topology data.
| Expansion is limited to site devices + their direct neighbours.
|
*/

class NmsSiteTopologyController extends Controller
{
    public function index(NmsSite $site)
    {
        return view(
            'sites.topology.index',
            compact('site')
        );
    }

    public function data(NmsSite $site): JsonResponse
    {
        /*
        |--------------------------------------------------------------------------
        | 1. DEVICES ASSIGNED TO THIS SITE (nms_site_devices)
        |--------------------------------------------------------------------------
        */

        $siteDevices = NmsSiteDevice::with('device')
            ->where('site_id', $site->id)
            ->get();

        $managed = $siteDevices
            ->pluck('device')
            ->filter()
            ->keyBy('device_id');

        /*
        |--------------------------------------------------------------------------
        | 2. DIRECT LLDP/CDP LINKS OF SITE DEVICES
        |--------------------------------------------------------------------------
        |
        | The native LibreNMS links table holds one row per discovered
        | neighbour relationship (protocol = lldp / cdp / other xdp).
        | remote_device_id stays 0 until LibreNMS resolved the neighbour
        | to an existing device. Only one hop is loaded.
        |
        */

        $managedIds = $managed->keys()->all();

        if (empty($managedIds)) {
            return response()->json($this->payload($site, [], [], [
                'site_devices' => 0,
                'managed' => 0,
                'neighbors' => 0,
                'links' => 0,
            ]));
        }

        $links = Link::with(['device', 'port', 'remoteDevice', 'remotePort'])
            ->where('active', 1)
            ->where(function (Builder $query) use ($managedIds) {
                $query->whereIn('local_device_id', $managedIds)
                    ->orWhereIn('remote_device_id', $managedIds);
            })
            ->orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 3. RESOLVE ALL LINK ENDPOINT DEVICES (NATIVE LIBRENMS DEVICES)
        |--------------------------------------------------------------------------
        |
        | Link rows are one directional, so the local end of a row can be a
        | device outside the Site (the Site device being the remote end).
        | Nodes are built from every endpoint device of the loaded links so
        | no dangling edges are created. Site membership comes from the
        | managed map, not from the endpoint list.
        |
        */

        $deviceIds = $links->pluck('local_device_id')
            ->map(fn ($id) => (int) $id)
            ->merge(
                $links->pluck('remote_device_id')
                    ->map(fn ($id) => (int) $id)
                    ->filter(fn ($id) => $id > 0)
            )
            ->unique()
            ->values()
            ->all();

        $devices = Device::whereIn('device_id', $deviceIds)
            ->get()
            ->keyBy('device_id');

        /*
        |--------------------------------------------------------------------------
        | 4. BUILD NODES
        |--------------------------------------------------------------------------
        */

        $nodes = [];

        foreach ($devices as $device) {
            $isSiteDevice = $managed->has($device->device_id);

            $nodes[] = $this->deviceNode(
                $device,
                $isSiteDevice ? 'site' : 'managed',
                $isSiteDevice ? $site->name : null
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 5. BUILD EDGES + DISCOVERED-NEIGHBOUR NODES
        |--------------------------------------------------------------------------
        */

        $edges = [];
        $edgeIndex = [];
        $neighborNodes = [];

        foreach ($links as $link) {
            $fromId = 'd' . $link->local_device_id;

            $localPortName = $link->port?->ifName ?: $link->port?->ifDescr ?: null;
            $remotePortName = $link->remotePort?->ifName ?: ($link->remote_port ?: null);

            $resolvedRemote = (int) $link->remote_device_id > 0;

            if ($resolvedRemote) {
                $toId = 'd' . $link->remote_device_id;
            } else {
                $neighborHost = $link->remote_hostname ?: 'Unknown Neighbor';

                $toId = 'n' . md5($neighborHost);

                if (! isset($neighborNodes[$toId])) {
                    $neighborNodes[$toId] = [
                        'id' => $toId,
                        'kind' => 'neighbor',
                        'state' => 'Discovered Neighbor',
                        'label' => $neighborHost,
                        'system_name' => $neighborHost,
                        'platform' => $link->remote_platform,
                        'version' => $link->remote_version,
                        'links' => [],
                    ];

                    $nodes[] = &$neighborNodes[$toId];
                }

                $neighborNodes[$toId]['links'][] = [
                    'local_device' => $link->device?->hostname ?: '-',
                    'local_port' => $localPortName ?: '-',
                    'remote_port' => $remotePortName ?: '-',
                    'protocol' => strtoupper($link->protocol ?: 'xdp'),
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | DEDUPLICATE REVERSED LINK ROWS
            |--------------------------------------------------------------------------
            |
            | LibreNMS stores one row per direction. Devices on both ends of
            | the same physical link produce two rows, so matching pairs are
            | collapsed into one edge.
            |
            */

            $fingerprint = $this->pairKey(
                $fromId,
                $localPortName,
                $toId,
                $remotePortName
            );

            $protocol = strtoupper($link->protocol ?: 'xdp');

            if (isset($edgeIndex[$fingerprint])) {
                $key = $edgeIndex[$fingerprint];

                if (! in_array($protocol, $edges[$key]['protocols'])) {
                    $edges[$key]['protocols'][] = $protocol;
                }

                $edges[$key]['protocol'] = implode(' / ', $edges[$key]['protocols']);
                $edges[$key]['label'] = $this->edgeLabel(
                    $localPortName,
                    $remotePortName,
                    $edges[$key]['protocol']
                );

                continue;
            }

            $edgeIndex[$fingerprint] = count($edges);

            $edges[] = [
                'id' => 'e' . $link->id,
                'from' => $fromId,
                'to' => $toId,
                'label' => $this->edgeLabel($localPortName, $remotePortName, $protocol),
                'protocol' => $protocol,
                'protocols' => [$protocol],
                'details' => $this->linkDetails($link, $localPortName, $remotePortName),
            ];
        }

        return response()->json($this->payload(
            $site,
            $nodes,
            $edges,
            [
                'site_devices' => $managed->count(),
                'managed' => $devices->count() - $managed->count(),
                'neighbors' => count($neighborNodes),
                'links' => count($edges),
            ]
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | PAYLOAD STRUCTURE
    |--------------------------------------------------------------------------
    */

    private function payload(
        NmsSite $site,
        array $nodes,
        array $edges,
        array $summary
    ): array {
        return [
            'site' => [
                'id' => $site->id,
                'name' => $site->name,
            ],
            'nodes' => $nodes,
            'edges' => $edges,
            'summary' => $summary,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MANAGED DEVICE NODE (NATIVE LIBRENMS DEVICE DATA)
    |--------------------------------------------------------------------------
    */

    private function deviceNode(
        Device $device,
        string $kind,
        ?string $siteName
    ): array {
        return [
            'id' => 'd' . $device->device_id,
            'kind' => $kind,
            'state' => $device->disabled ? 'Down' : ($device->status ? 'Up' : 'Down'),
            'label' => $device->hostname,
            'device_id' => $device->device_id,
            'hostname' => $device->hostname,
            'sys_name' => $device->sysName,
            'os' => $device->os,
            'os_version' => $device->version,
            'hardware' => $device->hardware,
            'mgmt_ip' => $device->ip ?: $device->hostname,
            'site_name' => $siteName,
            'device_url' => route('device', ['device' => $device->device_id, 'tab' => 'overview']),
            'ports_url' => route('device', ['device' => $device->device_id, 'tab' => 'ports']),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | LINK DETAILS (ONLY FIELDS LIBRENMS ACTUALLY HAS)
    |--------------------------------------------------------------------------
    */

    private function linkDetails(
        Link $link,
        ?string $localPortName,
        ?string $remotePortName
    ): array {
        $localPort = $link->port;
        $remotePort = $link->remotePort;

        /*
        |--------------------------------------------------------------------------
        | LIBRENMS PORT DATA: STATUS / SPEED / RX / TX
        |--------------------------------------------------------------------------
        |
        | All values come from the native LibreNMS ports table maintained by
        | the normal poller (ifInOctets_rate / ifOutOctets_rate are bytes per
        | second). No SNMP is executed from this module.
        |
        */

        return [
            'link_id' => $link->id,
            'protocol' => strtoupper($link->protocol ?: 'xdp'),

            'local_device' => $link->device?->hostname ?: '-',
            'local_interface' => $localPortName ?: '-',
            'local_port_descr' => $localPort?->ifDescr ?: $localPort?->ifAlias,

            'remote_device' => ((int) $link->remote_device_id > 0
                ? ($link->remoteDevice?->hostname ?: $link->remote_hostname)
                : $link->remote_hostname) ?: '-',
            'remote_resolved' => (int) $link->remote_device_id > 0,
            'remote_interface' => $remotePortName ?: '-',
            'remote_port_descr' => $remotePort?->ifDescr ?: $remotePort?->ifAlias,

            'local_port_status' => $this->portStatus($localPort),
            'local_port_speed' => $this->speed($localPort?->ifSpeed),
            'local_port_rx' => $this->rate($localPort?->ifInOctets_rate),
            'local_port_tx' => $this->rate($localPort?->ifOutOctets_rate),

            'remote_port_status' => $this->portStatus($remotePort),
        ];
    }

    private function portStatus(?Port $port): ?string
    {
        if (! $port) {
            return null;
        }

        return $this->statusLabel($port->ifAdminStatus?->value ?: $port->ifOperStatus?->value);
    }

    private function statusLabel(?string $value): ?string
    {
        return match ($value) {
            'up' => 'Up',
            'down' => 'Down',
            'testing' => 'Testing',
            'dormant' => 'Dormant',
            'notPresent', 'notpresent' => 'Not Present',
            'lowerLayerDown' => 'Lower Layer Down',
            default => $value ? ucfirst($value) : null,
        };
    }

private function speed($bits): ?string
    {
        return $bits === null ? null : Number::formatSi($bits, 2, 0, 'bps');
    }

    private function rate($bytesPerSecond): ?string
    {
        return $bytesPerSecond === null ? null : Number::formatBi($bytesPerSecond, 2, 0, 'ps');
    }

    /*
    |--------------------------------------------------------------------------
    | EDGE HELPERS
    |--------------------------------------------------------------------------
    */

    private function pairKey(
        ?string $fromId,
        ?string $fromPort,
        ?string $toId,
        ?string $toPort
    ): string {
        $a = ($fromId ?: '-') . ':' . ($fromPort ?: '-');
        $b = ($toId ?: '-') . ':' . ($toPort ?: '-');

        return $a <= $b ? $a . '|' . $b : $b . '|' . $a;
    }

    private function edgeLabel(
        ?string $localPort,
        ?string $remotePort,
        string $protocol
    ): string {
        return ($localPort ?: '-') . ' ↔ ' . ($remotePort ?: '-') . "\n" . $protocol;
    }
}
