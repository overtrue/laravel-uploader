<?php

namespace Overtrue\LaravelUploader\Events;

use Overtrue\LaravelUploader\Chunk;
use Overtrue\LaravelUploader\Result;
use Overtrue\LaravelUploader\Strategy;

class ChunkUploaded
{
    public function __construct(
        public Chunk $chunk,
        public Result $result,
        public Strategy $strategy
    ) {
    }
}
