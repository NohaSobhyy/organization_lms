<?php

namespace App\Models;

use App\Models\Api\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Portal;

class Department extends Model
{
    use HasFactory;

    protected $table = 'portal_departments';
    protected $guarded = [];

    /**
     * Get the portal that owns the department.
     */
    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class, 'portal_id');
    }

    /**
     * Get the users that belong to the department.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'portal_department_user', 'department_id', 'user_id')
            ->withTimestamps();
    }
}
