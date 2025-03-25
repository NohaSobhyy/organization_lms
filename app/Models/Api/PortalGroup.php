<?php

namespace App\Models\Api;

use App\Models\Portal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalGroup extends Model
{
    use HasFactory;
    protected $table = 'portal_groups';

    protected $fillable = [
        'name',
        'portal_id',
        'team_leader_id'
    ];

    public function portal()
    {
        return $this->belongsTo(Portal::class);
    }

    public function teamLeader()
    {
        return $this->belongsTo(User::class, 'team_leader_id');
    }

    public function portalUsers()
    {
        return $this->belongsToMany(PortalUser::class, 'portal_group_users');
    }
}
