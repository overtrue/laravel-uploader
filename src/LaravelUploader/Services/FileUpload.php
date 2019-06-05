<?php

/*
 * This file is part of the overtrue/laravel-uploader.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelUploader\Services;

use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUpload
{
    /**
     * Filesystem instance.
     *
     * @var \Illuminate\Filesystem\FilesystemManager
     */
    protected $filesystem;

    /**
     * Create a new ImageUploadService instance.
     *
     * @param \Illuminate\Filesystem\FilesystemManager $filesystem
     */
    public function __construct(FilesystemManager $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Construct the data URL for the JSON body.
     *
     * @param string $mime
     * @param string $content
     *
     * @return string
     */
    protected function getDataUrl($mime, $content)
    {
        $base = base64_encode($content);

        return 'data:'.$mime.';base64,'.$base;
    }

    /**
     * Handle the file upload. Returns the response body on success, or false
     * on failure.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param string                                              $disk
     * @param string                                              $filename
     * @param string                                              $directory
     *
     * @return array|bool
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function store(UploadedFile $file, string $disk, string $filename, string $directory = '')
    {
        $hashName = str_ireplace('.jpeg', '.jpg', $filename);

        $mime = $file->getClientMimeType();

        $path = $this->filesystem->disk($disk)->putFileAs($directory, $file, $hashName);

        if (!$path) {
            throw new Exception('Failed to store file.');
        }

        return [
            'success' => true,
            'filename' => $hashName,
            'original_name' => $file->getClientOriginalName(),
            'mime' => $mime,
            'size' => $file->getSize(),
            'storage_path' => $path,
            'relative_url' => str_replace(config('app.url'), '', Storage::disk($disk)->url($path)),
            'url' => Storage::disk($disk)->url($path),
            'dataURL' => $this->getDataUrl($mime, $this->filesystem->disk($disk)->get($path)),
        ];
    }

    /**
     * Delete a file from disk.
     *
     * @param string $path
     * @param        $disk
     */
    public function delete($path, $disk)
    {
        if (0 === stripos($path, 'storage')) {
            $path = substr($path, strlen('storage'));
        }

        $this->filesystem->disk($disk)->delete($path);
    }
}
