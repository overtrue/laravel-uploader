<?php

namespace Overtrue\LaravelUploader\Drivers;

use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class Single extends Driver
{
    public function getFile(Request $request, Repository $config): UploadedFile
    {
        return $request->file($config->get('form_name'));
    }
}
