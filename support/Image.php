<?php declare(strict_types=1);

namespace RatMD\Responsive\Support;

class Image
{

    /**
     * Supported File Formats -> File Extensions
     *
     * @var array
     */
    static public array $formats = [
        'avif'  => [
            'mime_type'     => 'image/avif',
            'extensions'    => ['avif']
        ],
        'bmp'   => [
            'mime_type'     => 'image/bmp',
            'extensions'    => ['bmp', 'dib'],
        ],
        'gif'   => [
            'mime_type'     => 'image/gif',
            'extensions'    => ['gif']
        ], 
        'jpeg'  => [
            'mime_type'     => 'image/jpeg',
            'extensions'    => ['jpeg', 'jpg', 'jpe', 'jif', 'jfif', 'jfi']
        ],
        'png'   => [
            'mime_type'     => 'image/png',
            'extensions'    => ['png']
        ],
        'webp'  => [
            'mime_type'     => 'image/webp',
            'extensions'    => ['webp']
        ],
    ];

    /**
     * Get File Extension by Image Format
     *
     * @param string $format
     * @return string|null
     */
    static public function getExtension(string $format): ?string
    {
        if (!array_key_exists($format, self::$formats)) {
            return null;
        } else {
            return self::$formats[$format]['extensions'][0];
        }
    }

    /**
     * Get MIME Type from Format
     *
     * @param string $format
     * @return string|null
     */
    static public function getMimeType(string $format): ?string
    {
        if (!array_key_exists($format, self::$formats)) {
            return null;
        } else {
            return self::$formats[$format]['mime_type'];
        }
    }

    /**
     * Get Image Format by File Extension
     *
     * @param string $extension
     * @return string|null
     */
    static public function getFormatByExtension(string $extension): ?string
    {
        if (!empty(self::$formats[$extension])) {
            return $extension;
        }
        foreach (self::$formats AS $format => $data) {
            if (in_array($extension, $data['extensions'])) {
                return $format;
            }
        }
        return null;
    }

    /**
     * Resize image with aspect ratio.
     *
     * @param integer $origWidth
     * @param integer $origHeight
     * @param integer $maxWidth
     * @param integer $maxHeight
     * @return array
     */
    static public function resizeWithAspectRatio(int $origWidth, int $origHeight, int $maxWidth = 0, int $maxHeight = 0): array
    {
        $maxWidth = $maxWidth <= 0 ? $origWidth : $maxWidth;
        $maxHeight = $maxHeight <= 0 ? $origHeight : $maxHeight;

        $origRatio = $origWidth/$origHeight;
        if ($maxWidth/$maxHeight > $origRatio) {
           $maxWidth = $maxHeight * $origRatio;
        } else {
           $maxHeight = $maxWidth / $origRatio;
        }

        return [intval(round($maxWidth)), intval(round($maxHeight))];
    }

}
