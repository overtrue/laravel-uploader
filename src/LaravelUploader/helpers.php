<?php

if (!function_exists('uploader_strategy')) {
    /**
     * Get uploader strategy config.
     *
     * @param  string $strategy
     *
     * @return array
     */
    function uploader_strategy($strategy)
    {
        return array_merge([
            'filters' => [],
        ], array_merge_recursive_distinct(config('uploader.strategies.default', []), config('uploader.strategies.'.$strategy, [])));
    }
}

if (!function_exists('array_merge_recursive_distinct')) {
    /**
     * Array merge recursive distinct.
     *
     * @param  array  &$array1
     * @param  array  &$array2
     *
     * @return array
     */
    function array_merge_recursive_distinct(array $array1, array $array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged [$key]) && is_array($merged [$key])) {
                $merged[$key] = array_merge_recursive_distinct($merged [$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}