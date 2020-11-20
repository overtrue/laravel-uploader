<?php

namespace Overtrue\LaravelUploader;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Response implements Jsonable, Arrayable
{
    public string $disk;
    public string $path;
    public ?string $mime;
    public string $size;
    public string $url;
    public string $relativeUrl;
    public string $filename;
    public string $extension;
    public string $originalName;
    public UploadedFile $file;
    public Strategy $strategy;

    /**
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

        if ($baseUri && 'local' !== $driver) {
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
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return \json_encode($this->toArray());
    }

    /**
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
