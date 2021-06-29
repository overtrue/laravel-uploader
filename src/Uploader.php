<?php

namespace Overtrue\LaravelUploader;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Facade;

class Uploader extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'uploader';
    }

    /**
     * @param array $options
     *
     * @throws BindingResolutionException
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
