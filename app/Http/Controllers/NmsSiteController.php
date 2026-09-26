<?php

namespace App\Http\Controllers;

use App\Models\NmsSite;
use Illuminate\Http\Request;

class NmsSiteController extends Controller
{
    public function index()
    {
        $sites = NmsSite::with(['teams' => function ($query) {
            $query->orderBy('id');
        }])
        ->orderBy('name')
        ->get();

        return view('sites.index', compact('sites'));
    }

    public function create()
    {
        return view('sites.create');
    }

    public function show(NmsSite $site)
    {
        $defaultTeam = $site->teams()
            ->orderBy('id')
            ->first();

        return view('sites.show', compact('site', 'defaultTeam'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:nms_sites,code',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        /*
         * Create site
         */
        $site = NmsSite::create($validated);

        /*
         * Create default team
         */
        $team = $site->teams()->create([
            'name' => $site->name . ' Team',
            'code' => $site->code . '-MAIN',
            'team_leader' => $site->contact_person,
            'contact_number' => $site->contact_number,
            'status' => 'active',
            'notes' => 'Default team created automatically with site.',
        ]);

        /*
         * Create default team leader member
         */
        if ($site->contact_person) {
            $team->members()->create([
                'name' => $site->contact_person,
                'role' => 'team_leader',
                'phone' => $site->contact_number,
                'email' => null,
                'status' => 'active',
                'notes' => 'Default team leader created automatically with site.',
            ]);
        }

        return redirect()
            ->route('sites.index')
            ->with('success', 'Site and default team created successfully.');
    }

    public function edit(NmsSite $site)
    {
        return view('sites.edit', compact('site'));
    }

    public function update(Request $request, NmsSite $site)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:nms_sites,code,' . $site->id,
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        /*
         * Update site
         */
        $site->update($validated);

        /*
         * Get default team
         */
        $defaultTeam = $site->teams()
            ->orderBy('id')
            ->first();

        if ($defaultTeam) {

            /*
             * Sync default team
             */
            $defaultTeam->update([
                'name' => $site->name . ' Team',
                'code' => $site->code . '-MAIN',
                'team_leader' => $site->contact_person,
                'contact_number' => $site->contact_number,
            ]);

            /*
             * Find existing team leader member
             */
            $leaderMember = $defaultTeam->members()
                ->where('role', 'team_leader')
                ->first();

            if ($site->contact_person) {

                /*
                 * Update existing team leader member
                 */
                if ($leaderMember) {
                    $leaderMember->update([
                        'name' => $site->contact_person,
                        'phone' => $site->contact_number,
                        'status' => 'active',
                    ]);
                }

                /*
                 * If no team leader member exists,
                 * create one automatically
                 */
                else {
                    $defaultTeam->members()->create([
                        'name' => $site->contact_person,
                        'role' => 'team_leader',
                        'phone' => $site->contact_number,
                        'email' => null,
                        'status' => 'active',
                        'notes' => 'Team leader created automatically from site.',
                    ]);
                }
            }
        }

        return redirect()
            ->route('sites.show', $site)
            ->with('success', 'Site updated successfully.');
    }

    public function destroy(NmsSite $site)
    {
        $site->delete();

        return redirect()
            ->route('sites.index')
            ->with('success', 'Site deleted successfully.');
    }
}
