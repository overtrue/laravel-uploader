<?php

namespace Overtrue\LaravelUploader\Drivers;

use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Overtrue\LaravelUploader\Contracts\ChunkDriver as ChunkDriverInterface;
use Overtrue\LaravelUploader\Contracts\Driver as HandlerInterface;
use Overtrue\LaravelUploader\Contracts\Result;

abstract class Driver implements HandlerInterface
{
    public function getFile(Request $request, Repository $config): UploadedFile
    {
        return $request->file($config->get('form_name'));
    }

    public function resolveResult(Result $result): array
    {
        return [
            'disk' => $result->getDisk(),
            'path' => $result->getPath(),
            'filename' => \basename($result->getPath()),
            'original_name' => $result->getFile()->getClientOriginalName()
        ];
    }

    public function supportChunkUpload(): bool
    {
        return $this instanceof ChunkDriverInterface;
    }
}
