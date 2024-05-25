<?php

namespace App\Models\File;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\User;

class FileUser extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_id',
        'filename',
        'unique_key',
    ];

    protected $appends = [
        'file_url',
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
