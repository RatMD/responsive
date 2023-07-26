<?php declare(strict_types=1);

namespace RatMD\Responsive\Classes;

use RatMD\Responsive\Contracts\GraphicDriver;
use RatMD\Responsive\Drivers\GDDriver;
use RatMD\Responsive\Drivers\GMagickDriver;
use RatMD\Responsive\Drivers\IMagickDriver;
use RatMD\Responsive\Exceptions\HandlerException;
use RatMD\Responsive\Models\Setting;
use RatMD\Responsive\Support\Image;

class ResponsiveHandler
{

    /**
     * Supported Drivers
     *
     * @var array|null
     */
    static protected ?array $drivers = null;

    /**
     * Supported Formats / Driver
     *
     * @var array|null
     */
    static protected ?array $formats = null;

    /**
     * Get supported graphics drivers
     *
     * @return array
     */
    static public function getSupportedDrivers()
    {
        if (self::$drivers !== null) {
            return self::$drivers;
        }

        self::$drivers = [];
        if (GDDriver::isSupported()) {
            self::$drivers['gd'] = GDDriver::class;
        }
        if (GMagickDriver::isSupported()) {
            self::$drivers['gmagick'] = GMagickDriver::class;
        }
        if (IMagickDriver::isSupported()) {
            self::$drivers['imagick'] = IMagickDriver::class;
        }

        return self::$drivers;
    }


    /**
     * Used driver name
     *
     * @var string
     */
    protected string $driverName;

    /**
     * Used driver class string
     *
     * @var string
     */
    protected string $driver;

    /**
     * Create a new ResponsiveHandler class
     * 
     * @param string|null $driver
     */
    public function __construct(?string $driver = null)
    {
        $drivers = self::getSupportedDrivers();
        if (empty($drivers)) {
            throw new HandlerException('No graphic driver found, please install either GD, Gmagick or Imagick.');
        }

        $driver = is_null($driver) ? Setting::get('hash_filenames', key($drivers)) : $driver;
        if (empty($drivers[$driver])) {
            throw new HandlerException("The passed graphic driver '{$driver}' is not supported by this OctoberCMS Plugin.");
        }

        $this->driverName = $driver;
        $this->driver = $drivers[$driver];
    }

    /**
     * Get local file path from image URL
     *
     * @param string $url
     * @return string
     * 
     * @throws HandlerException The passed image source %s does not exist.
     */
    protected function getPathFromURL(string $url): string
    {
        $host = request()->host();

        // Remove Protocol
        if (str_starts_with($url, 'http')) {
            $url = substr($url, str_starts_with($url, 'https') ? 8 : 7);
        }

        // Remove origin host
        if (str_starts_with($url, $host)) {
            $url = substr($url, strlen($host)+1);
        }

        // Check Filepath
        $path = base_path($url);
        if (!file_exists($path)) {
            throw new HandlerException("The passed image path '$path' does not exist.");
        }

        return $path;
    }

    /**
     * Select a Graphics driver which supports a specific input and/or output format.
     *
     * @param string $input
     * @param string $output
     * @return string|null
     */
    protected function getDriverSupporting(string $input, ?string $output = null): ?string
    {
        $drivers = self::getSupportedDrivers();

        foreach ($drivers AS $driver) {
            $formats = $driver::getSupportedFormats();

            if (in_array($input, $formats) && ($output === null || in_array($output, $formats))) {
                return $driver;
            }
        }
        
        return null;
    }

    /**
     * Instantiate graphic driver
     *
     * @param string $driver
     * @param string $source
     * @return GraphicDriver
     */
    protected function instantiateDriver(string $driver, string $source): GraphicDriver
    {
        return new $driver($source);
    }

    /**
     * Convert image
     *
     * @param string $url
     * @param array $options
     * @return string
     * 
     * @throws HandlerException No graphic driver found which supports the image format %s.
     * @throws HandlerException Empty conversion, please set either a different size and / or a different format.
     * @throws HandlerException The image output format %s is not supported (yet).
     * @throws HandlerException No graphic driver found which supports the input format %s and the output format %s.
     * @throws HandlerException The passed image could not be converted.
     */
    public function convert(string $url, array $options = []): string
    {
        $source = $this->getPathFromURL($url);
        $extension = pathinfo($source, \PATHINFO_EXTENSION);
        $filename = substr(basename($source), 0, -(strlen($extension)+1));
        
        // Select Driver which supports input format
        $inputFormat = Image::getFormatByExtension($extension);
        if (in_array($inputFormat, $this->driver::getSupportedFormats())) {
            $driver = $this->driver;
        } else {
            $driver = $this->getDriverSupporting($inputFormat);
        }
        if (is_null($driver)) {
            throw new HandlerException("No graphic driver found which supports the image format '$inputFormat'.");
        }

        // Check conversion options
        $width = $options['width'] ?? 0;
        $height = $options['height'] ?? 0;
        $format = $options['format'] ?? null;
        if ($width <= 0 && $height <= 0 && $format === null) {
            throw new HandlerException("Empty conversion, please set either a different size and / or a different format.");
        }

        $options = [];

        // Check output format
        if (!empty($format)) {
            if (($extension = Image::getExtension($format)) === null) {
                throw new HandlerException("The image output format '$format' is not supported (yet).");
            }

            if (!in_array($format, $driver::getSupportedFormats())) {
                $driver = $this->getDriverSupporting($inputFormat, $format);
            }

            if (is_null($driver)) {
                throw new HandlerException("No graphic driver found which supports the input format '$inputFormat' and the output format '$format'.");
            }
            $options['format'] = $format;
        }

        // Pass Image to driver
        $image = $this->instantiateDriver($driver, $source);

        // Change File Dimensions
        if ($width > 0 || $height > 0) {
            [$origWidth, $origHeight] = $image->getDimensions();
            [$width, $height] = Image::resizeWithAspectRatio($origWidth, $origHeight, $width, $height);

            $options['width'] = $width;
            $options['height'] = $height;
            $filename .= "_{$width}x{$height}";
        }
        $filename .= ".{$extension}";

        // Check and Create file
        if (Setting::get('hash_filenames', true)) {
            $filename = hash('sha256', $filename) . ".{$extension}";
        }
        $path = '/app/media/responsive-files/' . $filename;
        $filepath = storage_path($path);
        if (!file_exists($filepath)) {
            if (!$image->convert($filepath, $options)) {
                throw new HandlerException('The passed image could not be converted.');
            }
        }

        // Return URL
        $basePath = trim(substr(storage_path('/'), strlen(base_path('/'))-1), '/\\');
        return url($basePath . '/' . str_replace('\\', '/', trim($path, '/\\')));
    }

    /**
     * Generate Picture
     *
     * @return void
     */
    public function picture(string $url, array $breakpoints, array $formats = [], array $attributes = [])
    {
        krsort($breakpoints);

        // Build source-set
        $sources = [];
        foreach ($breakpoints AS $breakpoint => $options) {
            $attrs = [];

            if ($breakpoint > 0) {
                $attrs[] = 'media="(min-width: '. $breakpoint .'px)"';
            }

            if (!empty($formats)) {
                foreach ($formats AS $format) {
                    if (empty($mimeType = Image::getMimeType($format))) {
                        continue;
                    }
                    $attrs2 = $attrs;
                    $options2 = $options;
                    $options2['format'] = $format;

                    $srcset = $this->convert($url, $options2);
                    $attrs2[] = 'type="'. $mimeType .'"';
                    $sources[] = '<source srcset="'. $srcset .'" '. implode(' ', $attrs2) .' />';
                }
            } else {
                $srcset = $this->convert($url, $options);
                $sources[] = '<source srcset="'. $srcset .'" '. implode(' ', $attrs) .' />';
            }
        }
        
        // Build main attributes
        $attrs = [];
        $attrs[] = "src=\"$url\"";
        foreach ($attributes AS $key => $value) {
            if ($value === true) {
                $attrs[] = "$key";
            } else {
                $attrs[] = "$key=\"$value\"";
            }
        }

        // Generate output
        $output  = '<picture>' . "\n";
        $output .= implode("\n", $sources);
        $output .= '<img '. implode(' ', $attrs) .' />' . "\n";
        $output .= '</picture>';
        return $output;
    }

}
