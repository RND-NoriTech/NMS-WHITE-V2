<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NmsHardwareAudit extends Model
{
    protected $table = 'nms_hardware_audits';

    protected $fillable = [
        'site_id',
        'hardware_id',
        'user_id',
        'username',
        'action',
        'ip_address',
        'user_agent',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(NmsSite::class, 'site_id');
    }

    public function hardware(): BelongsTo
    {
        return $this->belongsTo(NmsHardware::class, 'hardware_id');
    }
}