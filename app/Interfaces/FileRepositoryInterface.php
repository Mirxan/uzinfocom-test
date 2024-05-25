<?php

namespace App\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\File\File;

interface FileRepositoryInterface
{
    public function getFiles(array $request): LengthAwarePaginator;

    public function getFile(string $unique_key, string $filename): string;

    public function createFile(array $request): string;

    public function createMultipleFiles(array $request): string;

    public function deleteFile(int $id): void;
}
