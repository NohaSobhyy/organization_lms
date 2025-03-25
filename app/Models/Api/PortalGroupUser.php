<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalGroupUser extends Model
{
    use HasFactory;
    protected $table = 'portal_group_users';

    protected $fillable = [
        'portal_user_id',
        'portal_group_id'
    ];
}
