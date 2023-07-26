<?php declare(strict_types=1);

namespace RatMD\Responsive;

use October\Rain\Exception\ApplicationException;
use RatMD\Responsive\Classes\ResponsiveHandler;
use RatMD\Responsive\Models\Setting;
use Response;
use System\Classes\PluginBase;

/**
 * Plugin Information File
 *
 * @link https://docs.octobercms.com/3.x/extend/system/plugins.html
 */
class Plugin extends PluginBase
{

    /**
     * Details about this plugin.
     * 
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'          => 'ratmd.responsive::lang.plugin.name',
            'description'   => 'ratmd.responsive::lang.plugin.description',
            'author'        => 'RatMD',
            'icon'          => 'icon-picture-o',
            'homepage'      => 'https://www.rat.md'
        ];
    }

    /**
     * Register Plugin
     * 
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Boot Plugin
     * 
     * @return void
     * 
     * @throws ApplicationException The output path %s for the responsive images could not be created.
     */
    public function boot()
    {
        $output = storage_path('/app/media/responsive-files');
        if (!file_exists($output)) {
            if (@mkdir($output, 0777, true) === false) {
                throw new ApplicationException("The output path '$output' for the responsive images could not be created.");
            }
        }
    }

    /**
     * Register Plugin Settings
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'ratmd_responsive_settings' => [
                'label'         => 'ratmd.responsive::lang.settings.menu.label',
                'description'   => 'ratmd.responsive::lang.settings.menu.description',
                'category'      => 'CATEGORY_CMS',
                'keywords'      => 'responsive images convert',
                'icon'          => 'icon-picture-o',
                'class'         => Setting::class
            ]
        ];
    }

    /**
     * Register Twig Markups
     *
     * @return array
     */
    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'convert'       => function () {
                    $handler = new ResponsiveHandler();
                    return $handler->convert(...func_get_args());
                }
            ],
            'functions' => [
                'picture'       => function () {
                    $handler = new ResponsiveHandler();

                    $args = func_get_args();
                    if (func_num_args() === 1 && is_array($args[0])) {
                        return $handler->picture(
                            $args[0]['image'], 
                            $args[0]['breakpoints'],
                            $args[0]['formats'] ?? [],
                            $args[0]['attributes'] ?? []
                        );
                    } else {
                        return $handler->picture(...func_get_args());
                    }
                }
            ]
        ];
    }

}
