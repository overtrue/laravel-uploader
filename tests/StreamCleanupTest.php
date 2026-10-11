<?php

namespace Tests;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Overtrue\LaravelUploader\Events\FileUploaded;
use Overtrue\LaravelUploader\Events\FileUploading;
use Overtrue\LaravelUploader\Strategy;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Throwable;

class StreamCleanupTest extends TestCase
{
    public static function failurePoints(): array
    {
        return [['storage'], ['response'], ['listener']];
    }

    #[DataProvider('failurePoints')]
    public function test_exception_identity_and_stream_cleanup_at_each_failure_point(string $failurePoint): void
    {
        $exception = new RuntimeException($failurePoint.' failed');
        $stream = null;
        $events = [];
        $disk = Mockery::mock(FilesystemAdapter::class);
        Storage::shouldReceive('disk')->with('public')->andReturn($disk);
        $disk->shouldReceive('put')->once()->andReturnUsing(function ($path, $input) use (&$stream, $failurePoint, $exception) {
            $stream = $input;
            if ($failurePoint === 'storage') {
                throw $exception;
            }

            return true;
        });
        if ($failurePoint === 'response') {
            $disk->shouldReceive('url')->once()->andThrow($exception);
        } elseif ($failurePoint === 'listener') {
            $disk->shouldReceive('url')->once()->andReturn('/storage/files/pixel.png');
        }
        Event::listen(FileUploading::class, function () use (&$events) {
            $events[] = 'uploading';
        });
        Event::listen(FileUploaded::class, function () use (&$events, $failurePoint, $exception) {
            $events[] = 'uploaded';
            if ($failurePoint === 'listener') {
                throw $exception;
            }
        });
        $strategy = new Strategy(['disk' => 'public', 'directory' => 'files', 'filename_type' => 'original'], $this->pngFile());
        $caught = null;

        try {
            $strategy->upload();
        } catch (Throwable $error) {
            $caught = $error;
        }

        // Retaining the handle verifies explicit cleanup even while an exception is retained.
        $wasOpen = is_resource($stream);
        if ($wasOpen) {
            fclose($stream);
        }
        $this->assertSame($exception, $caught);
        $this->assertSame($failurePoint === 'listener' ? ['uploading', 'uploaded'] : ['uploading'], $events);
        $this->assertFalse($wasOpen, "Stream remains open after {$failurePoint} throws");
    }

    public static function storageOutcomes(): array
    {
        return [['success'], ['exception']];
    }

    #[DataProvider('storageOutcomes')]
    public function test_storage_may_already_have_closed_the_stream(string $outcome): void
    {
        $exception = new RuntimeException('storage failed after closing');
        $disk = Mockery::mock();
        Storage::shouldReceive('disk')->with('public')->andReturn($disk);
        $disk->shouldReceive('put')->once()->andReturnUsing(function ($path, $stream) use ($outcome, $exception) {
            fclose($stream);
            if ($outcome === 'exception') {
                throw $exception;
            }

            return true;
        });
        $strategy = new Strategy(['disk' => 'public', 'directory' => 'files', 'filename_type' => 'original'], $this->pngFile());
        if ($outcome === 'exception') {
            $this->expectExceptionObject($exception);
        }

        $response = $strategy->upload();

        $this->assertSame('files/pixel.png', $response->path);
    }
}
