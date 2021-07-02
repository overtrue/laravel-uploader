<?php

namespace Overtrue\LaravelUploader;

use Illuminate\Support\Manager;
use Overtrue\LaravelUploader\Drivers\Chunk as ChunkDriver;
use Overtrue\LaravelUploader\Drivers\Dropzone;
use Overtrue\LaravelUploader\Drivers\Plupload;
use Overtrue\LaravelUploader\Drivers\Single;

/**
 * @method \Overtrue\LaravelUploader\Contracts\Driver|\Overtrue\LaravelUploader\Contracts\ChunkDriver driver($driver = null)
 */
class DriverManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return 'single';
    }

    public function createSingleDriver(): Single
    {
        return new Single();
    }

    public function createChunkDriver(): ChunkDriver
    {
        return new ChunkDriver();
    }

    public function createPluploadDriver(): Plupload
    {
        return new Plupload();
    }

    public function createDropzoneDriver(): Dropzone
    {
        return new Dropzone();
    }
}
