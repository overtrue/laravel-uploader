<?php

namespace Overtrue\LaravelUploader\Drivers;

use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use Overtrue\LaravelUploader\Chunk as ChunkObject;
use Overtrue\LaravelUploader\Contracts\ChunkDriver as ChunkDriverInterface;

class Chunk extends Driver implements ChunkDriverInterface
{
    public function isChunkRequest(Request $request): bool
    {
        return $request->has('chunk_index')
            && $request->has('chunk_count')
            && $request->has('original_name');
    }

    public function getChunk(Request $request, Repository $config): \Overtrue\LaravelUploader\Contracts\Chunk
    {
        return new ChunkObject(
            $this->getFile($request, $config),
            $this->getFileSize($request),
            $this->getCurrentIndex($request),
            $this->getChunksCount($request),
            $this->getFileOriginalName($request),
        );
    }

    public function getCurrentIndex(Request $request): int
    {
        return \intval($request->get('chunk_index'));
    }

    public function getFileOriginalName(Request $request)
    {
        return $request->get('original_name');
    }

    public function getFileSize(Request $request)
    {
        return $request->get('total_size');
    }

    public function getChunksCount(Request $request)
    {
        return $request->get('chunk_count');
    }
}
