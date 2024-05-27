<?php

namespace App\Traits;

use App\Models\Role\Role;
use Illuminate\Database\Eloquent\Builder;
use Exception;

trait AllowedRoles
{
    /**
     * Scope a query to only allowed roles.
     */
    public function scopeAllowedRoles(Builder $query, string $property = 'deleteAllowedRoles'): void
    {
        if (auth()->user()->isAdmin()) $query;

        if (!property_exists($this, $property)) {
            throw new Exception("Model {{" . class_basename($this) . "}} doesn\t have the {{$property}} property", 500);
        }

        $currentUserRoleNames = auth()->user()?->roles?->pluck('name')->toArray();
        $currentUserRoleIds = auth()->user()?->roles?->pluck('id')->toArray();
        $deleteAllowedRoles = collect($this->$property)->filter(fn ($item, $key) => in_array($key, $currentUserRoleNames));
        $role_ids = Role::whereIn('name', collect($deleteAllowedRoles)->flatten())->pluck('id')->concat($currentUserRoleIds);

        $query->whereHas('user', function ($q) use ($role_ids) {
            $q->whereHas('roles', function ($q) use ($role_ids) {
                $q->whereIn('id', $role_ids);
            });
        });
    }
}
