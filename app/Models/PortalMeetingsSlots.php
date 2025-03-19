<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalMeetingsSlots extends Model
{
    use HasFactory;
    protected $table = 'portal_meetings_time_slots';
    protected $guarded = [];
}
