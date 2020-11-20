<?php

namespace Overtrue\LaravelUploader\Events;

use Illuminate\Http\UploadedFile;

class FileDeleted
{
    public UploadedFile $file;

    /**
     * @param \Illuminate\Http\UploadedFile $file
     */
    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }
}
