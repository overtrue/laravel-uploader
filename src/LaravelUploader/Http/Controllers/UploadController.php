<?php

/*
 * This file is part of the overtrue/laravel-uploader.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelUploader\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Overtrue\LaravelUploader\Events\FileDeleted;
use Overtrue\LaravelUploader\Events\FileUploaded;
use Overtrue\LaravelUploader\Events\FileUploading;
use Overtrue\LaravelUploader\Services\FileUpload;
use Overtrue\LaravelUploader\StrategyResolver;

/**
 * class UploadController.
 */
class UploadController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(config('uploader.middleware', []));
    }

    /**
     * Handle file upload.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $strategy = StrategyResolver::resolve($request->get('strategy', 'default'));

        if (!$request->hasFile($strategy->inputName)) {
            \abort(422, 'no file found.');
        }
        $file = $request->file($strategy->inputName);
        $mime = $file->getClientMimeType();

        if ($strategy->isValidMime($mime)) {
            \abort(422, \sprintf('Invalid mime "%s".', $mime));
        }

        Event::fire(new FileUploading($file));

        $filename = $strategy->getFilename($file);

        $result = app(FileUpload::class)->store($file, $strategy->disk, $filename, $strategy->directory);

        if (!is_null($modified = Event::fire(new FileUploaded($file, $result, $strategy), [], true))) {
            $result = $modified;
        }

        return $result;
    }

    /**
     * Delete file.
     *
     * @param Request $request
     *
     * @return array
     */
    public function delete(Request $request)
    {
        $result = ['result' => app(FileUpload::class)->delete($request->file)];

        Event::fire(new FileDeleted($request->file));

        return $result;
    }
}
