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
}
