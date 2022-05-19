<?php namespace System\Classes;

use Backend;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider as ServiceProviderBase;
use ReflectionClass;
use SystemException;
use Yaml;

/**
 * PluginBase class
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class PluginBase extends ServiceProviderBase
{
    /**
     * @var array require plugin dependencies.
     */
    public $require = [];

    /**
     * @var boolean disabled determines if this plugin should be loaded (false) or not (true).
     */
    public $disabled = false;

    /**
     * @var bool loadedYamlConfiguration
     */
    protected $loadedYamlConfiguration = false;

    /**
     * pluginDetails returns information about this plugin, including plugin name and developer name.
     *
     * @return array
     * @throws SystemException
     */
    public function pluginDetails()
    {
        $thisClass = get_class($this);

        $configuration = $this->getConfigurationFromYaml(sprintf(
            'Plugin configuration file plugin.yaml is not '.
            'found for the plugin class %s. Create the file or override pluginDetails() '.
            'method in the plugin class.',
            $thisClass
        ));

        if (array_key_exists('plugin', $configuration)) {
            return $configuration['plugin'];
        }

        throw new SystemException(sprintf(
            'The plugin configuration file plugin.yaml should contain the "plugin" section: %s.',
            $thisClass
        ));
    }

    /**
     * register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
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
     * registerMarkupTags registers CMS markup tags introduced by this plugin.
     *
     * @return array
     */
    public function registerMarkupTags()
    {
        return [];
    }

    /**
     * registerComponents registers any frontend components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [];
    }

    /**
     * registerNavigation registers backend navigation items for this plugin.
     *
     * @return array
     * @throws SystemException
     */
    public function registerNavigation()
    {
        $configuration = $this->getConfigurationFromYaml();

        if (!array_key_exists('navigation', $configuration)) {
            return [];
        }

        $navigation = $configuration['navigation'];

        if (!is_array($navigation)) {
            return [];
        }

        array_walk_recursive($navigation, static function (&$item, $key) {
            if ($key === 'url') {
                $item = Backend::url($item);
            }
        });

        return $navigation;
    }

    /**
     * registerPermissions registers any backend permissions used by this plugin.
     *
     * @return array
     * @throws SystemException
     */
    public function registerPermissions()
    {
        $configuration = $this->getConfigurationFromYaml();

        if (!array_key_exists('permissions', $configuration)) {
            return [];
        }

        return $configuration['permissions'];
    }

    /**
     * registerSettings registers any backend configuration links used by this plugin.
     *
     * @return array
     * @throws SystemException
     */
    public function registerSettings()
    {
        $configuration = $this->getConfigurationFromYaml();

        if (!array_key_exists('settings', $configuration)) {
            return [];
        }

        $settings = $configuration['settings'];

        if (!is_array($settings)) {
            return [];
        }

        array_walk_recursive($settings, function (&$item, $key) {
            if ($key === 'url') {
                $item = Backend::url($item);
            }
        });

        return $settings;
    }

    /**
     * registerSchedule registers scheduled tasks that are executed on a regular basis.
     *
     * @param Schedule $schedule
     * @return void
     */
    public function registerSchedule($schedule)
    {
    }

    /**
     * registerReportWidgets registers any report widgets provided by this plugin.
     * The widgets must be returned in the following format:
     *
     *     return [
     *         'className1'=>[
     *             'label' => 'My widget 1',
     *             'context' => ['context-1', 'context-2'],
     *         ],
     *         'className2' => [
     *             'label' => 'My widget 2',
     *             'context' => 'context-1'
     *         ]
     *     ];
     *
     * @return array
     */
    public function registerReportWidgets()
    {
        return [];
    }

    /**
     * registerFormWidgets registers any form widgets implemented in this plugin.
     * The widgets must be returned in the following format:
     *
     *     return [
     *         ['className1' => 'alias'],
     *         ['className2' => 'anotherAlias']
     *     ];
     *
     * @return array
     */
    public function registerFormWidgets()
    {
        return [];
    }

    /**
     * registerFilterWidgets registers any filter widgets implemented in this plugin.
     * The widgets must be returned in the following format:
     *
     *     return [
     *         ['className1' => 'alias'],
     *         ['className2' => 'anotherAlias']
     *     ];
     *
     * @return array
     */
    public function registerFilterWidgets()
    {
        return [];
    }

    /**
     * registerListColumnTypes registers custom backend list column types introduced
     * by this plugin.
     *
     * @return array
     */
    public function registerListColumnTypes()
    {
        return [];
    }

    /**
     * registerMailLayouts registers any mail layouts implemented by this plugin.
     * The layouts must be returned in the following format:
     *
     *     return [
     *         'marketing' => 'acme.blog::layouts.marketing',
     *         'notification' => 'acme.blog::layouts.notification',
     *     ];
     *
     * @return array
     */
    public function registerMailLayouts()
    {
        return [];
    }

    /**
     * registerMailTemplates registers any mail templates implemented by this plugin.
     * The templates must be returned in the following format:
     *
     *     return [
     *         'acme.blog::mail.welcome',
     *         'acme.blog::mail.forgot_password',
     *     ];
     *
     * @return array
     */
    public function registerMailTemplates()
    {
        return [];
    }

    /**
     * registerMailPartials registers any mail partials implemented by this plugin.
     * The partials must be returned in the following format:
     *
     *     return [
     *         'tracking' => 'acme.blog::partials.tracking',
     *         'promotion' => 'acme.blog::partials.promotion',
     *     ];
     *
     * @return array
     */
    public function registerMailPartials()
    {
        return [];
    }

    /**
     * registerConsoleCommand registers a new console (artisan) command.
     * @param string $key The command name
     * @param string $class The command class
     * @return void
     */
    public function registerConsoleCommand($key, $class)
    {
        $key = 'command.'.$key;

        $this->app->singleton($key, $class);

        $this->commands($key);
    }

    /**
     * getConfigurationFromYaml reads configuration from YAML file.
     * @param string|null $exceptionMessage
     * @return array|bool
     * @throws SystemException
     */
    protected function getConfigurationFromYaml($exceptionMessage = null)
    {
        if ($this->loadedYamlConfiguration !== false) {
            return $this->loadedYamlConfiguration;
        }

        $reflection = new ReflectionClass(get_class($this));
        $yamlFilePath = dirname($reflection->getFileName()).'/plugin.yaml';

        if (file_exists($yamlFilePath)) {
            $this->loadedYamlConfiguration = Yaml::parse(file_get_contents($yamlFilePath));

            if (!is_array($this->loadedYamlConfiguration)) {
                throw new SystemException(sprintf(
                    'Invalid format of the plugin configuration file: %s. The file should define an array.',
                    $yamlFilePath
                ));
            }
        }
        else {
            if ($exceptionMessage !== null) {
                throw new SystemException($exceptionMessage);
            }

            $this->loadedYamlConfiguration = [];
        }

        return $this->loadedYamlConfiguration;
    }
}
