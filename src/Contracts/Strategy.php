<?php

namespace Overtrue\LaravelUploader\Contracts;

use Illuminate\Http\UploadedFile;

interface Strategy
{
    public function getFormName(): string;
    public function getDisks(): array;
    public function getAllowedMimes(): array;
    public function getMaxSize(): int;
    public function getChunkCountKey(): string;
    public function getChunkIndexKey(): string;
    public function getChunkDisk(): string;
    public function getChunkMaxSize(): int;
    public function getPath(UploadedFile $uploadedFile): string;
}
