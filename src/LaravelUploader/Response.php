<?php


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
        $this->path = $path;
        $this->file = $file;
        $this->strategy = $strategy;

        $disk = Storage::disk($this->strategy->getDisk());
        $this->filename = \basename($path);
        $this->originalName = $file->getClientOriginalName();
        $this->mime = $file->getClientMimeType();
        $this->size = $file->getSize();
        $this->relativeUrl = str_replace(config('app.url'), '', $disk->url($path));
        $this->url = $disk->url($this->path);
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
            'filename' => $this->filename,
            'relative_url' => $this->relativeUrl,
            'original_name' => $this->originalName,
        ];
    }
}