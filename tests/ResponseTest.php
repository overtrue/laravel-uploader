<?php

namespace Tests;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Overtrue\LaravelUploader\Response;
use Overtrue\LaravelUploader\Strategy;

class ResponseTest extends TestCase
{
    public function test_response_properties_array_and_json_preserve_public_types(): void
    {
        Storage::fake('public');
        $file = $this->uploadedFile('five!', 'report.txt');
        $strategy = new Strategy(['disk' => 'public', 'name' => 'document', 'directory' => 'files'], $file);
        $response = new Response('files/report.txt', $strategy, $file);
        $expected = [
            'mime' => 'text/plain',
            'size' => '5',
            'path' => 'files/report.txt',
            'url' => '/storage/files/report.txt',
            'disk' => 'public',
            'filename' => 'report.txt',
            'extension' => 'txt',
            'relative_url' => '/files/report.txt',
            'location' => '/storage/files/report.txt',
            'original_name' => 'report.txt',
            'strategy' => 'document',
        ];

        $this->assertInstanceOf(Arrayable::class, $response);
        $this->assertInstanceOf(Jsonable::class, $response);
        $this->assertSame($expected, $response->toArray());
        $this->assertSame($expected, json_decode($response->toJson(), true, 512, JSON_THROW_ON_ERROR));
        $this->assertSame($file, $response->file);
        $this->assertSame($strategy, $response->strategy);
        $this->assertSame('5', $response->size);
        $this->assertSame('text/plain', $response->mime);
        $this->assertSame('report.txt', $response->originalName);
        $this->assertSame('/files/report.txt', $response->relativeUrl);
    }

    public function test_remote_disk_base_uri_is_normalized_without_network_access(): void
    {
        $disk = Mockery::mock();
        Storage::shouldReceive('disk')->once()->with('remote')->andReturn($disk);
        config([
            'filesystems.disks.remote.driver' => 's3',
            'uploader.base_uri' => 'https://cdn.example.test/assets///',
        ]);
        $file = $this->pngFile();
        $strategy = new Strategy(['disk' => 'remote', 'directory' => 'files'], $file);

        $response = new Response('files/pixel.png', $strategy, $file);

        $this->assertSame('https://cdn.example.test/assets/files/pixel.png', $response->url);
        $this->assertSame($response->url, $response->toArray()['location']);
    }

    public function test_disk_url_is_used_when_no_base_uri_is_configured(): void
    {
        Storage::fake('public', ['url' => 'https://files.example.test']);
        $file = $this->pngFile();
        $strategy = new Strategy(['disk' => 'public', 'directory' => 'files'], $file);

        $response = new Response('files/pixel.png', $strategy, $file);

        $this->assertSame('https://files.example.test/files/pixel.png', $response->url);
    }

    public function test_local_disk_ignores_remote_base_uri(): void
    {
        Storage::fake('public', ['url' => 'https://local.example.test/storage']);
        config(['uploader.base_uri' => 'https://cdn.example.test/assets']);
        $file = $this->pngFile();
        $strategy = new Strategy(['disk' => 'public', 'directory' => 'files'], $file);

        $response = new Response('files/pixel.png', $strategy, $file);

        $this->assertSame('https://local.example.test/storage/files/pixel.png', $response->url);
        $this->assertSame($response->url, $response->toArray()['location']);
    }

    public function test_remote_disk_uses_its_url_when_base_uri_is_empty(): void
    {
        $disk = Mockery::mock(FilesystemAdapter::class);
        Storage::shouldReceive('disk')->once()->with('remote')->andReturn($disk);
        $disk->shouldReceive('url')->once()->with('files/pixel.png')->andReturn('https://remote.example.test/files/pixel.png');
        config(['filesystems.disks.remote.driver' => 's3', 'uploader.base_uri' => '']);
        $file = $this->pngFile();
        $strategy = new Strategy(['disk' => 'remote', 'directory' => 'files'], $file);

        $this->assertSame('https://remote.example.test/files/pixel.png', (new Response('files/pixel.png', $strategy, $file))->url);
    }

    public function test_disk_without_url_support_falls_back_to_application_url(): void
    {
        Storage::shouldReceive('disk')->once()->with('remote')->andReturn(new \stdClass);
        config(['filesystems.disks.remote.driver' => 'custom', 'uploader.base_uri' => '']);
        $file = $this->pngFile();
        $strategy = new Strategy(['disk' => 'remote', 'directory' => 'files'], $file);

        $this->assertSame('http://localhost/files/pixel.png', (new Response('files/pixel.png', $strategy, $file))->url);
    }
}
