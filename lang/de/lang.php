<?php declare(strict_types=1);

return [
    'plugin' => [
        'name' => 'Responsive Bilder',
        'description' => 'Biete unterschiedliche responsive Größen und Formate deiner Bilder an.'
    ],

    'settings' => [
        'menu' => [
            'label' => 'Responsive Bilder',
            'description' => 'Konfiguriere die Optionen für die Erstellung deiner responsiven Bilder.',
        ],

        'tabs' => [
            'general' => 'Allgemein',
            'details' => 'Details'
        ],

        'options' => [
            'default_driver' => 'Standard Grafik-Treiber',
            'default_driver_comment' => 'Der Standard Treiber wird nur dann benutzt, wenn er sowohl Eingabe- wie auch Ausgabeformat unterstützt.',
            'hash_filenames' => 'Mit gehashtem Dateinamen speichern',
            'hash_filenames_comment' => 'Speichere die generierten Dateien mit der gehashten Version des Dateinamens.',
        ],

        'details' => [
            'driver_support' => 'Grafik-Treiber Unterstützung',
            'extension_installed' => 'PHP-Erweiterung installiert',
            'avif_support' => 'AVIF Unterstützung',
            'bmp_support' => 'BMP Unterstützung',
            'gif_support' => 'GIF Unterstützung',
            'jpeg_support' => 'JPEG Unterstützung',
            'png_support' => 'PNG Unterstützung',
            'webp_support' => 'WEBP Unterstützung'
        ]
    ]
];
