<?php

namespace App\Models\File;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class File extends Model
{
    use HasFactory;

    const DIRECTORY_NAME = "files";

    protected $fillable = [
        'filename',
        'extension',
        'md5_file',
        'size',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(FileUser::class)
            ->withTimestamps();
    }

    protected function getFileUrlAttribute(): string
    {
        return $this->filename . "." . $this->extension;
    }
}
