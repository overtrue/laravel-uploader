<?php


namespace Overtrue\LaravelUploader;

use Illuminate\Http\Request;

/**
 * Class StrategyResolver
 */
class StrategyResolver
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param string|null              $name
     *
     * @return \Overtrue\LaravelUploader\Strategy
     */
    public static function resolveFromRequest(Request $request, string $name = null)
    {
        $config = static::recursiveMergeConfig(
            config('uploader.strategies.default', []),
            config("uploader.strategies.{$name}", [])
        );

        return new Strategy($config, $request);
    }

    /**
     * Array merge recursive distinct.
     *
     * @param array &$array1
     * @param array &$array2
     *
     * @return array
     */
    protected static function recursiveMergeConfig(array $array1, array $array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = \forward_static_call(__FUNCTION__, $merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

}