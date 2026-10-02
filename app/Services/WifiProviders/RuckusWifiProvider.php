<?php

namespace App\Services\WifiProviders;

use App\Models\NmsWifiConnection;

/*
|--------------------------------------------------------------------------
| RUCKUS WIFI PROVIDER
|--------------------------------------------------------------------------
|
| Structure/stub only. No Ruckus API endpoints are invented here.
|
| If Ruckus metadata is already exposed through LibreNMS/SNMP, LibreNMS
| stays the source of truth and is reused automatically by the WiFi
| module. When controller/API integration is added later, it plugs into
| this class behind the same interface.
|
| Until API credentials/endpoints are configured the provider returns
| provider-not-configured gracefully and the manual SSID fallback of
| the WiFi record is preserved.
|
| No credentials are requested, returned, stored or logged here.
|
*/

class RuckusWifiProvider implements WifiProviderInterface
{
    public function supports(NmsWifiConnection $wifi): bool
    {
        return $wifi->provider === 'ruckus';
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
        | entered SSID is kept until a Ruckus integration exists.
        |
        */

        return [
            'status' => 'not_configured',
            'message' => 'API not configured',
            'metadata' => [],
        ];
    }
}
