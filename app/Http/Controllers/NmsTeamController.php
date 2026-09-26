<?php

namespace App\Http\Controllers;

use App\Models\NmsSite;
use App\Models\NmsTeam;
use Illuminate\Http\Request;

class NmsTeamController extends Controller
{
    public function show(NmsSite $site, NmsTeam $team)
    {
    abort_unless($team->site_id === $site->id, 404);

    $team->load('members');

    return view('sites.teams.show', compact('site', 'team'));
    }
    public function index(NmsSite $site)
    {
        $teams = $site->teams()
            ->orderBy('name')
            ->get();

        return view('sites.teams.index', compact('site', 'teams'));
    }

    public function create(NmsSite $site)
    {
        return view('sites.teams.create', compact('site'));
    }

    public function store(Request $request, NmsSite $site)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'team_leader' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $site->teams()->create($validated);

        return redirect()
            ->route('sites.teams.index', $site)
            ->with('success', 'Team created successfully.');
    }

    public function edit(NmsSite $site, NmsTeam $team)
    {
        abort_unless($team->site_id === $site->id, 404);

        return view('sites.teams.edit', compact('site', 'team'));
    }

    public function update(Request $request, NmsSite $site, NmsTeam $team)
    {
        abort_unless($team->site_id === $site->id, 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'team_leader' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $team->update($validated);

        return redirect()
            ->route('sites.teams.index', $site)
            ->with('success', 'Team updated successfully.');
    }

    public function destroy(NmsSite $site, NmsTeam $team)
    {
        abort_unless($team->site_id === $site->id, 404);

        $team->delete();

        return redirect()
            ->route('sites.teams.index', $site)
            ->with('success', 'Team deleted successfully.');
    }
}
