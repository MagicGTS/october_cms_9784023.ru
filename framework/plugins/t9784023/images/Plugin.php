<?php namespace T9784023\Images;

use Backend;
use System\Classes\PluginBase;

/**
 * Images Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Images',
            'description' => 'No description provided yet...',
            'author'      => 'T9784023',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'T9784023\Images\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any backend permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            't9784023.images.some_permission' => [
                'tab' => 'Images',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers backend navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'images' => [
                'label'       => 'Images',
                'url'         => Backend::url('t9784023/images/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['t9784023.images.*'],
                'order'       => 500,
            ],
        ];
    }
}
