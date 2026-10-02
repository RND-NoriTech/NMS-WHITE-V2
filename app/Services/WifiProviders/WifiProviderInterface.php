<?php

namespace App\Services\WifiProviders;

use App\Models\NmsWifiConnection;

/*
|--------------------------------------------------------------------------
| WIFI PROVIDER INTERFACE
|--------------------------------------------------------------------------
|
| Provider APIs are ONLY used for metadata enrichment (SSID, wireless
| mode, radio metadata). LibreNMS remains the source of truth for device
| status, interface status, traffic and ports.
|
| Implementations must never return or store credentials such as
| password, passphrase, PSK or secret.
|
*/

interface WifiProviderInterface
{
    /*
    |--------------------------------------------------------------------------
    | DOES THIS PROVIDER HANDLE THE RECORD
    |--------------------------------------------------------------------------
    */

    public function supports(NmsWifiConnection $wifi): bool;

    /*
    |--------------------------------------------------------------------------
    | LIST INTERFACES EXPOSED BY THE PROVIDER
    |--------------------------------------------------------------------------
    |
    | Returns a list of normalized metadata arrays (one per interface),
    | each containing a non-empty interface_name key. When the provider
    | is not configured, an empty array is returned.
    |
    */

    public function getInterfaces(): array;

    /*
    |--------------------------------------------------------------------------
    | METADATA FOR ONE INTERFACE
    |--------------------------------------------------------------------------
    |
    | $interfaceName is matched against the linked LibreNMS Port ifName.
    | Returns normalized metadata keys or an empty array when unknown.
    |
    */

    public function getWifiMetadata(string $interfaceName): array;

    /*
    |--------------------------------------------------------------------------
    | SYNC A WIFI RECORD
    |--------------------------------------------------------------------------
    |
    | Returns a result array:
    |
    |   status:    synced | not_configured | error | unsupported
    |   message:   short human readable text (never contains secrets)
    |   metadata:  normalized metadata array (may be empty)
    |
    | Providers must never throw for expected conditions such as missing
    | configuration; they return a graceful result instead.
    |
    */

    public function sync(NmsWifiConnection $wifi): array;
}
