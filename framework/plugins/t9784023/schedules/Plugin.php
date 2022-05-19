<?php namespace T9784023\Schedules;

use Backend;
use System\Classes\PluginBase;

/**
 * Schedules Plugin Information File
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
            'name'        => 'Schedules',
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
            'T9784023\Schedules\Components\MyComponent' => 'myComponent',
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
            't9784023.schedules.some_permission' => [
                'tab' => 'Schedules',
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
            'schedules' => [
                'label'       => 'Schedules',
                'url'         => Backend::url('t9784023/schedules/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['t9784023.schedules.*'],
                'order'       => 500,
            ],
        ];
    }
}
