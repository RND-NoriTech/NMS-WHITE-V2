<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NmsVehicle extends Model
{
    protected $table = 'nms_vehicles';

    protected $fillable = [
        'site_id',
        'team_id',
        'name',
        'registration_number',
        'vehicle_type',
        'make',
        'model',
        'year',
        'color',
        'driver_name',
        'driver_phone',
        'tracker_device_id',
        'tracker_imei',
        'tracker_mac_address',
        'tracker_ip_address',
        'latitude',
        'longitude',
        'last_seen_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(NmsSite::class, 'site_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(NmsTeam::class, 'team_id');
    }
}
