<?php

namespace Overtrue\LaravelUploader;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;
use Overtrue\LaravelUploader\Contracts\Chunk as ChunkInterface;

class Chunk implements ChunkInterface
{
    public function __construct(
        protected UploadedFile $chunkFile,
        protected string $fileOriginalName,
        protected int $totalSize,
        protected int $index,
        protected int $count,
    ) {
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getChunkFile(): UploadedFile
    {
        return $this->chunkFile;
    }

    public function getTotalSize(): int
    {
        return $this->totalSize;
    }

    public function getFileOriginalName(): string
    {
        return $this->fileOriginalName;
    }

    public function getChunksId(): string
    {
        $id = \join([$this->getFileOriginalName(), $this->getTotalSize()]);

        if (Session::isStarted()) {
            return \md5(Session::getId().$id);
        }

        if (\auth()->check()) {
            return \md5(\auth()->id().$id);
        }

        return \md5($id);
    }

    public function getChunksCount(): int
    {
        return $this->count;
    }
}
