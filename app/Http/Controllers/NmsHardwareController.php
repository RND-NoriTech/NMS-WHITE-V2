<?php

namespace App\Http\Controllers;

use App\Models\NmsHardware;
use App\Models\NmsSite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\NmsHardwareAudit;

class NmsHardwareController extends Controller
{
    public function index(NmsSite $site)
    {
        $hardware = NmsHardware::with('team')
            ->where('site_id', $site->id)
            ->orderBy('model')
            ->get();

        return view(
            'sites.hardware.index',
            compact('site', 'hardware')
        );
    }

    public function create(NmsSite $site)
    {
        $teams = $site->teams()
            ->orderBy('name')
            ->get();

        return view(
            'sites.hardware.create',
            compact('site', 'teams')
        );
    }

    public function store(
        Request $request,
        NmsSite $site
    ) {
        $validated = $request->validate([
            'model' => [
                'required',
                'string',
                'max:255',
            ],

            'device_id' => [
                'required',
                'string',
                'max:255',
                'unique:nms_hardware,device_id,NULL,id,site_id,' . $site->id,
            ],

            'gateway' => [
                'nullable',
                'string',
                'max:255',
            ],

            'mac_address' => [
                'nullable',
                'string',
                'max:32',
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

            'status' => [
                'required',
                'in:active,inactive,maintenance',
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
        | CREATE HARDWARE
        |--------------------------------------------------------------------------
        */

        NmsHardware::create($validated);

        return redirect()
            ->route('sites.hardware.index', $site)
            ->with(
                'success',
                'Hardware added successfully.'
            );
    }

    public function edit(
        NmsSite $site,
        NmsHardware $hardware
    ) {
        /*
        |--------------------------------------------------------------------------
        | MAKE SURE HARDWARE BELONGS TO SITE
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $hardware->site_id === $site->id,
            404
        );

        $teams = $site->teams()
            ->orderBy('name')
            ->get();

        return view(
            'sites.hardware.edit',
            compact(
                'site',
                'hardware',
                'teams'
            )
        );
    }

    public function update(
        Request $request,
        NmsSite $site,
        NmsHardware $hardware
    ) {
        /*
        |--------------------------------------------------------------------------
        | MAKE SURE HARDWARE BELONGS TO SITE
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $hardware->site_id === $site->id,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'model' => [
                'required',
                'string',
                'max:255',
            ],

            'device_id' => [
                'required',
                'string',
                'max:255',
                'unique:nms_hardware,device_id,'
                    . $hardware->id
                    . ',id,site_id,'
                    . $site->id,
            ],

            'gateway' => [
                'nullable',
                'string',
                'max:255',
            ],

            'mac_address' => [
                'nullable',
                'string',
                'max:32',
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

            'status' => [
                'required',
                'in:active,inactive,maintenance',
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
        | UPDATE HARDWARE
        |--------------------------------------------------------------------------
        */

        $hardware->update($validated);

        return redirect()
            ->route('sites.hardware.index', $site)
            ->with(
                'success',
                'Hardware updated successfully.'
            );
    }

    public function destroy(
        NmsSite $site,
        NmsHardware $hardware
    ) {
        /*
        |--------------------------------------------------------------------------
        | MAKE SURE HARDWARE BELONGS TO SITE
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $hardware->site_id === $site->id,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | DELETE HARDWARE
        |--------------------------------------------------------------------------
        */

        $hardware->delete();

        return redirect()
            ->route('sites.hardware.index', $site)
            ->with(
                'success',
                'Hardware deleted successfully.'
            );
    }

    public function auditIndex(NmsSite $site)
    {
        abort_unless(
            Gate::allows('admin'),
            403,
            'Only administrators can view hardware audit logs.'
        );

        $audits = NmsHardwareAudit::with('hardware')
            ->where('site_id', $site->id)
            ->orderByDesc('id')
            ->paginate(50);

        return view(
            'sites.hardware.audit',
            compact('site', 'audits')
        );
    }


    public function revealPassword(
        NmsSite $site,
        NmsHardware $hardware
    ) {
        /*
        |--------------------------------------------------------------------------
        | MAKE SURE HARDWARE BELONGS TO SITE
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $hardware->site_id === $site->id,
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
            'Only administrators can reveal hardware passwords.'
        );

        /*
        |--------------------------------------------------------------------------
        | CHECK PASSWORD EXISTS
        |--------------------------------------------------------------------------
        */

        if (empty($hardware->password)) {
            return response()->json([
                'success' => false,
                'message' => 'No password stored for this hardware.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | PASSWORD IS AUTOMATICALLY DECRYPTED
        |--------------------------------------------------------------------------
        |
        | Because NmsHardware model should contain:
        |
        | protected $casts = [
        |     'password' => 'encrypted',
        | ];
        |
        */
        NmsHardwareAudit::create([
            'site_id' => $site->id,
            'hardware_id' => $hardware->id,
            'user_id' => auth()->id(),
            'username' => auth()->user()?->username
                ?? auth()->user()?->name
                ?? null,
            'action' => 'reveal_password',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return response()->json([
            'success' => true,
            'password' => $hardware->password,
        ]);
    }
}