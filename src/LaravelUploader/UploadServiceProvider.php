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

use Illuminate\Support\ServiceProvider;
use Overtrue\LaravelUploader\Services\FileUpload;

/**
 * Class UploadServiceProvider.
 */
class UploadServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->loadConfig();
        $this->loadTranslations();
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->registerServices();
    }

    /**
     * Register config.
     */
    protected function loadConfig()
    {
        $this->publishes([
            __DIR__.'/../config/uploader.php' => config_path('uploader.php'),
        ]);
    }

    /**
     * load translations.
     */
    protected function loadTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'uploader');

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/uploader'),
        ]);
    }

    /**
     * Register upload services.
     */
    protected function registerServices()
    {
        $this->app->singleton(FileUpload::class, function ($app) {
            return new FileUpload($app['filesystem']);
        });
    }
}
