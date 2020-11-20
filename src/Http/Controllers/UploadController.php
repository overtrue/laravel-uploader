<?php

namespace Overtrue\LaravelUploader\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Overtrue\LaravelUploader\StrategyResolver;

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
