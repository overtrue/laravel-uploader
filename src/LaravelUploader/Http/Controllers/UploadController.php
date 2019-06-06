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

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Overtrue\LaravelUploader\StrategyResolver;

/**
 * class UploadController.
 */
class UploadController extends BaseController
{
    /**
     * Handle file upload.
     *
     * @param Request $request
     *
     * @return \Overtrue\LaravelUploader\Response
     */
    public function __invoke(Request $request)
    {
        return StrategyResolver::resolveFromRequest($request, $request->get('strategy', 'default'))->upload();
    }
}
