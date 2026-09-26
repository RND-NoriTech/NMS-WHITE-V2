<?php

namespace App\Http\Controllers;

use App\Models\NmsSite;
use App\Models\NmsTeam;
use App\Models\NmsTeamMember;
use Illuminate\Http\Request;

class NmsTeamMemberController extends Controller
{
    public function create(NmsSite $site, NmsTeam $team)
    {
        abort_unless($team->site_id === $site->id, 404);

        return view('sites.teams.members.create', compact('site', 'team'));
    }

    public function store(Request $request, NmsSite $site, NmsTeam $team)
    {
        abort_unless($team->site_id === $site->id, 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:team_leader,team_member',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        /*
         * Kalau user pilih Team Leader:
         * - existing Team Leader dalam member list ditukar jadi Team Member
         * - header Team Leader dalam nms_teams ikut orang baru
         */
        if ($validated['role'] === 'team_leader') {

            $team->members()
                ->where('role', 'team_leader')
                ->update([
                    'role' => 'team_member',
                ]);

            $team->update([
                'team_leader' => $validated['name'],
                'contact_number' => $validated['phone'],
            ]);
        }

        $team->members()->create($validated);

        return redirect()
            ->route('sites.teams.show', [$site, $team])
            ->with('success', 'Team member added successfully.');
    }

    public function destroy(
        NmsSite $site,
        NmsTeam $team,
        NmsTeamMember $member
    ) {
        abort_unless($team->site_id === $site->id, 404);
        abort_unless($member->team_id === $team->id, 404);

        $wasLeader = $member->role === 'team_leader';

        $member->delete();

        if ($wasLeader) {
            $team->update([
                'team_leader' => null,
                'contact_number' => null,
            ]);
        }

        return redirect()
            ->route('sites.teams.show', [$site, $team])
            ->with('success', 'Team member deleted successfully.');
    }
}
