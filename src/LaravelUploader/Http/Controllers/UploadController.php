<?php

/*
 * This file is part of the laravel-uploader.
 *
 * (c) 2016 overtrue <i@overtrue.me>
 */

namespace Overtrue\LaravelUploader\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Event;
use Overtrue\LaravelUploader\Events\FileDeleted;
use Overtrue\LaravelUploader\Events\FileUploaded;
use Overtrue\LaravelUploader\Events\FileUploading;
use Overtrue\LaravelUploader\Services\FileUpload;

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
        $strategy = $request->get('strategy', 'default');
        $config = uploader_strategy($strategy);

        $directory = array_get($config, 'directory', '');
        $disk = array_get($config, 'disk', 'public');

        Event::fire(new FileUploading($request->file));

        $result = app(FileUpload::class)->store($request->file, $disk, $directory);

        Event::fire(new FileUploaded($request->file));

        return $result;
    }

    /**
     * Delete file.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $result = ['result' => app(FileUpload::class)->delete($request->file)];

        Event::fire(new FileDeleted($request->file));

        return $result;
    }
}
