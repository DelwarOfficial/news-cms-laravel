<?php

return [
    'driver' => env('IMAGE_DRIVER', 'gd'),
    'webp_quality' => (int) env('IMAGE_WEBP_QUALITY', 80),
    'thumbnail_sizes' => [
        'small' => [150, 150],
        'medium' => [400, 300],
        'large' => [800, 600],
    ],
];
