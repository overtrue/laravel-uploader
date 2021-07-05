<?php

namespace Overtrue\LaravelUploader\Contracts;

use Illuminate\Config\Repository as ConfigInterface;
use Illuminate\Http\UploadedFile;

interface Strategy
{
    public function getDriver(): string;
    public function getConfig(): ConfigInterface;
    public function getFormName(): string;
    public function getDisk(): string;
    public function getAllowedMimes(): array;
    public function getMaxSize(): int;
    public function getChunkDisk(): string;
    public function getStoragePath(UploadedFile $uploadedFile): string;
}
