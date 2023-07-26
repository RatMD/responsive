<?php declare(strict_types=1);

namespace RatMD\Responsive\Contracts;

interface GraphicDriver
{

    /**
     * Boolean state if the driver is supported
     *
     * @return boolean
     */
    static public function isSupported(): bool;

    /**
     * An array of supported image formats.
     *
     * @return array
     */
    static public function getSupportedFormats(): array;

    /**
     * Get image dimensions or null when unknown or invalid.
     *
     * @return array|null
     */
    public function getDimensions(): ?array;

    /**
     * Get image format or null, when unknown.
     *
     * @return string|null
     */
    public function getFormat(): ?string;

    /**
     * Convert image
     *
     * @param string $dest
     * @param array $options
     * @return bool
     */
    public function convert(string $dest, array $options): bool;

}
