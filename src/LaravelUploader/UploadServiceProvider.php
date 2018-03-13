<?php

/*
 * This file is part of the overtrue/laravel-uploader.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelUploader;

use Illuminate\Support\Facades\Blade;
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
        $this->loadViews();
        $this->loadAssets();
        $this->loadTranslations();
        $this->registerViewDirective();
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
     * Load views.
     */
    protected function loadViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'uploader');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/uploader'),
        ]);
    }

    /**
     * Load assets.
     */
    protected function loadAssets()
    {
        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/uploader'),
        ], 'public');
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

    /**
     * Register view directive.
     */
    protected function registerViewDirective()
    {
        Blade::directive('uploader', function ($expression) {
            $parts = explode(',', trim($expression, '()'));
            $data = count($parts) > 1 ? implode(',', $parts) : '[]';
            $template = 'uploader::'.trim(array_shift($parts), '"\'');

            return "<?php echo \$__env->make('{$template}', array_merge(array_except(get_defined_vars(), array('__data', '__path')), (array)$data))->render(); ?>";
        });
    }
}
