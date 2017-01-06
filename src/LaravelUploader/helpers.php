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
        return array_merge_recursive(config('uploader.strategies.default'), config('uploader.strategies.'.$strategy, []));
    }
}