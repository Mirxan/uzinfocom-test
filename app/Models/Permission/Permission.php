<?php

namespace App\Models\Permission;

use App\Models\Role\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'controller',
        'description_for_action',
        'description_for_controller',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class)->using(PermissionRole::class)
            ->withTimestamps();
    }
}
