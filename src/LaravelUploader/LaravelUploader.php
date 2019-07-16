<?php

/*
 * This file is part of the overtrue/laravel-uploader.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelUploader;

use Illuminate\Support\Facades\Facade;

/**
 * Class LaravelUploader.
 */
class LaravelUploader extends Facade
{
    /**
     * @param array $options
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function routes(array $options = [])
    {
        if (!self::$app->routesAreCached()) {
            self::$app->make('router')->post(
                'files/upload',
                \array_merge([
                    'uses' => '\Overtrue\LaravelUploader\Http\Controllers\UploadController',
                    'as' => 'file.upload',
                ], $options)
            );
        }
    }
}
