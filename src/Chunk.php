<?php

namespace Overtrue\LaravelUploader;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;
use Overtrue\LaravelUploader\Contracts\Chunk as ChunkInterface;

class Chunk implements ChunkInterface
{
    public function __construct(
        protected UploadedFile $file,
        protected string $originalName,
        protected int $index,
        protected bool $isLast
    ) {
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function isLast(): bool
    {
        return $this->isLast();
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function getFileOriginalName(): string
    {
        return $this->originalName;
    }

    public function getChunksId(): string
    {
        if (Session::isStarted()) {
            return Session::getId();
        }

        //todo:
    }
}
