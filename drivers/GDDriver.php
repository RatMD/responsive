<?php declare(strict_types=1);

namespace RatMD\Responsive\Drivers;

use RatMD\Responsive\Contracts\GraphicDriver;

class GDDriver implements GraphicDriver
{

    /**
     * GD Format Number identifiers
     *
     * @var array
     */
    const IMAGE_FORMATS = [
        0x01    => 'gif',
        0x02    => 'jpeg',
        0x03    => 'png',
        0x06    => 'bmp',
        0x09    => 'jpeg',
        0x12    => 'webp',
        0x13    => 'avif'
    ];

    /**
     * Supported Image Formats
     *
     * @var array|null
     */
    static protected ?array $formats = null;

    /**
     * Boolean state if the driver is supported
     *
     * @return boolean
     */
    static public function isSupported(): bool
    {
        return extension_loaded('gd');
    }

    /**
     * An array of supported image formats.
     *
     * @return array
     */
    static public function getSupportedFormats(): array
    {
        if (self::$formats !== null) {
            return self::$formats;
        }

        $formats = [];

        $keys = ['avif', 'bmp', 'gif', 'jpeg', 'png', 'webp'];
        foreach ($keys AS $key) {
            if (function_exists("imagecreatefrom{$key}")) {
                $formats[] = $key;
            }
        }

        return self::$formats = $formats;
    }


    /**
     * Current Image Source Path
     *
     * @var string
     */
    protected string $source;

    /**
     * Current Image Width
     *
     * @var string|null
     */
    protected ?int $width = null;

    /**
     * Current Image Height
     *
     * @var string|null
     */
    protected ?int $height = null;

    /**
     * Current Image Format
     *
     * @var string|null
     */
    protected ?string $format = null;

    /**
     * Create a new GD driver instance
     *
     * @param string $source
     */
    public function __construct(string $source)
    {
        $this->source = $source;
    }

    /**
     * Get image dimensions or null when unknown or invalid.
     *
     * @return array|null
     */
    public function getDimensions(): ?array
    {
        if ($this->width !== null) {
            return [$this->width, $this->height];
        }

        [$width, $height, $format] = getimagesize($this->source);
        $this->width = $width;
        $this->height = $height;
        $this->format = self::IMAGE_FORMATS[$format] ?? null;
        return [$width, $height];
    }

    /**
     * Get image format or null, when unknown.
     *
     * @return string|null
     */
    public function getFormat(): string|null
    {
        if ($this->format !== null) {
            return $this->format;
        }

        [$width, $height, $format] = getimagesize($this->source);
        $this->width = $width;
        $this->height = $height;
        $this->format = self::IMAGE_FORMATS[$format] ?? null;
        return $this->format;
    }

    /**
     * Convert image
     *
     * @param string $dest
     * @param array $options
     * @return boolean
     */
    public function convert(string $dest, array $options): bool
    {
        if (($format = $this->getFormat()) === null) {
            return false;
        }

        // Open Image
        $source = null;
        switch ($format) {
            case 'gif':
                $source = imagecreatefromgif($this->source);
                break;
            case 'jpeg':
                $source = imagecreatefromjpeg($this->source);
                break;
            case 'png':
                $source = imagecreatefrompng($this->source);
                break;
            case 'bmp':
                $source = imagecreatefrombmp($this->source);
                break;
            case 'webp':
                $source = imagecreatefromwebp($this->source);
                break;
            case 'avif':
                $source = imagecreatefromavif($this->source);
                break;
        }
        if (empty($source)) {
            return false;
        }

        // Resize Image
        if (!empty($options['width']) && !empty($options['height'])) {
            [$width, $height] = $this->getDimensions();
            $image = imagecreatetruecolor($options['width'], $options['height']);
            imagecopyresampled($image, $source, 0, 0, 0, 0, $options['width'], $options['height'], $width, $height);
            $source = $image;
        }

        // Store Image
        $status = false;
        switch ($options['format'] ?? $format) {
            case 'gif':
                $status = imagegif($source, $dest);
                break;
            case 'jpeg':
                $status = imagejpeg($source, $dest, $options['quality'] ?? -1);
                break;
            case 'png':
                $status = imagepng($source, $dest, $options['quality'] ?? -1);
                break;
            case 'bmp':
                $status = imagebmp($source, $dest);
                break;
            case 'webp':
                $status = imagewebp($source, $dest, $options['quality'] ?? 80);
                break;
            case 'avif':
                $status = imageavif($source, $dest, $options['quality'] ?? -1);
                break;
        }
        return $status && file_exists($dest);
    }

}
