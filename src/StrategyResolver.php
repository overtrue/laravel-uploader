<?php

namespace Overtrue\LaravelUploader;

use Illuminate\Http\Request;

class StrategyResolver
{
    /**
     * @return Strategy
     */
    public static function resolveFromRequest(Request $request, ?string $name = null)
    {
        $overrides = config("uploader.strategies.{$name}", []);
        $config = \array_replace_recursive(
            config('uploader.strategies.default', []),
            $overrides
        );

        if (array_key_exists('mimes', $overrides)) {
            $config['mimes'] = $overrides['mimes'];
        }

        $formName = $config['name'] ?? 'file';

        \abort_if(! $request->hasFile($formName), 422, \sprintf('No file "%s" uploaded.', $formName));

        return new Strategy($config, $request->file($formName));
    }
}
