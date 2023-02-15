<?php

namespace Overtrue\LaravelUploader\Events;

use Illuminate\Http\UploadedFile;

class FileDeleting
{
    public UploadedFile $file;

    /**
     * Create a new event instance.
     */
    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }
}
