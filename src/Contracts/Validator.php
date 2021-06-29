<?php

namespace Overtrue\LaravelUploader\Contracts;

use Illuminate\Http\UploadedFile;

interface Validator
{
    public function validate(UploadedFile $uploadedFile, Strategy $strategy): bool;
    public function getMessage(): string;
}
