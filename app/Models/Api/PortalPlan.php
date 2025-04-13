<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'description',
        'type',
        'start_date',
        'end_date',
    ];

    public function features()
    {
        return $this->belongsToMany(
            PortalFeature::class,
            'portal_feature_plan',
            'plan_id',
            'feature_id'
        );
    }
}
