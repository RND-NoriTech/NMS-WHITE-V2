<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NmsSiteDevice extends Model
{
    protected $table = 'nms_site_devices';

    protected $fillable = [
        'site_id',
        'device_id',
        'team_id',
        'notes',
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
        return $this->belongsTo(Device::class, 'device_id', 'device_id');
    }
}
