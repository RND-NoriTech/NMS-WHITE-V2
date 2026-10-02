<?php

namespace App\Models;

use App\Models\Device;
use App\Models\Port;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NmsWifiConnection extends Model
{
    protected $table = 'nms_wifi_connections';

    protected $fillable = [
        'site_id',
        'team_id',
        'name',
        'provider',
        'ssid',
        'wireless_mode',
        'device_name',
        'model',
        'device_id',
        'mac_address',
        'radio_mac_address',
        'master_interface',
        'ip_address',
        'gateway',
        'librenms_device_id',
        'librenms_port_id',
        'api_provider',
        'external_device_id',
        'username',
        'password',
        'monitoring_method',
        'status',
        'notes',
        'metadata_synced_at',
        'metadata_sync_status',
        'metadata_sync_message',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'encrypted',
        'metadata_synced_at' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(NmsSite::class, 'site_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(NmsTeam::class, 'team_id');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'librenms_device_id', 'device_id');
    }

    public function port(): BelongsTo
    {
        return $this->belongsTo(Port::class, 'librenms_port_id', 'port_id');
    }
}
