<?php

namespace Overtrue\LaravelUploader;

use Illuminate\Http\UploadedFile;
use Overtrue\LaravelUploader\Contracts\Result as ResultInterface;
use Overtrue\LaravelUploader\Contracts\Strategy;

class Result implements ResultInterface
{
    public function __construct(public UploadedFile $file, public string $path, public Strategy $strategy)
    {
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function getStrategy(): Strategy
    {
        return $this->strategy;
    }
}
