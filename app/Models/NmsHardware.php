<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NmsHardware extends Model
{
    protected $table = 'nms_hardware';

    protected $fillable = [
        'site_id',
        'team_id',
        'model',
        'device_id',
        'gateway',
        'mac_address',
        'username',
        'password',
        'status',
        'notes',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'encrypted',
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