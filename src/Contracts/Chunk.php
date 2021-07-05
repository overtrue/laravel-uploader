<?php

namespace Overtrue\LaravelUploader\Contracts;

use Illuminate\Http\UploadedFile;

interface Chunk
{
    public function getIndex(): int;
    public function getChunkFile(): UploadedFile;
    public function getChunksCount(): int;
    public function getTotalSize(): int;
    public function getFileOriginalName(): string;
}
