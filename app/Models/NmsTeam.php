<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NmsTeam extends Model
{
    protected $table = 'nms_teams';

    protected $fillable = [
        'site_id',
        'name',
        'code',
        'team_leader',
        'contact_number',
        'status',
        'notes',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(NmsSite::class, 'site_id');
    }
    public function members(): HasMany
    {
        return $this->hasMany(NmsTeamMember::class, 'team_id');
    }
}
