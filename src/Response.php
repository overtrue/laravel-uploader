<?php

/*
 * This file is part of the overtrue/laravel-uploader.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelUploader;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Response implements Jsonable, Arrayable
{
    /**
     * @var string
     */
    public $disk;

    /**
     * @var string
     */
    public $path;

    /**
     * @var \Illuminate\Http\UploadedFile
     */
    public $file;

    /**
     * @var \Overtrue\LaravelUploader\Strategy
     */
    public $strategy;

    /**
     * @var string
     */
    public $mime;

    /**
     * @var string
     */
    public $size;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $relativeUrl;

    /**
     * @var string
     */
    public $filename;

    /**
     * @var string
     */
    public $extension;

    /**
     * @var string
     */
    public $originalName;

    /**
     * Response constructor.
     *
     * @param string                             $path
     * @param \Overtrue\LaravelUploader\Strategy $strategy
     * @param \Illuminate\Http\UploadedFile      $file
     */
    public function __construct(string $path, Strategy $strategy, UploadedFile $file)
    {
        $disk = Storage::disk($strategy->getDisk());
        $baseUri = rtrim(config('uploader.base_uri'), '/');
        $driver = config('filesystems.disks.%s.driver', $strategy->getDisk());
        $relativeUrl = \sprintf('/%s', \ltrim($path, '/'));
        $url = url($path);

        if ($baseUri && $driver !== 'local') {
            $url = \sprintf('%s/%s', $baseUri, $path);
        } elseif (method_exists($disk, 'url')) {
            $url = $disk->url($path);
        }

        $this->path = $path;
        $this->file = $file;
        $this->disk = $strategy->getDisk();
        $this->strategy = $strategy;
        $this->filename = \basename($path);
        $this->extension = $file->getClientOriginalExtension();
        $this->originalName = $file->getClientOriginalName();
        $this->mime = $file->getClientMimeType();
        $this->size = $file->getSize();
        $this->url = $url;
        $this->relativeUrl = $relativeUrl;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return \json_encode($this->toArray());
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'mime' => $this->mime,
            'size' => $this->size,
            'path' => $this->path,
            'url' => $this->url,
            'disk' => $this->disk,
            'filename' => $this->filename,
            'extension' => $this->extension,
            'relative_url' => $this->relativeUrl,
            'location' => $this->url,
            'original_name' => $this->originalName,
            'strategy' => $this->strategy->getName(),
        ];
    }
}
