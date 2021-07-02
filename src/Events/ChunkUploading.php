<?php

namespace Overtrue\LaravelUploader\Events;

use Overtrue\LaravelUploader\Chunk;
use Overtrue\LaravelUploader\Strategy;

class ChunkUploading
{
    public function __construct(
        public Chunk $chunk,
        public Strategy $strategy
    ) {
    }
}
