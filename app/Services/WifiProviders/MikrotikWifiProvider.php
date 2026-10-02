<?php

namespace App\Services\WifiProviders;

use App\Models\NmsWifiConnection;
use RuntimeException;

/*
|--------------------------------------------------------------------------
| MIKROTIK WIFI PROVIDER
|--------------------------------------------------------------------------
|
| Uses the RouterOS API ONLY for metadata enrichment of the interface
| linked through LibreNMS. The query targets /interface/wifi/print and
| the RouterOS interface name is matched against the linked LibreNMS
| Port ifName (wifi1, wifi2, wifi3, ...).
|
| Only safe fields are read:
|   configuration.ssid, configuration.mode, mac-address, radio-mac,
|   master-interface, country, running, disabled
|
| security.passphrase and any other credential-like field are NEVER
| requested, returned or stored.
|
| Credentials come from Laravel config (.env), never from the database,
| and are never logged. Until configured the provider returns
| provider-not-configured gracefully and the manual SSID is preserved.
|
*/

class MikrotikWifiProvider implements WifiProviderInterface
{
    /*
    |--------------------------------------------------------------------------
    | SAFE ROUTEROS FIELDS -> NORMALIZED KEYS
    |--------------------------------------------------------------------------
    */

    private const FIELD_MAP = [
        'name' => 'interface_name',
        'configuration.ssid' => 'ssid',
        'configuration.mode' => 'wireless_mode',
        'mac-address' => 'mac_address',
        'radio-mac' => 'radio_mac_address',
        'master-interface' => 'master_interface',
        'country' => 'country',
        'running' => 'running',
        'disabled' => 'disabled',
    ];

    public function supports(NmsWifiConnection $wifi): bool
    {
        return $wifi->provider === 'mikrotik';
    }

    public function getInterfaces(): array
    {
        $host = config('nms-wifi.providers.mikrotik.api_host');

        if (! $host || ! $this->configurationReady()) {
            return [];
        }

        return $this->fetchInterfaces($host);
    }

    public function getWifiMetadata(string $interfaceName): array
    {
        foreach ($this->getInterfaces() as $interface) {
            if (($interface['interface_name'] ?? null) === $interfaceName) {
                return $interface;
            }
        }

        return [];
    }

    public function sync(NmsWifiConnection $wifi): array
    {
        /*
        |--------------------------------------------------------------------------
        | ENRICHMENT REQUIRES A LINKED LIBRENMS PORT
        |--------------------------------------------------------------------------
        |
        | LibreNMS keeps monitoring the device and port; RouterOS API only
        | enriches SSID / radio metadata matched by the port ifName.
        |
        */

        if ($wifi->monitoring_method !== 'snmp') {
            return [
                'status' => 'unsupported',
                'message' => 'MikroTik metadata sync requires SNMP / LibreNMS monitoring with a linked interface.',
                'metadata' => [],
            ];
        }

        $interfaceName = $wifi->port?->ifName;

        if (! $interfaceName) {
            return [
                'status' => 'unsupported',
                'message' => 'No linked LibreNMS interface to match RouterOS interface names.',
                'metadata' => [],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | GRACEFUL WHEN NOT CONFIGURED, MANUAL SSID IS PRESERVED
        |--------------------------------------------------------------------------
        */

        $host = $wifi->device?->hostname;

        if (! $host || ! $this->configurationReady()) {
            return [
                'status' => 'not_configured',
                'message' => 'API not configured',
                'metadata' => [],
            ];
        }

        try {
            $interfaces = $this->fetchInterfaces($host);
        } catch (RuntimeException $e) {
            return [
                'status' => 'error',
                'message' => 'RouterOS API error: ' . $e->getMessage(),
                'metadata' => [],
            ];
        }

        foreach ($interfaces as $interface) {
            if (($interface['interface_name'] ?? null) === $interfaceName) {
                return [
                    'status' => 'synced',
                    'message' => 'Metadata synced from RouterOS API.',
                    'metadata' => $interface,
                ];
            }
        }

        return [
            'status' => 'error',
            'message' => 'Interface ' . $interfaceName . ' not found on the RouterOS device.',
            'metadata' => [],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY AND NORMALIZE /INTERFACE/WIFI/PRINT
    |--------------------------------------------------------------------------
    |
    | Only the safe fields from FIELD_MAP are copied; everything else
    | (including security.* fields) is discarded.
    |
    */

    private function fetchInterfaces(string $host): array
    {
        $rows = [];

        $connection = $this->connect($host);

        try {
            $this->login($connection);

            foreach ($this->readReply($connection, '/interface/wifi/print') as $sentence) {
                $row = $sentence['data'];

                $normalized = [];

                foreach (self::FIELD_MAP as $rosKey => $normalizedKey) {
                    if (array_key_exists($rosKey, $row) && $row[$rosKey] !== '') {
                        $normalized[$normalizedKey] = $row[$rosKey];
                    }
                }

                if (! empty($normalized['interface_name'])) {
                    $rows[] = $normalized;
                }
            }
        } finally {
            fclose($connection);
        }

        return $rows;
    }

    private function configurationReady(): bool
    {
        $config = config('nms-wifi.providers.mikrotik');

        return ! empty($config['enabled'])
            && ! empty($config['api_user'])
            && ! empty($config['api_password']);
    }

    /*
    |--------------------------------------------------------------------------
    | ROUTEROS API PROTOCOL CLIENT
    |--------------------------------------------------------------------------
    |
    | Minimal implementation of the RouterOS binary API protocol:
    | length-prefixed words, sentences terminated by a zero-length word.
    |
    */

    private function connect(string $host)
    {
        $config = config('nms-wifi.providers.mikrotik');

        $port = $config['api_port'] ?? 8728;
        $timeout = $config['timeout'] ?? 3;

        $connection = @stream_socket_client(
            'tcp://' . $host . ':' . $port,
            $errno,
            $errstr,
            (float) $timeout
        );

        if (! $connection) {
            throw new RuntimeException('Unable to connect to RouterOS API.');
        }

        stream_set_timeout($connection, (int) $timeout);

        return $connection;
    }

    private function login($connection): void
    {
        $config = config('nms-wifi.providers.mikrotik');

        $sentences = $this->execute(
            $connection,
            ['/login', '=name=' . $config['api_user'], '=password=' . $config['api_password']]
        );

        foreach ($sentences as $sentence) {
            if ($sentence['type'] === 'trap' || $sentence['type'] === 'fatal') {
                throw new RuntimeException('RouterOS authentication failed.');
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SEND COMMAND AND COLLECT REPLY ROWS
    |--------------------------------------------------------------------------
    |
    | Returns reply sentences as arrays:
    |   ['type' => 're'|'done'|'trap'|'fatal', 'data' => [key => value]]
    |
    */

    private function readReply($connection, string $command): array
    {
        $sentences = $this->execute($connection, [$command]);

        foreach ($sentences as $sentence) {
            if ($sentence['type'] === 'trap' || $sentence['type'] === 'fatal') {
                throw new RuntimeException('RouterOS query failed.');
            }
        }

        return $sentences;
    }

    private function execute($connection, array $words): array
    {
        $this->writeSentence($connection, $words);

        $sentences = [];

        while (true) {
            $sentence = $this->readSentence($connection);

            if ($sentence === null) {
                break;
            }

            $type = $sentence[0] ?? '';

            if ($type === '' ) {
                continue;
            }

            $data = [];

            foreach (array_slice($sentence, 1) as $word) {
                if (str_starts_with($word, '=')) {
                    $parts = explode('=', substr($word, 1), 2);

                    $data[$parts[0]] = $parts[1] ?? '';
                }
            }

            $sentences[] = [
                'type' => ltrim($type, '!'),
                'data' => $data,
            ];

            if ($type === '!done' || $type === '!fatal') {
                break;
            }
        }

        return $sentences;
    }

    private function writeSentence($connection, array $words): void
    {
        foreach ($words as $word) {
            $this->writeWord($connection, $word);
        }

        fwrite($connection, $this->encodeLength(0));
    }

    private function writeWord($connection, string $word): void
    {
        fwrite($connection, $this->encodeLength(strlen($word)) . $word);
    }

    private function encodeLength(int $length): string
    {
        if ($length < 128) {
            return chr($length);
        }

        if ($length < 16384) {
            return pack('n', $length | 0x8000);
        }

        if ($length < 2097152) {
            return pack('n', ($length >> 8) | 0xC000) . chr($length & 0xFF);
        }

        return pack('N', $length | 0xE0000000);
    }

    private function readSentence($connection): ?array
    {
        $words = [];

        while (true) {
            $word = $this->readWord($connection);

            if ($word === null) {
                return null;
            }

            if ($word === '') {
                break;
            }

            $words[] = $word;
        }

        return $words;
    }

    private function readWord($connection): ?string
    {
        $length = $this->readLength($connection);

        if ($length === null) {
            return null;
        }

        if ($length === 0) {
            return '';
        }

        $buffer = '';

        while (strlen($buffer) < $length) {
            $chunk = fread($connection, $length - strlen($buffer));

            if ($chunk === false || $chunk === '') {
                throw new RuntimeException('Connection to RouterOS API lost.');
            }

            $buffer .= $chunk;
        }

        return $buffer;
    }

    private function readLength($connection): ?int
    {
        $byte = $this->readByte($connection);

        if ($byte === null) {
            return null;
        }

        if ($byte < 128) {
            return $byte;
        }

        if (($byte & 0xC0) === 0x80) {
            return (($byte & 0x3F) << 8) | $this->readByteOrThrow($connection);
        }

        if (($byte & 0xE0) === 0xC0) {
            return (($byte & 0x1F) << 16)
                | ($this->readByteOrThrow($connection) << 8)
                | $this->readByteOrThrow($connection);
        }

        return (($byte & 0x0F) << 24)
            | ($this->readByteOrThrow($connection) << 16)
            | ($this->readByteOrThrow($connection) << 8)
            | $this->readByteOrThrow($connection);
    }

    private function readByte($connection): ?int
    {
        $data = fread($connection, 1);

        if ($data === false || $data === '') {
            return null;
        }

        return ord($data);
    }

    private function readByteOrThrow($connection): int
    {
        $byte = $this->readByte($connection);

        if ($byte === null) {
            throw new RuntimeException('Connection to RouterOS API lost.');
        }

        return $byte;
    }
}
