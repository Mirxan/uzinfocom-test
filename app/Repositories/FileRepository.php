<?php

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Interfaces\FileRepositoryInterface;
use App\Models\File\File;
use App\Models\File\FileUser;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileRepository implements FileRepositoryInterface
{
    public function getFiles(array $request): LengthAwarePaginator
    {
        return FileUser::ownFiles()->with(['user'])->paginate(getPerPage($request));
    }

    public function getFile(string $unique_key, string $filename): string
    {
        $file = FileUser::ownFiles()->where([
            'filename' => $filename,
            'unique_key' => $unique_key,
        ])->firstOrFail();

        $filename = $file->file()->value('filename') . "." . $file->file()->value('extension');

        return getFilePath($filename);
    }

    public function createFile(array $request): string
    {
        return DB::transaction(function () use ($request) {
            $this->uploadFile($request['file']);
            return "File uploaded successfully!";
        });
    }

    public function createMultipleFiles(array $request): string
    {
        foreach ($request['files'] as $f) {
            $this->uploadFile($f);
        }

        return "Files uploaded successfully!";
    }

    public function deleteFile(int $id): void
    {
        DB::transaction(function () use ($id) {
            $fileUser = FileUser::allowedRoles()->with(['file'])->findOrFail($id);
            $file = $fileUser->file;

            FileUser::destroy($id);

            if (!$file->users()->count()) {
                Storage::disk('public')->delete(File::DIRECTORY_NAME . "/" . $file->file_url);
                $file->delete();
            }
        });
    }

    private function uploadFile(UploadedFile $file): void
    {
        $extension = $file->extension();
        $filename = $file->getClientOriginalName();
        $size = $file->getSize();
        $md5_file = md5_file($file);
        $generated_filename = now()->timestamp . Str::random(12);

        $newFile = File::where(['md5_file' => $md5_file])->firstOr(function () use ($file, $md5_file, $extension, $generated_filename, $size) {
            $newFile = File::create([
                'md5_file' => $md5_file,
                'extension' => $extension,
                'filename' => $generated_filename,
                'size' => $size,
            ]);

            if ($newFile) {
                $file->storeAs(File::DIRECTORY_NAME, $generated_filename . '.' . $extension, [
                    'disk' => 'public',
                ]);
            }

            return $newFile;
        });

        $user = $newFile->users()->where('user_id', auth()->id());

        $user->firstOr(function () use ($filename, $user) {
            $unique_key = auth()->id() . now()->format('U');
            $user->attach(auth()->id(), [
                'filename' => $filename,
                'unique_key' => $unique_key
            ]);
        });
    }
}
