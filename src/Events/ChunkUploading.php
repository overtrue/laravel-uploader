<?php

namespace Overtrue\LaravelUploader\Events;

use Illuminate\Http\UploadedFile;
use Overtrue\LaravelUploader\Strategy;

class ChunkUploading
{
    public function __construct(
        public UploadedFile $chunk,
        public Strategy $strategy
    ) {
    }
}
