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
     * @param string                                              $dir
     *
     * @return array|bool
     */
    public function store(UploadedFile $file, $disk, $filename, $dir = '')
    {
        $hashName = str_ireplace('.jpeg', '.jpg', $filename);

        $dir = $this->formatDir($dir);

        $mime = $file->getMimeType();

        $path = $this->filesystem->disk($disk)->putFileAs($dir, $file, $hashName);

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
     * Replace date variable in dir path.
     *
     * @param string $dir
     *
     * @return string
     */
    protected function formatDir($dir)
    {
        $replacements = [
            '{Y}' => date('Y'),
            '{m}' => date('m'),
            '{d}' => date('d'),
            '{H}' => date('H'),
            '{i}' => date('i'),
        ];

        return str_replace(array_keys($replacements), $replacements, $dir);
    }

    /**
     * Delete a file from disk.
     *
     * @param string $path
     *
     * @param        $disk
     *
     * @return void
     */
    public function delete($path, $disk)
    {
        if (0 === stripos($path, 'storage')) {
            $path = substr($path, strlen('storage'));
        }

        $this->filesystem->disk($disk)->delete($path);
    }
}
