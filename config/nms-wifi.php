<?php

/*
|--------------------------------------------------------------------------
| NMS-WHITE WiFi PROVIDER CONFIGURATION
|--------------------------------------------------------------------------
|
| Provider APIs are used ONLY for metadata enrichment (SSID, wireless
| mode, radio metadata). LibreNMS remains the source of truth for
| device status, interface status, traffic and ports.
|
| SECURITY
| Provider credentials are read from the environment only. They are
| never stored in the database, never written to logs and never
| returned to the browser. A proper credential vault / encrypted
| storage design is required before any database-backed credential
| persistence may be added.
|
*/

return [

    'providers' => [

        'mikrotik' => [
            'enabled' => env('NMS_WIFI_MIKROTIK_ENABLED', false),
            'api_host' => env('NMS_WIFI_MIKROTIK_API_HOST'),
            'api_user' => env('NMS_WIFI_MIKROTIK_API_USER'),
            'api_password' => env('NMS_WIFI_MIKROTIK_API_PASSWORD'),
            'api_port' => env('NMS_WIFI_MIKROTIK_API_PORT', 8728),
            'timeout' => env('NMS_WIFI_MIKROTIK_TIMEOUT', 3),
        ],

        'ruckus' => [
            'enabled' => false,
        ],

        'omada' => [
            'enabled' => false,
        ],

    ],

];
