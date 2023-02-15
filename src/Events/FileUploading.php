<?php

namespace Overtrue\LaravelUploader\Events;

use Illuminate\Http\UploadedFile;

class FileUploading
{
    public UploadedFile $file;

    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }
}
