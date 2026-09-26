<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class NmsSite extends Model
{
    use HasFactory;

    protected $table = 'nms_sites';

    protected $fillable = [
        'name',
        'code',
        'address',
        'latitude',
        'longitude',
        'contact_person',
        'contact_number',
        'status',
        'notes',
    ];
	public function teams(): HasMany
	{
    return $this->hasMany(NmsTeam::class, 'site_id');
}
public function network(): HasOne
{
    return $this->hasOne(NmsSiteNetwork::class, 'site_id');
}
}
