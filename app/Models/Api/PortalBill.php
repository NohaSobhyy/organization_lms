<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalBill extends Model
{
    use HasFactory;
    protected $table = 'portal_bill';
    protected $fillable = ['name', 'bill', 'portal_id'];
}
