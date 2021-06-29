<?php

namespace Overtrue\LaravelUploader;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\Pure;

class Result implements Jsonable, Arrayable
{
    public array $disks;
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

    public function __construct(string $path, Strategy $strategy, UploadedFile $file)
    {
        $disk = Storage::disk($strategy->getDisks()[0]);
        $baseUri = rtrim(config('uploader.base_uri'), '/');
        $driver = config('filesystems.disks.%s.driver', $strategy->getDisks());
        $relativeUrl = \sprintf('/%s', \ltrim($path, '/'));
        $url = url($path);

        if ($baseUri && 'local' !== $driver) {
            $url = \sprintf('%s/%s', $baseUri, $path);
        } elseif (method_exists($disk, 'url')) {
            $url = $disk->url($path);
        }

        $this->path = $path;
        $this->file = $file;
        $this->disks = $strategy->getDisks();
        $this->strategy = $strategy;
        $this->filename = \basename($path);
        $this->extension = $file->getClientOriginalExtension();
        $this->originalName = $file->getClientOriginalName();
        $this->mime = $file->getClientMimeType();
        $this->size = $file->getSize();
        $this->url = $url;
        $this->relativeUrl = $relativeUrl;
    }

    public function toJson($options = 0): string
    {
        return \json_encode($this->toArray());
    }

    #[Pure]
    public function toArray(): array
    {
        return [
            'mime' => $this->mime,
            'size' => $this->size,
            'path' => $this->path,
            'url' => $this->url,
            'disk' => $this->disks,
            'filename' => $this->filename,
            'extension' => $this->extension,
            'relative_url' => $this->relativeUrl,
            'location' => $this->url,
            'original_name' => $this->originalName,
            'strategy' => $this->strategy->getFormName(),
        ];
    }
}
