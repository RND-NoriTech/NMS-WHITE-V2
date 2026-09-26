<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NmsTeamMember extends Model
{
    protected $table = 'nms_team_members';

    protected $fillable = [
        'team_id',
        'name',
        'role',
        'phone',
        'email',
        'status',
        'notes',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(NmsTeam::class, 'team_id');
    }
}
