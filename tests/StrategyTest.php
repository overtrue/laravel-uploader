<?php

namespace Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Overtrue\LaravelUploader\Events\FileUploaded;
use Overtrue\LaravelUploader\Events\FileUploading;
use Overtrue\LaravelUploader\Strategy;
use Overtrue\LaravelUploader\StrategyResolver;
use PHPUnit\Framework\Attributes\DataProvider;

class StrategyTest extends TestCase
{
    public function test_resolver_inherits_defaults_and_applies_scalar_overrides(): void
    {
        $file = $this->pngFile();
        $request = Request::create('/files/upload', 'POST', [], [], ['file' => $file]);
        $strategy = StrategyResolver::resolveFromRequest($request, 'avatar');

        $this->assertSame($file, $strategy->getFile());
        $this->assertSame('file', $strategy->getName());
        $this->assertSame('public', $strategy->getDisk());
        $this->assertSame('avatars/{Y}/{m}/{d}', $strategy->getDirectory());
        $this->assertSame(config('uploader.strategies.default.mimes'), $strategy->getMimes());
        $this->assertSame(2 * 1024 * 1024, $strategy->getMaxSize());
        $this->assertSame(md5_file($file->getRealPath()).'.png', $strategy->getFilename());
    }

    public function test_original_and_md5_filenames_are_based_on_the_uploaded_file(): void
    {
        $file = $this->uploadedFile('deterministic bytes', 'report.TXT');
        $original = new Strategy(['directory' => 'files', 'filename_type' => 'original'], $file);
        $md5 = new Strategy(['directory' => 'files', 'filename_type' => 'md5_file'], $file);

        $this->assertSame('report.TXT', $original->getFilename());
        $this->assertSame(md5('deterministic bytes').'.TXT', $md5->getFilename());
        $this->assertSame(config('filesystems.default'), $md5->getDisk());
    }

    public function test_random_filename_uses_the_files_cached_hash_name(): void
    {
        $file = $this->pngFile();
        $strategy = new Strategy(['directory' => 'files', 'filename_type' => 'random'], $file);

        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]{40}\.png$/', $strategy->getFilename());
        $this->assertSame($file->hashName(), $strategy->getFilename());
        $this->assertSame($strategy->getFilename(), $strategy->getFilename());
    }

    public function test_upload_formats_every_date_token_and_trims_directory_slashes(): void
    {
        Storage::fake('public');
        $file = $this->pngFile();
        $strategy = new Strategy([
            'disk' => 'public',
            'directory' => 'uploads/{Y}/{m}/{d}/{H}/{i}/{s}///',
            'filename_type' => 'original',
        ], $file);
        $before = 'uploads/'.date('Y/m/d/H/i/s').'/pixel.png';
        $response = $strategy->upload();
        $after = 'uploads/'.date('Y/m/d/H/i/s').'/pixel.png';

        $this->assertContains($response->path, [$before, $after]);
        $this->assertSame(file_get_contents($file->getRealPath()), Storage::disk('public')->get($response->path));
    }

    public static function sizeLimits(): array
    {
        return [
            'unlimited' => [0, 2049, true],
            'empty file' => [1, 0, true],
            'one byte at limit' => [1, 1, true],
            'one byte over limit' => [1, 2, false],
            'kilobyte at limit' => ['1K', 1024, true],
            'kilobyte over limit' => ['1K', 1025, false],
            'fractional kilobyte at limit' => ['1.5KB', 1536, true],
            'fractional kilobyte over limit' => ['1.5KB', 1537, false],
        ];
    }

    #[DataProvider('sizeLimits')]
    public function test_size_boundaries_use_actual_uploaded_bytes($limit, int $size, bool $valid): void
    {
        $file = $this->uploadedFile(str_repeat('x', $size));
        $strategy = new Strategy(['directory' => 'files', 'max_size' => $limit], $file);

        $this->assertSame($size, $file->getSize());
        $this->assertSame($valid, $strategy->isValidSize());
    }

    public static function mimeLists(): array
    {
        return [
            'wildcard' => [['*'], true],
            'allowed' => [['image/jpeg', 'image/png'], true],
            'rejected' => [['image/jpeg'], false],
            'empty' => [[], false],
        ];
    }

    #[DataProvider('mimeLists')]
    public function test_mime_allow_list(array $mimes, bool $valid): void
    {
        $strategy = new Strategy(['directory' => 'files', 'mimes' => $mimes], $this->pngFile());

        $this->assertSame($valid, $strategy->isValidMime());
    }

    public function test_upload_events_have_the_expected_payload_and_order(): void
    {
        Storage::fake('public');
        $file = $this->pngFile();
        $strategy = new Strategy(['disk' => 'public', 'directory' => 'files', 'filename_type' => 'original'], $file);
        $events = [];
        Event::listen(FileUploading::class, function ($event) use (&$events, $file) {
            $this->assertSame($file, $event->file);
            Storage::disk('public')->assertMissing('files/pixel.png');
            $events[] = $event;
        });
        Event::listen(FileUploaded::class, function ($event) use (&$events, $file, $strategy) {
            $this->assertSame($file, $event->file);
            $this->assertSame($strategy, $event->strategy);
            $this->assertSame(file_get_contents($file->getRealPath()), Storage::disk('public')->get('files/pixel.png'));
            $events[] = $event;
        });

        $response = $strategy->upload();

        $this->assertCount(2, $events);
        $this->assertInstanceOf(FileUploading::class, $events[0]);
        $this->assertInstanceOf(FileUploaded::class, $events[1]);
        $this->assertSame($response, $events[1]->response);
    }

    public function test_upload_passes_a_readable_stream_and_options_then_closes_it(): void
    {
        $file = $this->uploadedFile('actual streamed bytes');
        $strategy = new Strategy(['disk' => 'uploads', 'directory' => 'files', 'filename_type' => 'original'], $file);
        $stream = null;
        $options = ['visibility' => 'private'];
        $disk = Mockery::mock();
        Storage::shouldReceive('disk')->with('uploads')->twice()->andReturn($disk);
        $disk->shouldReceive('put')->once()->withArgs(function ($path, $input, $actualOptions) use (&$stream, $options) {
            $stream = $input;
            $this->assertSame('files/example.txt', $path);
            $this->assertIsResource($input);
            $this->assertSame('actual streamed bytes', stream_get_contents($input));
            $this->assertSame($options, $actualOptions);

            return true;
        })->andReturn(true);

        $response = $strategy->upload($options);

        $this->assertSame('files/example.txt', $response->path);
        $this->assertFalse(is_resource($stream));
    }
}
