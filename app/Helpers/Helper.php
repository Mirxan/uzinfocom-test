<?php

use App\Models\File\File;
use Illuminate\Support\Facades\Storage;


if (!function_exists('getPerPage')) {
    function getPerPage(array|object|null $request): int
    {
        return $request && isset($request['per_page']) ? $request['per_page'] : 15;
    }
}

if (!function_exists('getFilePath')) {

    function getFilePath(string $filename): string
    {
        return Storage::disk('public')->path(File::DIRECTORY_NAME . "/" . $filename);
    }
}
