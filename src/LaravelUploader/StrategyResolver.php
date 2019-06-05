<?php


namespace Overtrue\LaravelUploader;

/**
 * Class StrategyResolver
 */
class StrategyResolver
{
    /**
     * @param string|null $name
     *
     * @return \Overtrue\LaravelUploader\Strategy
     */
    public static function resolve(string $name = null)
    {
        $config = static::recursiveMergeConfig(config('uploader.strategies.default', []), config('uploader.strategies.'.$name, []));

        return new Strategy($config);
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
                $merged[$key] = $this->recursiveMergeConfig($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

}