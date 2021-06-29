<?php

namespace Overtrue\LaravelUploader;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\UploadedFile;
use JetBrains\PhpStorm\Pure;

class ChunkResult implements Jsonable, Arrayable
{
    public array $disks;
    public string $path;
    public string $size;
    public UploadedFile $file;
    public Strategy $strategy;

    public function __construct(string $path, Strategy $strategy, UploadedFile $file)
    {
        $this->path = $path;
        $this->file = $file;
        $this->strategy = $strategy;
        $this->size = $file->getSize();
    }

    public function toJson($options = 0): string
    {
        return \json_encode($this->toArray());
    }

    #[Pure]
    public function toArray(): array
    {
        return [
            'size' => $this->size,
            'path' => $this->path,
            'strategy' => $this->strategy->getFormName(),
        ];
    }
}
