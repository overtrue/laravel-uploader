<?php

namespace Overtrue\LaravelUploader\Contracts;

use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

interface Driver
{
    public function supportChunkUpload(): bool;
    public function getFile(Request $request, Repository $config): UploadedFile;
    public function resolveResult(Result $result): array;
}
