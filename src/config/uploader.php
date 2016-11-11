<?php

/*
 * This file is part of the laravel-uploader.
 *
 * (c) 2016 overtrue <i@overtrue.me>
 */

return [
    'strategies' => [
        'default' => [
            'file' => [
                'mimes' => ['application/pdf'],
                'storeage' => 'local', // dist from config/filesystems.php
                'directory' => '', // directory
                'max_file_size' => '5m',
            ],
            'image' => [
                'mimes' => ['image/jpeg', 'image/png', 'image/bmp', 'image/gif'],
                'storeage' => 'public',
                'directory' => '', // directory,
                'max_file_size' => '2m',
                'crop' => [
                    'strategy' => 'crop', // crop, resize, resizeCanvas, fit
                    'sizes' => [
                        'small' => [200, 200],
                        'medium' => [800, 800],
                        'large' => [1200, 1200],
                    ],
                ],
            ],
        ],

        // avatar
        'avatar' => [
            'image' => [
                'directory' => 'avatars',
            ],
        ],
    ],
];

// @uploader('file', ['strategy' => 'avatar', 'data' => [$product->images]])
