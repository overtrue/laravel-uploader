<?php

namespace Tests;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Overtrue\LaravelUploader\Events\FileUploaded;
use Overtrue\LaravelUploader\Events\FileUploading;
use Overtrue\LaravelUploader\LaravelUploader;

class FeatureTest extends TestCase
{
    protected function defineRoutes($router)
    {
        LaravelUploader::routes();
    }

    public function test_registered_route_uploads_real_bytes_and_returns_metadata(): void
    {
        Storage::fake('public');
        $file = $this->pngFile();
        $contents = file_get_contents($file->getRealPath());
        $path = 'uploads/'.date('Y/m/d').'/'.md5($contents).'.png';
        $route = $this->app['router']->getRoutes()->getByName('file.upload');

        $this->assertSame(['POST'], $route->methods());
        $this->assertSame('files/upload', $route->uri());
        $this->postJson('/files/upload', ['file' => $file])
            ->assertOk()
            ->assertExactJson([
                'mime' => 'image/png',
                'size' => (string) strlen($contents),
                'path' => $path,
                'url' => '/storage/'.$path,
                'disk' => 'public',
                'filename' => md5($contents).'.png',
                'extension' => 'png',
                'relative_url' => '/'.$path,
                'location' => '/storage/'.$path,
                'original_name' => 'pixel.png',
                'strategy' => 'file',
            ]);

        Storage::disk('public')->assertExists($path);
        $this->assertSame($contents, Storage::disk('public')->get($path));
    }

    public function test_custom_strategy_uses_its_field_disk_directory_and_filename(): void
    {
        Storage::fake('avatars');
        config(['uploader.strategies.avatar' => [
            'name' => 'portrait',
            'disk' => 'avatars',
            'directory' => 'avatars',
            'filename_type' => 'original',
        ]]);
        $file = $this->pngFile();

        $this->postJson('/files/upload', ['strategy' => 'avatar', 'portrait' => $file])
            ->assertOk()
            ->assertJsonPath('path', 'avatars/pixel.png')
            ->assertJsonPath('disk', 'avatars')
            ->assertJsonPath('strategy', 'portrait');

        $this->assertSame(file_get_contents($file->getRealPath()), Storage::disk('avatars')->get('avatars/pixel.png'));
    }

    public function test_missing_custom_field_returns_422_without_writing_or_events(): void
    {
        Storage::fake('public');
        Event::fake([FileUploading::class, FileUploaded::class]);
        config(['uploader.strategies.avatar.name' => 'portrait']);

        $this->postJson('/files/upload', ['strategy' => 'avatar', 'file' => $this->pngFile()])
            ->assertStatus(422)
            ->assertJsonPath('message', 'No file "portrait" uploaded.');

        $this->assertSame([], Storage::disk('public')->allFiles());
        Event::assertNothingDispatched();
    }

    public function test_disallowed_mime_returns_422_without_writing_or_events(): void
    {
        Storage::fake('public');
        Event::fake([FileUploading::class, FileUploaded::class]);

        $this->postJson('/files/upload', ['file' => $this->uploadedFile()])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Invalid mime "text/plain".');

        $this->assertSame([], Storage::disk('public')->allFiles());
        Event::assertNothingDispatched();
    }

    public function test_oversized_file_returns_422_without_writing_or_events(): void
    {
        Storage::fake('public');
        Event::fake([FileUploading::class, FileUploaded::class]);
        config(['uploader.strategies.default.max_size' => 3]);
        $file = $this->uploadedFile('four', 'example.png', 'image/png');

        $this->postJson('/files/upload', ['file' => $file])
            ->assertStatus(422)
            ->assertJsonPath('message', 'File has too large size("4").');

        $this->assertSame([], Storage::disk('public')->allFiles());
        Event::assertNothingDispatched();
    }

    public function test_route_options_override_the_name_and_apply_middleware(): void
    {
        $this->app['router']->aliasMiddleware('upload-test', RejectUpload::class);
        LaravelUploader::routes(['as' => 'custom.upload', 'middleware' => ['upload-test']]);
        $this->app['router']->getRoutes()->refreshNameLookups();
        $route = $this->app['router']->getRoutes()->getByName('custom.upload');

        $this->assertSame(['upload-test'], $route->middleware());
        $this->postJson('/files/upload')->assertStatus(403)->assertJson(['blocked' => true]);
    }
}

class RejectUpload
{
    public function handle($request, $next)
    {
        return response()->json(['blocked' => true], 403);
    }
}
