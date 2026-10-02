<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\NmsSite;
use App\Models\NmsSiteDevice;
use App\Models\NmsWifiConnection;
use App\Models\Port;
use App\Services\WifiProviders\WifiProviderManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class NmsWifiConnectionController extends Controller
{
    public function index(NmsSite $site)
    {
        $wifi = NmsWifiConnection::with(['team', 'device', 'port'])
            ->where('site_id', $site->id)
            ->orderBy('name')
            ->get();

        return view(
            'sites.wifi.index',
            compact('site', 'wifi')
        );
    }

    public function create(NmsSite $site)
    {
        $teams = $site->teams()
            ->orderBy('name')
            ->get();

        $managedDevices = $this->siteManagedDevices($site);

        $devicePorts = collect();

        return view(
            'sites.wifi.create',
            compact('site', 'teams', 'managedDevices', 'devicePorts')
        );
    }

    public function store(
        Request $request,
        NmsSite $site
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'provider' => [
                'nullable',
                'string',
                'max:255',
                'in:mikrotik,ruckus,omada,other',
            ],

            'ssid' => [
                'nullable',
                'string',
                'max:255',
            ],

            'device_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'model' => [
                'nullable',
                'string',
                'max:255',
            ],

            'device_id' => [
                'nullable',
                'string',
                'max:255',
            ],

            'mac_address' => [
                'nullable',
                'string',
                'max:32',
            ],

            'ip_address' => [
                'nullable',
                'string',
                'max:45',
            ],

            'gateway' => [
                'nullable',
                'string',
                'max:255',
            ],

            'librenms_device_id' => [
                'nullable',
                'integer',
                'exists:devices,device_id',
            ],

            'librenms_port_id' => [
                'nullable',
                'integer',
                'exists:ports,port_id',
            ],

            'api_provider' => [
                'nullable',
                'string',
                'max:255',
                'in:omada,ruckus,other',
            ],

            'external_device_id' => [
                'nullable',
                'string',
                'max:255',
            ],

            'username' => [
                'nullable',
                'string',
                'max:255',
            ],

            'password' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'team_id' => [
                'nullable',
                'integer',
            ],

            'monitoring_method' => [
                'required',
                'in:snmp,api,manual',
            ],

            'status' => [
                'required',
                'in:active,inactive,maintenance,offline',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | VERIFY TEAM BELONGS TO THIS SITE
        |--------------------------------------------------------------------------
        */

        if (! empty($validated['team_id'])) {
            abort_unless(
                $site->teams()
                    ->where('id', $validated['team_id'])
                    ->exists(),
                422
            );
        }

        /*
        |--------------------------------------------------------------------------
        | APPLY MONITORING MODE RULES
        |--------------------------------------------------------------------------
        |
        | SNMP / LibreNMS:
        |   - librenms_device_id required
        |   - device MUST already belong to this site via nms_site_devices
        |   - no SNMP community/version handling here, LibreNMS already manages it
        |
        | API:
        |   - api_provider required
        |   - no LibreNMS device
        |
        | Manual:
        |   - no LibreNMS device, no API provider
        |
        */

        $this->applyMonitoringModeRules($validated, $site->id);

        /*
        |--------------------------------------------------------------------------
        | NORMALIZE MAC ADDRESS
        |--------------------------------------------------------------------------
        */

        if (! empty($validated['mac_address'])) {
            $validated['mac_address'] = strtoupper(
                str_replace(
                    '-',
                    ':',
                    trim($validated['mac_address'])
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ASSIGN SITE
        |--------------------------------------------------------------------------
        */

        $validated['site_id'] = $site->id;

        /*
        |--------------------------------------------------------------------------
        | CREATE WIFI CONNECTION
        |--------------------------------------------------------------------------
        */

        NmsWifiConnection::create($validated);

        return redirect()
            ->route('sites.wifi.index', $site)
            ->with(
                'success',
                'WiFi connection added successfully.'
            );
    }

    public function edit(
        NmsSite $site,
        NmsWifiConnection $wifi
    ) {
        /*
        |--------------------------------------------------------------------------
        | MAKE SURE WIFI CONNECTION BELONGS TO SITE
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $wifi->site_id === $site->id,
            404
        );

        $teams = $site->teams()
            ->orderBy('name')
            ->get();

        $managedDevices = $this->siteManagedDevices($site);

        /*
        |--------------------------------------------------------------------------
        | PORTS OF THE CURRENTLY LINKED DEVICE (FOR INTERFACE DROPDOWN)
        |--------------------------------------------------------------------------
        */

        $devicePorts = collect();

        if (! empty($wifi->librenms_device_id)) {
            $devicePorts = $this->deviceWirelessPorts($wifi->librenms_device_id);
        }

        /*
        |--------------------------------------------------------------------------
        | KEEP CURRENT LINKED DEVICE SELECTABLE IF IT WAS UNASSIGNED FROM SITE
        |--------------------------------------------------------------------------
        */

        if (
            ! empty($wifi->librenms_device_id)
            && ! $managedDevices->contains('device_id', $wifi->librenms_device_id)
            && $wifi->device
        ) {
            $managedDevices->push($wifi->device);
            $managedDevices = $managedDevices
                ->sortBy('hostname')
                ->values();
        }

        return view(
            'sites.wifi.edit',
            compact(
                'site',
                'wifi',
                'teams',
                'managedDevices',
                'devicePorts'
            )
        );
    }

    public function update(
        Request $request,
        NmsSite $site,
        NmsWifiConnection $wifi
    ) {
        /*
        |--------------------------------------------------------------------------
        | MAKE SURE WIFI CONNECTION BELONGS TO SITE
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $wifi->site_id === $site->id,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'provider' => [
                'nullable',
                'string',
                'max:255',
                'in:mikrotik,ruckus,omada,other',
            ],

            'ssid' => [
                'nullable',
                'string',
                'max:255',
            ],

            'device_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'model' => [
                'nullable',
                'string',
                'max:255',
            ],

            'device_id' => [
                'nullable',
                'string',
                'max:255',
            ],

            'mac_address' => [
                'nullable',
                'string',
                'max:32',
            ],

            'ip_address' => [
                'nullable',
                'string',
                'max:45',
            ],

            'gateway' => [
                'nullable',
                'string',
                'max:255',
            ],

            'librenms_device_id' => [
                'nullable',
                'integer',
                'exists:devices,device_id',
            ],

            'librenms_port_id' => [
                'nullable',
                'integer',
                'exists:ports,port_id',
            ],

            'api_provider' => [
                'nullable',
                'string',
                'max:255',
                'in:omada,ruckus,other',
            ],

            'external_device_id' => [
                'nullable',
                'string',
                'max:255',
            ],

            'username' => [
                'nullable',
                'string',
                'max:255',
            ],

            'password' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'team_id' => [
                'nullable',
                'integer',
            ],

            'monitoring_method' => [
                'required',
                'in:snmp,api,manual',
            ],

            'status' => [
                'required',
                'in:active,inactive,maintenance,offline',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | VERIFY TEAM BELONGS TO SITE
        |--------------------------------------------------------------------------
        */

        if (! empty($validated['team_id'])) {
            abort_unless(
                $site->teams()
                    ->where('id', $validated['team_id'])
                    ->exists(),
                422
            );
        }

        /*
        |--------------------------------------------------------------------------
        | APPLY MONITORING MODE RULES
        |--------------------------------------------------------------------------
        */

        $this->applyMonitoringModeRules($validated, $site->id);

        /*
        |--------------------------------------------------------------------------
        | NORMALIZE MAC ADDRESS
        |--------------------------------------------------------------------------
        */

        if (! empty($validated['mac_address'])) {
            $validated['mac_address'] = strtoupper(
                str_replace(
                    '-',
                    ':',
                    trim($validated['mac_address'])
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | KEEP OLD PASSWORD IF NEW PASSWORD IS EMPTY
        |--------------------------------------------------------------------------
        */

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE WIFI CONNECTION
        |--------------------------------------------------------------------------
        */

        $wifi->update($validated);

        return redirect()
            ->route('sites.wifi.index', $site)
            ->with(
                'success',
                'WiFi connection updated successfully.'
            );
    }

    public function destroy(
        NmsSite $site,
        NmsWifiConnection $wifi
    ) {
        /*
        |--------------------------------------------------------------------------
        | MAKE SURE WIFI CONNECTION BELONGS TO SITE
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $wifi->site_id === $site->id,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | DELETE WIFI CONNECTION
        |--------------------------------------------------------------------------
        */

        $wifi->delete();

        return redirect()
            ->route('sites.wifi.index', $site)
            ->with(
                'success',
                'WiFi connection deleted successfully.'
            );
    }

    public function revealPassword(
        NmsSite $site,
        NmsWifiConnection $wifi
    ) {
        /*
        |--------------------------------------------------------------------------
        | MAKE SURE WIFI CONNECTION BELONGS TO SITE
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $wifi->site_id === $site->id,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | ADMIN ONLY
        |--------------------------------------------------------------------------
        */

        abort_unless(
            Gate::allows('admin'),
            403,
            'Only administrators can reveal WiFi passwords.'
        );

        /*
        |--------------------------------------------------------------------------
        | CHECK PASSWORD EXISTS
        |--------------------------------------------------------------------------
        */

        if (empty($wifi->password)) {
            return response()->json([
                'success' => false,
                'message' => 'No password stored for this WiFi connection.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | PASSWORD IS AUTOMATICALLY DECRYPTED
        |--------------------------------------------------------------------------
        |
        | Because NmsWifiConnection model contains:
        |
        | protected $casts = [
        |     'password' => 'encrypted',
        | ];
        |
        */
        return response()->json([
            'success' => true,
            'password' => $wifi->password,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SYNC WIFI METADATA (PROVIDER ENRICHMENT)
    |--------------------------------------------------------------------------
    |
    | Provider APIs are only used for SSID / wireless mode / radio
    | metadata. LibreNMS keeps being the source of truth for device
    | status, interface status, traffic and ports.
    |
    | If a provider API is unavailable the WiFi record never fails:
    | SNMP status/traffic are kept, the manually entered SSID is
    | preserved and a sync status like "API not configured" is stored.
    |
    | Secrets (password, passphrase, PSK, secret) are never requested,
    | returned, stored or logged.
    |
    */

    public function sync(
        Request $request,
        NmsSite $site,
        NmsWifiConnection $wifi
    ) {
        /*
        |--------------------------------------------------------------------------
        | MAKE SURE WIFI CONNECTION BELONGS TO SITE
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $wifi->site_id === $site->id,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | MANUAL MODE NEEDS NO PROVIDER SYNC
        |--------------------------------------------------------------------------
        */

        if ($wifi->monitoring_method === 'manual') {
            return redirect()
                ->route('sites.wifi.index', $site)
                ->with(
                    'success',
                    'Manual mode: no provider sync required.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | RESOLVE PROVIDER (PER RECORD, SITES MAY MIX PROVIDERS)
        |--------------------------------------------------------------------------
        */

        $provider = (new WifiProviderManager())->for($wifi);

        if (! $provider) {
            $this->storeSyncResult($wifi, 'unsupported', 'No provider integration for this record.');

            return redirect()
                ->route('sites.wifi.index', $site)
                ->with(
                    'success',
                    'No provider integration for this record. SNMP / LibreNMS monitoring stays active.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | RUN PROVIDER SYNC, NEVER FAIL THE RECORD
        |--------------------------------------------------------------------------
        */

        try {
            $result = $provider->sync($wifi);
        } catch (\Throwable $e) {
            $result = [
                'status' => 'error',
                'message' => 'Provider sync failed: unexpected error.',
                'metadata' => [],
            ];
        }

        $status = $result['status'] ?? 'error';
        $message = $result['message'] ?? '';

        /*
        |--------------------------------------------------------------------------
        | APPLY NORMALIZED METADATA (WHITELISTED KEYS ONLY)
        |--------------------------------------------------------------------------
        */

        $this->storeSyncResult(
            $wifi,
            $status,
            $message,
            $result['metadata'] ?? []
        );

        /*
        |--------------------------------------------------------------------------
        | FLASH MESSAGE BY RESULT
        |--------------------------------------------------------------------------
        */

        $flash = [
            'synced' => 'WiFi metadata synced successfully.',
            'not_configured' => 'API not configured. SNMP / LibreNMS monitoring stays active.',
            'unsupported' => $message ?: 'Provider sync not supported for this record.',
            'error' => $message ?: 'Provider sync failed.',
        ];

        return redirect()
            ->route('sites.wifi.index', $site)
            ->with(
                $status === 'synced' ? 'success' : 'warning',
                $flash[$status] ?? 'Provider sync finished.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | PERSIST SYNC RESULT (SAFE METADATA KEYS ONLY)
    |--------------------------------------------------------------------------
    |
    | Only normalized, non-secret keys are applied. Existing manually
    | entered values are preserved unless the provider supplied fresh
    | data. SNMP status/traffic are never copied here.
    |
    */

    private function storeSyncResult(
        NmsWifiConnection $wifi,
        string $status,
        string $message,
        array $metadata = []
    ): void {
        $update = [
            'metadata_synced_at' => now(),
            'metadata_sync_status' => $status,
            'metadata_sync_message' => $message,
        ];

        $allowed = [
            'ssid',
            'wireless_mode',
            'interface_name',
            'mac_address',
            'radio_mac_address',
            'master_interface',
            'country',
            'running',
            'disabled',
        ];

        $metadata = array_intersect_key($metadata, array_flip($allowed));

        if (! empty($metadata['ssid'])) {
            $update['ssid'] = $metadata['ssid'];
        }

        if (! empty($metadata['wireless_mode'])) {
            $update['wireless_mode'] = $metadata['wireless_mode'];
        }

        if (! empty($metadata['radio_mac_address'])) {
            $update['radio_mac_address'] = $metadata['radio_mac_address'];
        }

        if (! empty($metadata['master_interface'])) {
            $update['master_interface'] = $metadata['master_interface'];
        }

        if (! empty($metadata['mac_address']) && empty($wifi->mac_address)) {
            $update['mac_address'] = $metadata['mac_address'];
        }

        $wifi->update($update);
    }

    /*
    |--------------------------------------------------------------------------
    | LIBRENMS DEVICES ALREADY ASSIGNED TO THIS SITE
    |--------------------------------------------------------------------------
    |
    | Reuses the existing nms_site_devices mapping. No SNMP discovery or
    | polling logic is implemented here, LibreNMS Device stays the source
    | of truth for status, OS, hardware and version.
    |
    */

    private function siteManagedDevices(NmsSite $site)
    {
        return Device::whereIn(
                'device_id',
                NmsSiteDevice::where('site_id', $site->id)
                    ->pluck('device_id')
            )
            ->orderBy('hostname')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | MONITORING MODE RULES
    |--------------------------------------------------------------------------
    |
    | Clears cross-mode fields when the mode changes:
    |   - switching away from SNMP clears the LibreNMS device link
    |   - switching away from API clears provider and external device id
    |   - manual mode uses stored inventory fields only
    |
    */

    private function applyMonitoringModeRules(array &$validated, int $siteId): void
    {
        if ($validated['monitoring_method'] === 'snmp') {
            abort_unless(
                ! empty($validated['librenms_device_id']),
                422,
                'A managed LibreNMS device is required for SNMP / LibreNMS monitoring.'
            );

            abort_unless(
                ! empty($validated['librenms_port_id']),
                422,
                'A wireless interface is required for SNMP / LibreNMS monitoring.'
            );

            abort_unless(
                NmsSiteDevice::where('site_id', $siteId)
                    ->where('device_id', $validated['librenms_device_id'])
                    ->exists(),
                422,
                'The selected LibreNMS device is not assigned to this site.'
            );

            abort_unless(
                Port::where('port_id', $validated['librenms_port_id'])
                    ->where('device_id', $validated['librenms_device_id'])
                    ->exists(),
                422,
                'The selected interface does not belong to the selected LibreNMS device.'
            );

            $validated['api_provider'] = null;
            $validated['external_device_id'] = null;

            return;
        }

        if ($validated['monitoring_method'] === 'api') {
            abort_unless(
                ! empty($validated['api_provider']),
                422,
                'An API provider is required for API monitoring.'
            );

            $validated['librenms_device_id'] = null;
            $validated['librenms_port_id'] = null;

            return;
        }

        $validated['librenms_device_id'] = null;
        $validated['librenms_port_id'] = null;
        $validated['api_provider'] = null;
        $validated['external_device_id'] = null;
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX: PORTS OF A SITE MANAGED LIBRENMS DEVICE
    |--------------------------------------------------------------------------
    |
    | Used to populate the Wireless Interface dropdown when the Managed
    | Device changes. Reuses LibreNMS Port data, no SNMP is executed here.
    |
    */

    public function devicePorts(
        NmsSite $site,
        Device $device
    ) {
        /*
        |--------------------------------------------------------------------------
        | DEVICE MUST BE ASSIGNED TO THIS SITE
        |--------------------------------------------------------------------------
        */

        abort_unless(
            NmsSiteDevice::where('site_id', $site->id)
                ->where('device_id', $device->device_id)
                ->exists(),
            404
        );

        /*
        |--------------------------------------------------------------------------
        | WIRELESS LOOKING PORTS FIRST, FALLBACK TO ALL PORTS
        |--------------------------------------------------------------------------
        |
        | The filter is deliberately wide (ifName/ifDescr/ifAlias) so known
        | MikroTik interfaces such as wifi1 / wifi2 always appear. If a device
        | has no wireless-looking port at all, all non-deleted ports are
        | returned so the interface can still be selected.
        |
        */

        $ports = $this->deviceWirelessPorts($device->device_id);

        if ($ports->isEmpty()) {
            $ports = Port::where('device_id', $device->device_id)
                ->where('deleted', 0)
                ->orderBy('ifName')
                ->get();
        }

        return response()->json([
            'ports' => $ports->map(function (Port $port) {
                return [
                    'port_id' => $port->port_id,
                    'ifName' => $port->ifName,
                    'ifDescr' => $port->ifDescr,
                    'ifAlias' => $port->ifAlias,
                    'ifOperStatus' => $port->ifOperStatus?->value,
                    'ifAdminStatus' => $port->ifAdminStatus?->value,
                    'ifSpeed' => $port->ifSpeed,
                    'ifType' => $port->ifType,
                    'ifPhysAddress' => $port->ifPhysAddress,
                ];
            }),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | IMPORT WIFI INTERFACES (SELECTION PAGE)
    |--------------------------------------------------------------------------
    |
    | Scans all site-assigned LibreNMS devices for wireless-looking ports
    | and lets the user confirm which ones become WiFi records. Nothing
    | is created without user confirmation.
    |
    */

    public function importIndex(NmsSite $site)
    {
        $devices = $this->siteManagedDevices($site);

        /*
        |--------------------------------------------------------------------------
        | PORTS ALREADY LINKED TO WIFI RECORDS
        |--------------------------------------------------------------------------
        */

        $linkedPortIds = NmsWifiConnection::whereNotNull('librenms_port_id')
            ->pluck('librenms_port_id');

        $candidates = collect();

        foreach ($devices as $device) {
            $ports = $this->deviceWirelessPorts($device->device_id)
                ->whereNotIn('port_id', $linkedPortIds);

            if ($ports->isNotEmpty()) {
                $candidates->push([
                    'device' => $device,
                    'ports' => $ports,
                ]);
            }
        }

        return view(
            'sites.wifi.import',
            compact('site', 'candidates')
        );
    }

    public function importStore(
        Request $request,
        NmsSite $site
    ) {
        $validated = $request->validate([
            'ports' => [
                'required',
                'array',
            ],

            'ports.*' => [
                'string',
            ],
        ]);

        $imported = 0;

        foreach ($validated['ports'] as $entry) {
            /*
            |--------------------------------------------------------------------------
            | PARSE DEVICE:PORT SELECTION
            |--------------------------------------------------------------------------
            */

            if (! str_contains($entry, ':')) {
                continue;
            }

            [$deviceId, $portId] = explode(':', $entry, 2);

            if (
                ! ctype_digit($deviceId)
                || ! ctype_digit($portId)
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | DEVICE MUST BELONG TO THIS SITE
            |--------------------------------------------------------------------------
            */

            $device = Device::whereIn(
                'device_id',
                NmsSiteDevice::where('site_id', $site->id)
                    ->pluck('device_id')
            )
                ->where('device_id', $deviceId)
                ->first();

            if (! $device) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | PORT MUST BELONG TO THE DEVICE AND NOT BE LINKED YET
            |--------------------------------------------------------------------------
            */

            $port = Port::where('port_id', $portId)
                ->where('device_id', $device->device_id)
                ->first();

            if (! $port) {
                continue;
            }

            $alreadyLinked = NmsWifiConnection::where('librenms_port_id', $port->port_id)
                ->exists();

            if ($alreadyLinked) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | CREATE WIFI RECORD FROM LIBRENMS PORT
            |--------------------------------------------------------------------------
            |
            | monitoring_method = snmp, status stays a stored default, real
            | status is always derived from LibreNMS at display time.
            |
            */

            NmsWifiConnection::create([
                'site_id' => $site->id,
                'name' => $device->hostname . ' ' . ($port->ifName ?: $port->port_id),
                'monitoring_method' => 'snmp',
                'librenms_device_id' => $device->device_id,
                'librenms_port_id' => $port->port_id,
                'status' => 'active',
            ]);

            $imported++;
        }

        return redirect()
            ->route('sites.wifi.index', $site)
            ->with(
                'success',
                $imported > 0
                    ? $imported . ' WiFi interface(s) imported successfully.'
                    : 'No new WiFi interfaces were imported.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | WIRELESS LOOKING PORTS OF A LIBRENMS DEVICE
    |--------------------------------------------------------------------------
    |
    | LibreNMS Port data is reused as-is. Comparisons are case-insensitive
    | (utf8mb4_unicode_ci) so MikroTik wifi1 / wifi2 always match.
    |
    */

    private function deviceWirelessPorts(int $deviceId)
    {
        return Port::where('device_id', $deviceId)
            ->where('deleted', 0)
            ->where(function ($query) {
                $query->where('ifName', 'like', '%wifi%')
                    ->orWhere('ifName', 'like', '%wlan%')
                    ->orWhere('ifDescr', 'like', '%wifi%')
                    ->orWhere('ifDescr', 'like', '%wlan%')
                    ->orWhere('ifAlias', 'like', '%wireless%');
            })
            ->orderBy('ifName')
            ->get();
    }
}
