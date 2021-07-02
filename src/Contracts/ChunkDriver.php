<?php

namespace Overtrue\LaravelUploader\Contracts;

use Illuminate\Config\Repository;
use Illuminate\Http\Request;

interface ChunkDriver extends Driver
{
    public function isChunkRequest(Request $request): bool;
    public function getChunk(Request $request, Repository $config): Chunk;
}
