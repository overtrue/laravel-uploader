<?php

namespace Tests;

use Illuminate\Http\UploadedFile;
use Orchestra\Testbench\TestCase as Orchestra;
use Overtrue\LaravelUploader\UploadServiceProvider;

abstract class TestCase extends Orchestra
{
    private array $temporaryFiles = [];

    protected function getPackageProviders($app)
    {
        return [UploadServiceProvider::class];
    }

    protected function defineEnvironment($app)
    {
        // The installation instructions require publishing this configuration.
        $app['config']->set('uploader', require __DIR__.'/../config/uploader.php');
        $app['config']->set('app.url', 'http://localhost');
    }

    protected function uploadedFile(string $contents = 'upload contents', string $name = 'example.txt', string $mime = 'text/plain'): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'laravel-uploader-');
        file_put_contents($path, $contents);
        $this->temporaryFiles[] = $path;

        return new UploadedFile($path, $name, $mime, UPLOAD_ERR_OK, true);
    }

    protected function pngFile(): UploadedFile
    {
        return $this->uploadedFile(
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wl6PpAAAAAASUVORK5CYII='),
            'pixel.png',
            'image/png'
        );
    }

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }

        parent::tearDown();
    }
}
