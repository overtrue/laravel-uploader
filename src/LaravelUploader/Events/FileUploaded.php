<?php

/*
 * This file is part of the laravel-uploader.
 *
 * (c) 2016 overtrue <i@overtrue.me>
 */

namespace Overtrue\LaravelUploader\Events;

use Illuminate\Http\UploadedFile;

class FileUploaded
{
    public $file;

    /**
     * The result of the uploaded file.
     * 
     * @var array
     */
    public $result;

    /**
     * Create a new event instance.
     * 
     * @param Illuminate\Http\UploadedFile $file
     * @param array $result
     */
    public function __construct(UploadedFile $file, array $result)
    {
        $this->file = $file;
        $this->result = $result;
    }
}
