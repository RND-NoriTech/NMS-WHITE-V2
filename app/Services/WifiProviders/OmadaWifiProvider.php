<?php

namespace App\Services\WifiProviders;

use App\Models\NmsWifiConnection;

/*
|--------------------------------------------------------------------------
| OMADA WIFI PROVIDER
|--------------------------------------------------------------------------
|
| Structure/stub only. No Omada Open API / Controller API / Cloud API
| endpoints are invented here.
|
| When an Omada integration is added later (Open API, Controller API or
| Cloud API), it plugs into this class behind the same interface.
|
| Until API credentials/endpoints are configured the provider returns
| provider-not-configured gracefully and the manual SSID fallback of
| the WiFi record is preserved.
|
| No credentials are requested, returned, stored or logged here.
|
*/

class OmadaWifiProvider implements WifiProviderInterface
{
    public function supports(NmsWifiConnection $wifi): bool
    {
        return $wifi->provider === 'omada';
    }

    public function getInterfaces(): array
    {
        /*
        |--------------------------------------------------------------------------
        | NOT CONFIGURED YET
        |--------------------------------------------------------------------------
        */

        return [];
    }

    public function getWifiMetadata(string $interfaceName): array
    {
        return [];
    }

    public function sync(NmsWifiConnection $wifi): array
    {
        /*
        |--------------------------------------------------------------------------
        | GRACEFUL NOT-CONFIGURED RESULT
        |--------------------------------------------------------------------------
        |
        | SNMP / LibreNMS status and traffic are untouched. The manually
        | entered SSID is kept until an Omada integration exists.
        |
        */

        return [
            'status' => 'not_configured',
            'message' => 'API not configured',
            'metadata' => [],
        ];
    }
}
