<?php

namespace App\Models\Api;

use App\Models\Portal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalUser extends Model
{
    use HasFactory;
    protected $table = 'portal_users';

    protected $fillable = [
        'user_id',
        'portal_id',
        'role'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function portal()
    {
        return $this->belongsTo(Portal::class);
    }

    public function groups()
    {
        return $this->belongsToMany(PortalGroup::class, 'group_user');
    }
}
