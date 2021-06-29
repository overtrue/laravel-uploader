<?php

namespace Overtrue\LaravelUploader\Events;

use Illuminate\Http\UploadedFile;
use Overtrue\LaravelUploader\Result;
use Overtrue\LaravelUploader\Strategy;

class ChunkUploaded
{
    public function __construct(
        public UploadedFile $chunk,
        public Result $response,
        public Strategy $strategy
    ) {
    }
}
