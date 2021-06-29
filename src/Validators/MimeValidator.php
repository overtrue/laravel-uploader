<?php

namespace Overtrue\LaravelUploader\Validators;

use Illuminate\Http\UploadedFile;
use Overtrue\LaravelUploader\Contracts\Strategy;
use Overtrue\LaravelUploader\Contracts\Validator;

class MimeValidator implements Validator
{
    public function validate(UploadedFile $uploadedFile, Strategy $strategy): bool
    {
        return $strategy->getAllowedMimes() === ['*'] || \in_array($uploadedFile->getClientMimeType(), $strategy->getAllowedMimes());
    }

    public function getMessage(): string
    {
        return 'Invalid file type.';
    }
}
