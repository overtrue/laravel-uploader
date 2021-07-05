<?php

namespace Tests;

use Illuminate\Http\UploadedFile;
use Overtrue\LaravelUploader\Drivers\Single;
use Overtrue\LaravelUploader\Strategy;

class StrategyTest extends TestCase
{
    public function test_it_has_default_config()
    {
        $strategy = new Strategy([]);

        \config(['filesystems.default' => 'local']);

        $this->assertSame('file', $strategy->getFormName());
        $this->assertSame('local', $strategy->getDisk());
        $this->assertSame('local', $strategy->getChunkDisk());
        $this->assertSame(Single::class, $strategy->getDriver());
        $this->assertSame(2 * 1024 * 1024, $strategy->getMaxSize());
        $this->assertSame(['image/jpeg', 'image/png', 'image/bmp', 'image/gif'], $strategy->getAllowedMimes());
    }

    public function test_it_can_init_with_array_config()
    {
        $strategy = new Strategy(
            [
                'form_name' => 'avatar',
                'max_size' => '1m',
                'filename_type' => 'random',
                'disk' => 'local',
                'allowed_mimes' => ['image/jpeg'],
                'chunk_disk' => 'chunk',
                'driver' => 'chunk',
            ]
        );

        $this->assertSame('avatar', $strategy->getFormName());
        $this->assertSame('local', $strategy->getDisk());
        $this->assertSame('chunk', $strategy->getChunkDisk());
        $this->assertSame('chunk', $strategy->getDriver());
        $this->assertSame(1024 * 1024, $strategy->getMaxSize());
        $this->assertSame(['image/jpeg'], $strategy->getAllowedMimes());
    }

    public function test_it_can_generate_file_storage_path()
    {
        $strategy = new Strategy(
            [
                'filename_type' => 'original',
                'prefix' => '/foo/{Y}/{m}/{d}/{H}/{i}/{s}',
            ]
        );

        $file = new UploadedFile(__DIR__ . '/stubs/text.md', 'text.md', null, null, true);

        $this->assertSame(sprintf('/foo/%s/text.md', date('Y/m/d/H/i/s')), $strategy->getStoragePath($file));


        // md5_file
        $strategy = new Strategy(
            [
                'filename_type' => 'md5_file',
                'prefix' => '/foo/{Y}/{m}/{d}/{H}/{i}/{s}',
            ]
        );

        $this->assertSame(
            sprintf('/foo/%s/%s.md', date('Y/m/d/H/i/s'), \md5_file(__DIR__ . '/stubs/text.md')),
            $strategy->getStoragePath($file)
        );

        // hash
        $strategy = new Strategy(
            [
                'filename_type' => 'hash',
                'prefix' => '/foo/{Y}/{m}/{d}/{H}/{i}/{s}',
            ]
        );

        $this->assertMatchesRegularExpression(
            sprintf('~/foo/%s/[a-z0-9]{40}.txt~i', date('Y/m/d/H/i/s')),
            $strategy->getStoragePath($file)
        );
    }
}
