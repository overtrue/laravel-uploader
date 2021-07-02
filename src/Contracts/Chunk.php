<?php

namespace Overtrue\LaravelUploader\Contracts;

use Illuminate\Http\UploadedFile;

interface Chunk
{
    public function getIndex(): int;
    public function isLast(): bool;
    public function getFile(): UploadedFile;
    public function getFileOriginalName(): string;
}
