<?php namespace T9784023\LearningPrograms;

use Backend;
use System\Classes\PluginBase;

/**
 * LearningProgram Plugin Information File
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
            'name'        => 'LearningPrograms',
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
            'T9784023\LearningPrograms\Components\MyComponent' => 'myComponent',
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
            't9784023.learningprograms.some_permission' => [
                'tab' => 'LearningPrograms',
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
            'learningprograms' => [
                'label'       => 'LearningPrograms',
                'url'         => Backend::url('t9784023/learningprograms/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['t9784023.learningprograms.*'],
                'order'       => 500,
            ],
        ];
    }
}
