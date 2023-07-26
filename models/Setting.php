<?php declare(strict_types=1);

namespace RatMD\Responsive\Models;

use RatMD\Responsive\Classes\ResponsiveHandler;
use System\Models\SettingModel;

class Setting extends SettingModel
{

    /**
     * Settings Code
     */
    public $settingsCode = 'ratmd_responsive';

    /**
     * Settings Fields
     *
     * @var string
     */
    public $settingsFields = 'fields.yaml';

    /**
     * Get available drivers
     *
     * @return array
     */
    public function getDefaultDriverOptions(): array
    {
        $drivers = ResponsiveHandler::getSupportedDrivers();

        $options = [
            'gd'        => 'GD',
            'gmagick'   => 'GMagick (GraphicsMagick)',
            'imagick'   => 'IMagick (ImageMagick)'
        ];

        $availableOptions = array_intersect_key($options, $drivers);
        return $availableOptions;
    }

}
