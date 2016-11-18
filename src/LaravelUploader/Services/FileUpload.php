<?php

/*
 * This file is part of the laravel-uploader.
 *
 * (c) 2016 overtrue <i@overtrue.me>
 */

namespace Overtrue\LaravelUploader\Services;

use Closure;
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
     * @param string                                              $file
     * @param string                                              $path path to store.
     *
     * @return array|bool
     */
    public function store(UploadedFile $file, $disk, $dir = '', Closure $callback = null)
    {
        $hashName = str_ireplace('.jpeg', '.jpg', $file->hashName());

        $mime = $file->getMimeType();

        $realname = $file->storeAs($dir, $hashName, $disk);

        return [
                'filename' => $hashName,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $mime,
                'size' => $file->getClientSize(),
                'relative_url' => "storage/$realname",
                'url' => asset("storage/$realname"),
                'dataURL' => $this->getDataUrl($mime, $this->filesystem->disk($disk)->get($realname)),
        ];
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
