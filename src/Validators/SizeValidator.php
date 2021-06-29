<?php

namespace Overtrue\LaravelUploader\Validators;

use Illuminate\Http\UploadedFile;
use Overtrue\LaravelUploader\Contracts\Strategy;
use Overtrue\LaravelUploader\Contracts\Validator;

class SizeValidator implements Validator
{
    public function validate(UploadedFile $uploadedFile, Strategy $strategy): bool
    {
        return $strategy->getMaxSize() >= $uploadedFile->getSize();
    }

    public function getMessage(): string
    {
        return 'File to large.';
    }
}
