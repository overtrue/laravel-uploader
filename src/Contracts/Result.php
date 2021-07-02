<?php

namespace Overtrue\LaravelUploader\Contracts;

use Illuminate\Http\UploadedFile;

interface Result
{
    public function getStrategy(): Strategy;
    public function getPath(): string;
    public function getFile(): UploadedFile;
}
