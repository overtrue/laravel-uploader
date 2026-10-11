<?php

namespace Tests;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Orchestra\Testbench\TestCase;
use Overtrue\LaravelUploader\LaravelUploader;
use Overtrue\LaravelUploader\UploadServiceProvider;

class ServiceProviderTest extends TestCase
{
    private string $publishDirectory;

    protected function getPackageProviders($app)
    {
        return [UploadServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return $this->packageManifest()['extra']['laravel']['aliases'];
    }

    protected function defineEnvironment($app)
    {
        $this->publishDirectory = sys_get_temp_dir().'/uploader-publish-'.bin2hex(random_bytes(8));
        mkdir($this->publishDirectory.'/config', 0777, true);
        mkdir($this->publishDirectory.'/resources', 0777, true);
        mkdir($this->publishDirectory.'/bootstrap/cache', 0777, true);
        $app->setBasePath($this->publishDirectory);
        $app->useConfigPath($this->publishDirectory.'/config');
    }

    public function test_provider_is_discoverable_and_loads_namespaced_translations(): void
    {
        $manifest = $this->packageManifest();
        $this->assertSame(['\\'.UploadServiceProvider::class], $manifest['extra']['laravel']['providers']);
        $this->assertTrue($this->app->getLoadedProviders()[UploadServiceProvider::class]);
        $this->assertSame('Start', trans('uploader::uploader.start'));
    }

    public function test_discovered_facade_alias_registers_the_upload_route(): void
    {
        $this->assertSame(['LaravelUploader' => '\\'.LaravelUploader::class], $this->packageManifest()['extra']['laravel']['aliases']);
        \LaravelUploader::routes(['as' => 'facade.upload']);
        $this->app['router']->getRoutes()->refreshNameLookups();
        $route = $this->app['router']->getRoutes()->getByName('facade.upload');

        $this->assertSame('files/upload', $route->uri());
        $this->assertSame(['POST'], $route->methods());
    }

    public function test_documented_publish_command_installs_configuration_and_translations(): void
    {
        $paths = ServiceProvider::pathsToPublish(UploadServiceProvider::class);
        $this->assertArrayHasKey(dirname(__DIR__).'/src/../config/uploader.php', $paths);
        $this->assertArrayHasKey(dirname(__DIR__).'/src/../resources/lang', $paths);
        $this->assertSame(config_path('uploader.php'), $paths[dirname(__DIR__).'/src/../config/uploader.php']);
        $this->assertSame(resource_path('lang/vendor/uploader'), $paths[dirname(__DIR__).'/src/../resources/lang']);

        $this->artisan('vendor:publish', ['--provider' => UploadServiceProvider::class])->assertSuccessful();

        $this->assertFileEquals(__DIR__.'/../config/uploader.php', config_path('uploader.php'));
        $this->assertFileEquals(__DIR__.'/../resources/lang/en/uploader.php', resource_path('lang/vendor/uploader/en/uploader.php'));
        // Published configuration is loaded by Laravel on the next application boot.
        $published = require config_path('uploader.php');
        $this->assertSame('uploads/{Y}/{m}/{d}', $published['strategies']['default']['directory']);
        $this->assertSame('avatars/{Y}/{m}/{d}', $published['strategies']['avatar']['directory']);
    }

    private function packageManifest(): array
    {
        return json_decode(file_get_contents(__DIR__.'/../composer.json'), true, 512, JSON_THROW_ON_ERROR);
    }

    protected function tearDown(): void
    {
        if (isset($this->publishDirectory)) {
            (new Filesystem)->deleteDirectory($this->publishDirectory);
        }

        parent::tearDown();
    }
}
