<?php

namespace Overtrue\LaravelUploader\Events;

use Illuminate\Http\UploadedFile;
use Overtrue\LaravelUploader\Strategy;

class FileUploading
{
    public function __construct(
        public UploadedFile $file,
        public Strategy $strategy
    ) {
    }
}
