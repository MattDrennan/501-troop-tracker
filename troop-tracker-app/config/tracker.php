<?php

declare(strict_types=1);

return [
    'name' => env('TRACKER_NAME', 'UNKNWON'),
    'plugins' => [
        'type' => env('TRACKER_AUTH_PLUGIN', 'standalone'),
        'xenforo' => [
            'apiurl' => env('TRACKER_XENFORO_APIURL', ''),
            'key' => env('TRACKER_XENFORO_KEY', ''),
            'user' => env('TRACKER_XENFORO_USER', ''),
        ],
    ],
    'donate' => [
        'url' => env('TRACKER_DONATE_URL', 'https://example.com'),
    ],
    'forum' => [
        'name' => env('TRACKER_FORUM_NAME', 'Default Forum'),
        'url' => env('TRACKER_FORUM_URL', 'https://example.com'),
        'webmaster' => env('TRACKER_WEBMASTER', 'anyone@example.com'),
    ]
];