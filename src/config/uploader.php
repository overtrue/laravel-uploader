<?php

/*
 * This file is part of the laravel-uploader.
 *
 * (c) 2016 overtrue <i@overtrue.me>
 */

return [
    'strategies' => [
        /**
         * default strategy.
         */
        'default' => [
            'input_name' => 'file',
            'mimes' => ['image/jpeg', 'image/png', 'image/bmp', 'image/gif'],
            'disk' => 'public',
            'directory' => '{Y}/{m}/{d}', // directory,
            'max_file_size' => '2m',
        ],

        // avatar extends default
        'avatar' => [
            'directory' => 'avatars/{Y}/{m}/{d}',
        ],
    ],
];

// @uploader('file', ['strategy' => 'avatar', 'data' => [$product->images]])
