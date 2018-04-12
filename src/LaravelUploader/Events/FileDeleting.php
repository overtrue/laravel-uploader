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

class FileDeleting
{
    public $file;

    /**
     * Create a new event instance.
     *
     * @param Illuminate\Http\UploadedFile $file
     */
    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }
}
