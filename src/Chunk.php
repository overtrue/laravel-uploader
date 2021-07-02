<?php

namespace Overtrue\LaravelUploader;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;
use Overtrue\LaravelUploader\Contracts\Chunk as ChunkInterface;

class Chunk implements ChunkInterface
{
    public function __construct(
        protected UploadedFile $chunkFile,
        protected string $originalName,
        protected int $fileSize,
        protected int $index,
        protected bool $isLast,
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

    public function getChunkFile(): UploadedFile
    {
        return $this->chunkFile;
    }

    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    public function getFileOriginalName(): string
    {
        return $this->originalName;
    }

    public function getChunksId(): string
    {
        $id = \join([$this->getFileOriginalName(), $this->getFileSize()]);

        if (Session::isStarted()) {
            return \md5(Session::getId().$id);
        }

        if (\auth()->check()) {
            return \md5(\auth()->id().$id);
        }

        return \md5($id);
    }
}
