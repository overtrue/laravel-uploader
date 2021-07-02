<?php

namespace Overtrue\LaravelUploader\Drivers;

use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use Overtrue\LaravelUploader\Contracts\Chunk;
use Overtrue\LaravelUploader\Contracts\ChunkDriver as ChunkDriverInterface;

class Plupload extends Driver implements ChunkDriverInterface
{
    public function isChunkRequest(Request $request): bool
    {
        // TODO: Implement isChunkRequest() method.
    }

    public function getChunk(Request $request, Repository $config): Chunk
    {
        // TODO: Implement getChunk() method.
    }
}
