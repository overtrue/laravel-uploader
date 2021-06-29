<?php

namespace Overtrue\LaravelUploader\Events;

use Illuminate\Http\UploadedFile;
use Overtrue\LaravelUploader\Result;
use Overtrue\LaravelUploader\Strategy;

class FileUploaded
{
    public function __construct(
        public UploadedFile $file,
        public Strategy $strategy,
        public Result $result,
    ) {
    }
}
