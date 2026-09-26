<?php

namespace App\Http\Controllers;

use App\Models\NmsSite;
use Illuminate\Http\Request;

class NmsSiteNetworkController extends Controller
{
    public function edit(NmsSite $site)
    {
        $network = $site->network;

        return view('sites.network.edit', compact('site', 'network'));
    }

    public function update(Request $request, NmsSite $site)
    {
        $validated = $request->validate([
            'management_subnet' => 'nullable|string|max:255',
            'gateway' => 'nullable|string|max:255',
            'dns_domain' => 'nullable|string|max:255',
            'monitoring_method' => 'required|in:direct,vpn,dns',
            'vpn_status' => 'required|in:connected,disconnected,unknown',
            'notes' => 'nullable|string',
        ]);

        $site->network()->updateOrCreate(
            ['site_id' => $site->id],
            $validated
        );

        return redirect()
            ->route('sites.show', $site)
            ->with('success', 'Site network settings updated successfully.');
    }
}
