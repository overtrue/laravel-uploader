<?php

namespace Overtrue\LaravelUploader;

class ChunkResult extends Result
{
    public function __construct(
        public Chunk $chunk,
        public string $path,
        public Strategy $strategy,
        public int $percentage
    ) {
        parent::__construct($chunk->getChunkFile(), $path, $strategy);
    }
}
