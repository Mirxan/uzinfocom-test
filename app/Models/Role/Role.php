<?php

namespace App\Models\Role;

use App\Models\Permission\Permission;
use App\Models\Permission\PermissionRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;

class Role extends Model
{

    public const ADMIN = 'Admin';
    public const USER = 'User';

    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(RoleUser::class)
            ->withTimestamps();
    }


    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)
            ->using(PermissionRole::class)
            ->withTimestamps();
    }
}
