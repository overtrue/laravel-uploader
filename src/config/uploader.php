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
            'directory' => 'uploads/{Y}/{m}/{d}', // directory,
            'max_file_size' => '2m',
            'filename_hash' => 'random', // random/md5_file/original
        ],

        // avatar extends default
        'avatar' => [
            'directory' => 'avatars/{Y}/{m}/{d}',
        ],
    ],
];

// @uploader('file', ['strategy' => 'avatar', 'data' => [$product->images]])
