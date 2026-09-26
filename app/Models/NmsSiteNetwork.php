<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NmsSiteNetwork extends Model
{
    protected $table = 'nms_site_networks';

    protected $fillable = [
        'site_id',
        'management_subnet',
        'gateway',
        'dns_domain',
        'monitoring_method',
        'vpn_status',
        'notes',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(NmsSite::class, 'site_id');
    }
}
