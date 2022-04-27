<?php namespace System\Classes;

use App;
use File;
use View;
use Config;

/**
 * AppBase class is an application level plugin
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class AppBase extends PluginBase
{
    /**
     * pluginDetails returns nothing because App is not a plugin.
     * @return array
     */
    public function pluginDetails()
    {
        return [];
    }

    /**
     * register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
        $appPath = app_path();
        $appNamespace = 'app';

        // Register configuration path
        $configPath = $appPath . '/config';
        if (File::isDirectory($configPath)) {
            Config::package($appNamespace, $configPath, $appNamespace);
        }

        // Register views path
        $viewsPath = $appPath . '/views';
        if (File::isDirectory($viewsPath)) {
            View::addNamespace($appNamespace, $viewsPath);
        }

        // Add init, if available
        $initFile = $appPath . '/init.php';
        if (File::exists($initFile)) {
            require $initFile;
        }

        // Add routes, if available
        $routesFile = $appPath . '/routes.php';
        if (File::exists($routesFile) && !App::routesAreCached()) {
            require $routesFile;
        }
    }

    /**
     * boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * getConfigurationFromYaml returns nothing because App doesn't support YAML config.
     */
    protected function getConfigurationFromYaml($exceptionMessage = null)
    {
        return [];
    }
}
