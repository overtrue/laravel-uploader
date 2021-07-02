<?php

namespace Overtrue\LaravelUploader;

use Illuminate\Http\Request;

class StrategyResolver
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param string|null              $name
     *
     * @return \Overtrue\LaravelUploader\Strategy
     */
    public static function resolveFromRequest(Request $request, string $name = null): Strategy
    {
        $config = \array_replace_recursive(
            config('uploader.strategies.default', []),
            config("uploader.strategies.{$name}", [])
        );

        return new Strategy($config);
    }
}
