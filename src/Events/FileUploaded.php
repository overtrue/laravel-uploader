<?php

namespace Overtrue\LaravelUploader\Events;

use Illuminate\Http\UploadedFile;
use Overtrue\LaravelUploader\Response;
use Overtrue\LaravelUploader\Strategy;

class FileUploaded
{
    public UploadedFile $file;
    public Response $response;
    public Strategy $strategy;

    /**
     * @param \Illuminate\Http\UploadedFile      $file
     * @param \Overtrue\LaravelUploader\Response $response
     * @param \Overtrue\LaravelUploader\Strategy $strategy
     */
    public function __construct(UploadedFile $file, Response $response, Strategy $strategy)
    {
        $this->file = $file;
        $this->response = $response;
        $this->strategy = $strategy;
    }
}
