<?php

/*
 * This file is part of the overtrue/laravel-uploader.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelUploader\Events;

use Illuminate\Http\UploadedFile;
use Overtrue\LaravelUploader\Response;
use Overtrue\LaravelUploader\Strategy;

class FileUploaded
{
    public $file;

    /**
     * The result of the uploaded file.
     *
     * @var \Overtrue\LaravelUploader\Response
     */
    public $response;

    /**
     * The strategy of the uploaded file.
     *
     * @var \Overtrue\LaravelUploader\Strategy
     */
    public $strategy;

    /**
     * The config of strategy.
     *
     * @var array
     */
    public $config;

    /**
     * Create a new event instance.
     *
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
