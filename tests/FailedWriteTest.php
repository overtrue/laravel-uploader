<?php

namespace Tests;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToWriteFile;
use Mockery;
use Overtrue\LaravelUploader\Events\FileUploaded;
use Overtrue\LaravelUploader\Events\FileUploading;
use Overtrue\LaravelUploader\Strategy;
use PHPUnit\Framework\Attributes\DataProvider;
use Throwable;

class FailedWriteTest extends TestCase
{
    public function test_false_write_throws_without_constructing_a_response_or_dispatching_success(): void
    {
        Event::fake([FileUploading::class, FileUploaded::class]);
        $stream = null;
        $disk = Mockery::mock();
        // A Response would request the disk a second time.
        $diskRequests = 0;
        Storage::shouldReceive('disk')->with('public')->andReturnUsing(function () use ($disk, &$diskRequests) {
            $diskRequests++;

            return $disk;
        });
        $disk->shouldReceive('put')->once()->andReturnUsing(function ($path, $input) use (&$stream) {
            $stream = $input;

            return false;
        });
        $strategy = new Strategy(['disk' => 'public', 'directory' => 'files', 'filename_type' => 'original'], $this->pngFile());
        $caught = null;

        try {
            $strategy->upload();
        } catch (Throwable $exception) {
            $caught = $exception;
        }

        $wasOpen = is_resource($stream);
        if ($wasOpen) {
            fclose($stream);
        }
        $this->assertInstanceOf(UnableToWriteFile::class, $caught);
        $this->assertSame('files/pixel.png', $caught->location());
        $this->assertFalse($wasOpen);
        $this->assertSame(1, $diskRequests);
        Event::assertDispatchedTimes(FileUploading::class, 1);
        Event::assertNotDispatched(FileUploaded::class);
    }

    public static function throwingDisks(): array
    {
        return [['throw' => false], ['throw' => true]];
    }

    #[DataProvider('throwingDisks')]
    public function test_real_local_directory_collision_throws_and_never_reports_success(bool $throw): void
    {
        Event::fake([FileUploading::class, FileUploaded::class]);
        $disk = Storage::fake('public', ['throw' => $throw]);
        $disk->makeDirectory('files/pixel.png');
        $strategy = new Strategy(['disk' => 'public', 'directory' => 'files', 'filename_type' => 'original'], $this->pngFile());
        $caught = null;

        try {
            $strategy->upload();
        } catch (Throwable $exception) {
            $caught = $exception;
        }

        $this->assertInstanceOf(UnableToWriteFile::class, $caught);
        $this->assertSame('files/pixel.png', $caught->location());
        $this->assertTrue($disk->directoryExists('files/pixel.png'));
        $this->assertFalse($disk->fileExists('files/pixel.png'));
        Event::assertDispatchedTimes(FileUploading::class, 1);
        Event::assertNotDispatched(FileUploaded::class);
    }
}
