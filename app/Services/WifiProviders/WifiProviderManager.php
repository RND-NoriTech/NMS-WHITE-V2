<?php

namespace App\Services\WifiProviders;

use App\Models\NmsWifiConnection;

/*
|--------------------------------------------------------------------------
| WIFI PROVIDER MANAGER
|--------------------------------------------------------------------------
|
| Resolves the provider handling a WiFi record. A Site may contain
| devices from different providers, so resolution is per record.
|
| Provider values: mikrotik, ruckus, omada, other.
| The 'other' value intentionally has no provider class; LibreNMS/SNMP
| (or manual) handles those records without enrichment.
|
*/

class WifiProviderManager
{
    /*
    |--------------------------------------------------------------------------
    | REGISTERED PROVIDERS
    |--------------------------------------------------------------------------
    */

    private array $providers;

    public function __construct()
    {
        $this->providers = [
            new MikrotikWifiProvider(),
            new RuckusWifiProvider(),
            new OmadaWifiProvider(),
        ];
    }

    public function for(NmsWifiConnection $wifi): ?WifiProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($wifi)) {
                return $provider;
            }
        }

        return null;
    }
}
