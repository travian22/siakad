<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoService
{
    public function replace(?UploadedFile $newFile, ?string $currentPath, string $directory): ?string
    {
        if (! $newFile) {
            return $currentPath;
        }

        $newPath = $newFile->store($directory, 'public');

        if ($currentPath && Storage::disk('public')->exists($currentPath)) {
            Storage::disk('public')->delete($currentPath);
        }

        return $newPath;
    }
}
