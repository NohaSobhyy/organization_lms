<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalFeature extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'name_ar', 'description'];

    public function portalPlans()
    {
        return $this->belongsToMany(
            PortalPlan::class,
            'portal_feature_plan',
            'feature_id',
            'plan_id'
        );
    }
}
