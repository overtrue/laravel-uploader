<?php

/*
 * This file is part of the laravel-uploader.
 *
 * (c) 2016 overtrue <i@overtrue.me>
 */

namespace Overtrue\LaravelUploader\Services;

use Closure;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemManager;
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
     * @param                                                     $disk
     * @param string                                              $dir
     *
     * @return array|bool
     */
    public function store(UploadedFile $file, $disk, $dir = '')
    {
        $hashName = str_ireplace('.jpeg', '.jpg', $file->hashName());

        $dir = $this->formatDir($dir);

        $mime = $file->getMimeType();

        $path = $this->filesystem->disk($disk)->putFileAs($dir, $file, $hashName);

        if (!$path) {
            throw new Exception("Failed to store file.");
        }

        return [
            'success' => true,
            'filename' => $hashName,
            'original_name' => $file->getClientOriginalName(),
            'mime' => $mime,
            'size' => $file->getClientSize(),
            'storage_path' => $path,
            'relative_url' => '/storage/'.$path,
            'url' => Storage::disk($disk)->url($path),
            'dataURL' => $this->getDataUrl($mime, $this->filesystem->disk($disk)->get($path)),
        ];
    }

    /**
     * Replace date variable in dir path.
     *
     * @param  string $dir
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
     * @return array
     */
    public function delete($path, $disk)
    {
        if (stripos($path, 'storage') === 0) {
            $path = substr($path, strlen('storage'));
        }

        $this->filesystem->disk($disk)->delete($path);
    }
}
