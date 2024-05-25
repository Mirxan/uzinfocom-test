<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\File\File;
use App\Models\File\FileUser;
use App\Models\Role\Role;
use App\Models\Role\RoleUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->using(RoleUser::class)
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function files(): BelongsToMany
    {
        return $this->belongsToMany(File::class)
            ->using(FileUser::class)
            ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return in_array(Role::ADMIN, auth()->user()->roles?->pluck('name')->toArray());
    }

    public function isUser(): bool
    {
        return in_array(Role::USER, auth()->user()->roles?->pluck('name')->toArray());
    }

    public function scopePermissions(Builder $query): Builder
    {
        return $query
            ->selectRaw('users.id user_id,r_u.role_id,p.action,p.controller')
            ->leftJoin('role_user as r_u', 'r_u.user_id', '=', 'users.id')
            ->leftJoin('permission_role as p_r', 'p_r.role_id', '=', 'r_u.role_id')
            ->leftJoin('permissions as p', 'p.id', '=', 'p_r.permission_id');
    }

    public function scopeHasPermission(Builder $query, string $controller, string $action)
    {
        return $query->permissions()->where(['user_id' => auth()->id(), 'controller' => $controller, 'action' => $action])->exists();
    }
}
