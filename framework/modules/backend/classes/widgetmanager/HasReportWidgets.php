<?php namespace Backend\Classes\WidgetManager;

use Event;
use BackendAuth;
use SystemException;

/**
 * HasReportWidgets
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasReportWidgets
{
    /**
     * @var array reportWidgets
     */
    protected $reportWidgets;

    /**
     * @var array reportWidgetCallbacks cache
     */
    protected $reportWidgetCallbacks = [];

    /**
     * listReportWidgets returns a list of registered report widgets.
     * @return array Array keys are class names.
     */
    public function listReportWidgets()
    {
        if ($this->reportWidgets === null) {
            $this->reportWidgets = [];

            /*
             * Load module widgets
             */
            foreach ($this->reportWidgetCallbacks as $callback) {
                $callback($this);
            }

            /*
             * Load plugin widgets
             */
            $plugins = $this->pluginManager->getPlugins();

            foreach ($plugins as $plugin) {
                if (!is_array($widgets = $plugin->registerReportWidgets())) {
                    continue;
                }

                foreach ($widgets as $className => $widgetInfo) {
                    $this->registerReportWidget($className, $widgetInfo);
                }
            }
        }

        /**
         * @event system.reportwidgets.extendItems
         * Enables adding or removing report widgets.
         *
         * You will have access to the WidgetManager instance and be able to call the appropiate methods
         * $manager->registerReportWidget();
         * $manager->removeReportWidget();
         *
         * Example usage:
         *
         *     Event::listen('system.reportwidgets.extendItems', function ($manager) {
         *          $manager->removeReportWidget('Acme\ReportWidgets\YourWidget');
         *     });
         *
         */
        Event::fire('system.reportwidgets.extendItems', [$this]);

        $user = BackendAuth::getUser();
        foreach ($this->reportWidgets as $widget => $config) {
            if (!empty($config['permissions'])) {
                if (!$user->hasAccess($config['permissions'], false)) {
                    unset($this->reportWidgets[$widget]);
                }
            }
        }

        return $this->reportWidgets;
    }

    /**
     * getReportWidgets returns the raw array of registered report widgets.
     * @return array Array keys are class names.
     */
    public function getReportWidgets()
    {
        return $this->reportWidgets;
    }

    /*
     * registerReportWidget registers a single report widget.
     */
    public function registerReportWidget($className, $widgetInfo)
    {
        $this->reportWidgets[$className] = $widgetInfo;
    }

    /**
     * registerReportWidgets manually registers report widget for consideration. Usage:
     *
     *     WidgetManager::registerReportWidgets(function ($manager) {
     *         $manager->registerReportWidget(\RainLab\GoogleAnalytics\ReportWidgets\TrafficOverview::class, [
     *             'name' => 'Google Analytics traffic overview',
     *             'context' => 'dashboard'
     *         ]);
     *     });
     *
     */
    public function registerReportWidgets(callable $definitions)
    {
        $this->reportWidgetCallbacks[] = $definitions;
    }

    /**
     * removeReportWidget removes a registered ReportWidget.
     * @param string $className Widget class name.
     * @return void
     */
    public function removeReportWidget($className)
    {
        if (!$this->reportWidgets) {
            throw new SystemException('Unable to remove a widget before widgets are loaded.');
        }

        unset($this->reportWidgets[$className]);
    }
}