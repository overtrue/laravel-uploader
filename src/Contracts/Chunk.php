<?php

namespace Overtrue\LaravelUploader\Contracts;

use Illuminate\Http\UploadedFile;

interface Chunk
{
    public function getIndex(): int;
    public function isLast(): bool;
    public function getChunkFile(): UploadedFile;
    public function getFileSize(): int;
    public function getFileOriginalName(): string;
}
