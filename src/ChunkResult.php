<?php

namespace Overtrue\LaravelUploader;

class ChunkResult extends Result
{
    public function __construct(
        public string $disk,
        public string $path,
        public Chunk $chunk,
        public int $percentage
    ) {
        parent::__construct($disk, $path, $this->chunk->getFile());
    }
}
