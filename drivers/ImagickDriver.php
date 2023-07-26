<?php declare(strict_types=1);

namespace RatMD\Responsive\Drivers;

use RatMD\Responsive\Contracts\GraphicDriver;

class IMagickDriver implements GraphicDriver
{

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
        return class_exists('Imagick');
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
        $available = \Imagick::queryFormats();
        foreach ($keys AS $key) {
            if (in_array(strtoupper($key), $available) || ($key === 'jpeg' && in_array('JPG', $available))) {
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
     * Current Image Source Image
     *
     * @var ?\Imagick
     */
    protected ?\Imagick $image = null;

    /**
     * Current Image Format
     *
     * @var string|null
     */
    protected ?string $format = null;

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
     * Create a new IMagick driver instance
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

        if ($this->image === null) {
            $this->image = new \Imagick($this->source);
        }

        $this->width = $this->image->getImageWidth();
        $this->height = $this->image->getImageHeight();
        return [$this->width, $this->height];
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

        if ($this->image === null) {
            $this->image = new \Imagick($this->source);
        }
        
        $format = $this->image->getImageFormat();
        $format = $format === 'JPG' ? 'JPEG' : $format;

        $formats = self::getSupportedFormats();
        if (in_array($format, $formats)) {
            return $this->format = $format;
        } else {
            return null;
        }
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
        if ($this->image === null) {
            $this->image = new \Imagick($this->source);
        }
        $image = $this->image;

        // Resize Image
        if (!empty($options['width']) && !empty($options['height'])) {
            $image->resizeImage($options['width'], $options['height'], \Imagick::FILTER_LANCZOS, 1);
        }

        // Write Image
        $status = true;
        try {
            $status = $image->writeImage($dest);
        } catch (\ImagickException $e) {
            $status = false;
        }
        return $status && file_exists($dest);
    }

}
