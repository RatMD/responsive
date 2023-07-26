<?php declare(strict_types=1);

return [
    'plugin' => [
        'name' => 'Responsive Images',
        'description' => 'Provide different responsive sizes and formats of your pictures.'
    ],

    'settings' => [
        'menu' => [
            'label' => 'Responsive Images',
            'description' => 'Configure the settings used to generate your responsive images.',
        ],

        'tabs' => [
            'general' => 'General',
            'details' => 'Details'
        ],

        'options' => [
            'default_driver' => 'Default Graphic Driver',
            'default_driver_comment' => 'The default driver is only used, when it supports the input and output format.',
            'hash_filenames' => 'Store with hashed filename',
            'hash_filenames_comment' => 'Store the generated files using the hashed version of the filename.',
        ],

        'details' => [
            'driver_support' => 'Graphic-Driver Support',
            'extension_installed' => 'PHP-Extension installed',
            'avif_support' => 'AVIF Support',
            'bmp_support' => 'BMP Support',
            'gif_support' => 'GIF Support',
            'jpeg_support' => 'JPEG Support',
            'png_support' => 'PNG Support',
            'webp_support' => 'WEBP Support'
        ]
    ]
];
