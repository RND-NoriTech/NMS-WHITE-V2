<?php

namespace App\Http\Controllers;

use App\Models\NmsSite;
use App\Models\NmsVehicle;
use Illuminate\Http\Request;

class NmsVehicleController extends Controller
{
    public function index(NmsSite $site)
    {
        $vehicles = NmsVehicle::with('team')
            ->where('site_id', $site->id)
            ->orderBy('name')
            ->get();

        return view(
            'sites.vehicles.index',
            compact('site', 'vehicles')
        );
    }

    public function create(NmsSite $site)
    {
        $teams = $site->teams()
            ->orderBy('name')
            ->get();

        return view(
            'sites.vehicles.create',
            compact('site', 'teams')
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

            'registration_number' => [
                'required',
                'string',
                'max:255',
                'unique:nms_vehicles,registration_number,NULL,id,site_id,' . $site->id,
            ],

            'vehicle_type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'make' => [
                'nullable',
                'string',
                'max:255',
            ],

            'model' => [
                'nullable',
                'string',
                'max:255',
            ],

            'year' => [
                'nullable',
                'integer',
                'min:1900',
                'max:2100',
            ],

            'color' => [
                'nullable',
                'string',
                'max:255',
            ],

            'driver_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'driver_phone' => [
                'nullable',
                'string',
                'max:255',
            ],

            'tracker_device_id' => [
                'nullable',
                'string',
                'max:255',
            ],

            'tracker_imei' => [
                'nullable',
                'string',
                'max:255',
            ],

            'tracker_mac_address' => [
                'nullable',
                'string',
                'max:32',
            ],

            'tracker_ip_address' => [
                'nullable',
                'string',
                'max:45',
            ],

            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],

            'last_seen_at' => [
                'nullable',
                'date',
            ],

            'team_id' => [
                'nullable',
                'integer',
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
        | NORMALIZE TRACKER MAC ADDRESS
        |--------------------------------------------------------------------------
        */

        if (! empty($validated['tracker_mac_address'])) {
            $validated['tracker_mac_address'] = strtoupper(
                str_replace(
                    '-',
                    ':',
                    trim($validated['tracker_mac_address'])
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
        | CREATE VEHICLE
        |--------------------------------------------------------------------------
        */

        NmsVehicle::create($validated);

        return redirect()
            ->route('sites.vehicles.index', $site)
            ->with(
                'success',
                'Vehicle added successfully.'
            );
    }

    public function show(
        NmsSite $site,
        NmsVehicle $vehicle
    ) {
        /*
        |--------------------------------------------------------------------------
        | MAKE SURE VEHICLE BELONGS TO SITE
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $vehicle->site_id === $site->id,
            404
        );

        return view(
            'sites.vehicles.show',
            compact('site', 'vehicle')
        );
    }

    public function edit(
        NmsSite $site,
        NmsVehicle $vehicle
    ) {
        /*
        |--------------------------------------------------------------------------
        | MAKE SURE VEHICLE BELONGS TO SITE
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $vehicle->site_id === $site->id,
            404
        );

        $teams = $site->teams()
            ->orderBy('name')
            ->get();

        return view(
            'sites.vehicles.edit',
            compact(
                'site',
                'vehicle',
                'teams'
            )
        );
    }

    public function update(
        Request $request,
        NmsSite $site,
        NmsVehicle $vehicle
    ) {
        /*
        |--------------------------------------------------------------------------
        | MAKE SURE VEHICLE BELONGS TO SITE
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $vehicle->site_id === $site->id,
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

            'registration_number' => [
                'required',
                'string',
                'max:255',
                'unique:nms_vehicles,registration_number,'
                    . $vehicle->id
                    . ',id,site_id,'
                    . $site->id,
            ],

            'vehicle_type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'make' => [
                'nullable',
                'string',
                'max:255',
            ],

            'model' => [
                'nullable',
                'string',
                'max:255',
            ],

            'year' => [
                'nullable',
                'integer',
                'min:1900',
                'max:2100',
            ],

            'color' => [
                'nullable',
                'string',
                'max:255',
            ],

            'driver_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'driver_phone' => [
                'nullable',
                'string',
                'max:255',
            ],

            'tracker_device_id' => [
                'nullable',
                'string',
                'max:255',
            ],

            'tracker_imei' => [
                'nullable',
                'string',
                'max:255',
            ],

            'tracker_mac_address' => [
                'nullable',
                'string',
                'max:32',
            ],

            'tracker_ip_address' => [
                'nullable',
                'string',
                'max:45',
            ],

            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],

            'last_seen_at' => [
                'nullable',
                'date',
            ],

            'team_id' => [
                'nullable',
                'integer',
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
        | NORMALIZE TRACKER MAC ADDRESS
        |--------------------------------------------------------------------------
        */

        if (! empty($validated['tracker_mac_address'])) {
            $validated['tracker_mac_address'] = strtoupper(
                str_replace(
                    '-',
                    ':',
                    trim($validated['tracker_mac_address'])
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE VEHICLE
        |--------------------------------------------------------------------------
        */

        $vehicle->update($validated);

        return redirect()
            ->route('sites.vehicles.index', $site)
            ->with(
                'success',
                'Vehicle updated successfully.'
            );
    }

    public function destroy(
        NmsSite $site,
        NmsVehicle $vehicle
    ) {
        /*
        |--------------------------------------------------------------------------
        | MAKE SURE VEHICLE BELONGS TO SITE
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $vehicle->site_id === $site->id,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | DELETE VEHICLE
        |--------------------------------------------------------------------------
        */

        $vehicle->delete();

        return redirect()
            ->route('sites.vehicles.index', $site)
            ->with(
                'success',
                'Vehicle deleted successfully.'
            );
    }
}
