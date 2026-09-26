<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\NmsSite;
use App\Models\NmsSiteDevice;
use Illuminate\Http\Request;

class NmsSiteDeviceController extends Controller
{
    public function index(NmsSite $site)
    {
        $assignedDevices = NmsSiteDevice::with(['device', 'team'])
            ->where('site_id', $site->id)
            ->orderBy('id')
            ->get();

        return view(
            'sites.devices.index',
            compact('site', 'assignedDevices')
        );
    }

    public function create(NmsSite $site)
    {
        $assignedDeviceIds = NmsSiteDevice::where('site_id', $site->id)
            ->pluck('device_id');

        $devices = Device::whereNotIn('device_id', $assignedDeviceIds)
            ->orderBy('hostname')
            ->get();

        $teams = $site->teams()
            ->orderBy('name')
            ->get();

        return view(
            'sites.devices.create',
            compact('site', 'devices', 'teams')
        );
    }

    public function store(Request $request, NmsSite $site)
    {
        $validated = $request->validate([
            'device_id' => 'required|integer|exists:devices,device_id',
            'team_id' => 'nullable|integer|exists:nms_teams,id',
            'notes' => 'nullable|string',
        ]);

        if (! empty($validated['team_id'])) {
            $teamBelongsToSite = $site->teams()
                ->where('id', $validated['team_id'])
                ->exists();

            abort_unless($teamBelongsToSite, 422);
        }

        NmsSiteDevice::firstOrCreate(
            [
                'site_id' => $site->id,
                'device_id' => $validated['device_id'],
            ],
            [
                'team_id' => $validated['team_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()
            ->route('sites.devices.index', $site)
            ->with('success', 'Device assigned to site successfully.');
    }

    public function destroy(NmsSite $site, NmsSiteDevice $siteDevice)
    {
        abort_unless($siteDevice->site_id === $site->id, 404);

        $siteDevice->delete();

        return redirect()
            ->route('sites.devices.index', $site)
            ->with('success', 'Device removed from site successfully.');
    }

    public function discover(NmsSite $site)
{
    $teams = $site->teams()
        ->orderBy('name')
        ->get();

    return view(
        'sites.devices.discover',
        compact('site', 'teams')
    );
}

public function discoverStore(Request $request, NmsSite $site)
{
    $validated = $request->validate([
        'hostname' => 'required|string|max:255',
        'community' => 'required|string|max:255',
        'team_id' => 'nullable|integer|exists:nms_teams,id',
        'notes' => 'nullable|string',
    ]);

    if (! empty($validated['team_id'])) {
        $teamBelongsToSite = $site->teams()
            ->where('id', $validated['team_id'])
            ->exists();

        abort_unless($teamBelongsToSite, 422);
    }

    $hostname = trim($validated['hostname']);

    /*
     * Check if device already exists in LibreNMS
     */
    $device = Device::where('hostname', $hostname)->first();

    /*
     * If not existing, add using LibreNMS CLI
     */
    if (! $device) {
        $command = sprintf(
            'cd /opt/librenms && ./lnms device:add --v2c -c %s %s',
            escapeshellarg($validated['community']),
            escapeshellarg($hostname)
        );

        exec($command . ' 2>&1', $output, $exitCode);

        if ($exitCode !== 0) {
            return back()
                ->withInput()
                ->withErrors([
                    'hostname' => implode("\n", $output),
                ]);
        }

        /*
         * Reload device from database
         */
        $device = Device::where('hostname', $hostname)->first();
    }

    if (! $device) {
        return back()
            ->withInput()
            ->withErrors([
                'hostname' => 'Device was added but could not be found in LibreNMS database.',
            ]);
    }

    /*
     * Assign device to this NMS-WHITE site
     */
    NmsSiteDevice::updateOrCreate(
        [
            'site_id' => $site->id,
            'device_id' => $device->device_id,
        ],
        [
            'team_id' => $validated['team_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]
    );

    /*
     * Trigger LibreNMS discovery
     */
    $discoverCommand = sprintf(
        'cd /opt/librenms && ./lnms device:discover %s',
        escapeshellarg($hostname)
    );

    exec($discoverCommand . ' 2>&1');

    /*
     * Trigger LibreNMS poll
     */
    $pollCommand = sprintf(
        'cd /opt/librenms && ./lnms device:poll %s',
        escapeshellarg($hostname)
    );

    exec($pollCommand . ' 2>&1');

    return redirect()
        ->route('sites.devices.index', $site)
        ->with('success', 'Device discovered, added to LibreNMS and assigned to site successfully.');
}
}
