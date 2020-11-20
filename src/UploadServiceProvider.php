<?php

namespace Overtrue\LaravelUploader;

use Illuminate\Support\ServiceProvider;

class UploadServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadConfig();
        $this->loadTranslations();
    }

    protected function loadConfig()
    {
        $this->publishes([
            __DIR__.'/../config/uploader.php' => config_path('uploader.php'),
        ]);
    }

    protected function loadTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'uploader');

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/uploader'),
        ]);
    }
}
