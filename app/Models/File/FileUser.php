<?php

namespace App\Models\File;

use App\Models\Role\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\User;
use App\Traits\AllowedRoles;

class FileUser extends Pivot
{
    use HasFactory,
        AllowedRoles;

    protected $fillable = [
        'user_id',
        'file_id',
        'filename',
        'unique_key',
    ];

    protected $appends = [
        'file_url',
    ];

    public $deleteAllowedRoles = [
        Role::MODERATOR => [
            Role::USER,
        ],
    ];

    protected function getFileUrlAttribute(): string
    {
        return env('APP_URL') . "/" . "api/files/" . $this->filename;
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwnFiles(Builder $query): Builder
    {
        return $query->when(auth()->user()->isUser(), fn ($q) => $q->where('user_id', auth()->id()));
    }
}
