<?php

namespace Tests;

use Illuminate\Http\Request;
use Overtrue\LaravelUploader\StrategyResolver;
use PHPUnit\Framework\Attributes\DataProvider;

class MimeOverrideTest extends TestCase
{
    public static function overrides(): array
    {
        return [
            'absent inherits defaults' => [[], ['image/jpeg', 'image/png', 'image/bmp', 'image/gif'], true],
            'shorter replaces defaults' => [['mimes' => ['image/jpeg']], ['image/jpeg'], false],
            'empty rejects everything' => [['mimes' => []], [], false],
            'wildcard allows everything' => [['mimes' => ['*']], ['*'], true],
            'full replacement' => [['mimes' => ['text/plain', 'image/gif', 'image/bmp', 'image/png']], ['text/plain', 'image/gif', 'image/bmp', 'image/png'], true],
        ];
    }

    #[DataProvider('overrides')]
    public function test_strategy_mime_override_is_an_atomic_allow_list(array $override, array $expected, bool $valid): void
    {
        config(['uploader.strategies.custom' => $override]);
        $request = Request::create('/files/upload', 'POST', [], [], ['file' => $this->pngFile()]);
        $strategy = StrategyResolver::resolveFromRequest($request, 'custom');

        $this->assertSame($expected, $strategy->getMimes());
        $this->assertSame($valid, $strategy->isValidMime());
        $this->assertSame(config('uploader.strategies.default.directory'), $strategy->getDirectory());
    }

    public function test_wildcard_override_accepts_a_type_not_in_the_default_list(): void
    {
        config(['uploader.strategies.custom.mimes' => ['*']]);
        $request = Request::create('/files/upload', 'POST', [], [], ['file' => $this->uploadedFile()]);

        $this->assertTrue(StrategyResolver::resolveFromRequest($request, 'custom')->isValidMime());
    }
}
