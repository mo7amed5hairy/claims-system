<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasMediaUpload
{
    /**
     * Upload a single media file.
     */
    public function uploadMedia(
        ?UploadedFile $file,
        array $options = []
    ): ?string {
        if (!$file) {
            return null;
        }

        $disk   = $options['disk']   ?? 'public';
        $folder = trim($options['folder'] ?? $this->getTable(), '/');
        $name   = $options['name']   ?? Str::uuid()->toString();

        $extension = $file->getClientOriginalExtension();
        $filename  = "{$name}.{$extension}";

        $path = $file->storeAs($folder, $filename, $disk);

        return $path;
    }

    /**
     * Upload multiple media files.
     */
    public function uploadMultipleMedia(
        ?array $files,
        array $options = []
    ): array {
        if (!$files) {
            return [];
        }

        $paths = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = $this->uploadMedia($file, $options);
            }
        }

        return array_filter($paths);
    }

    /**
     * Replace old media files with new ones.
     */
    public function replaceMultipleMedia(
        ?array $newFiles,
        ?array $oldPaths = [],
        array $options = []
    ): array {
        $disk = $options['disk'] ?? 'public';

        // Delete old files if they exist
        if (!empty($oldPaths)) {
            foreach ($oldPaths as $path) {
                $this->deleteMedia($path, $disk);
            }
        }

        // Upload new files
        return $this->uploadMultipleMedia($newFiles, $options);
    }

    /**
     * Delete a media file.
     */
    public function deleteMedia(?string $path, string $disk = 'public'): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    /**
     * Get the full URL for a media path.
     */
    public function mediaUrl(?string $path, string $disk = 'public'): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return Storage::disk($disk)->url($path);
    }

    /**
     * Get full URLs for multiple media paths.
     */
    public function multipleMediaUrls(?array $paths, string $disk = 'public'): array
    {
        if (empty($paths)) {
            return [];
        }

        return array_map(fn($path) => $this->mediaUrl($path, $disk), $paths);
    }
}
