<?php namespace Backend\Classes\WidgetManager;

use Str;

/**
 * HasFormWidgets
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasFormWidgets
{
    /**
     * @var array formWidgets stored in the form of ['FormWidgetClass' => $formWidgetInfo]
     */
    protected $formWidgets;

    /**
     * @var array formWidgetCallbacks cache
     */
    protected $formWidgetCallbacks = [];

    /**
     * @var array formWidgetHints keyed by their code.
     * Stored in the form of ['formwidgetcode' => 'FormWidgetClass'].
     */
    protected $formWidgetHints;

    /**
     * listFormWidgets returns a list of registered form widgets.
     * @return array Array keys are class names.
     */
    public function listFormWidgets()
    {
        if ($this->formWidgets === null) {
            $this->formWidgets = [];

            /*
             * Load module widgets
             */
            foreach ($this->formWidgetCallbacks as $callback) {
                $callback($this);
            }

            /*
             * Load plugin widgets
             */
            $plugins = $this->pluginManager->getPlugins();

            foreach ($plugins as $plugin) {
                if (!is_array($widgets = $plugin->registerFormWidgets())) {
                    continue;
                }

                foreach ($widgets as $className => $widgetInfo) {
                    $this->registerFormWidget($className, $widgetInfo);
                }
            }
        }

        return $this->formWidgets;
    }

    /**
     * registerFormWidget registers a single form widget.
     * @param string $className Widget class name.
     * @param array $widgetInfo Registration information, can contain a `code` key.
     * @return void
     */
    public function registerFormWidget($className, $widgetInfo = null)
    {
        if (!is_array($widgetInfo)) {
            $widgetInfo = ['code' => $widgetInfo];
        }

        $widgetCode = $widgetInfo['code'] ?? null;

        if (!$widgetCode) {
            $widgetCode = Str::getClassId($className);
        }

        $this->formWidgets[$className] = $widgetInfo;
        $this->formWidgetHints[$widgetCode] = $className;
    }

    /**
     * registerFormWidgets manually registers form widget for consideration. Usage:
     *
     *     WidgetManager::registerFormWidgets(function ($manager) {
     *         $manager->registerFormWidget(\Backend\FormWidgets\CodeEditor::class, 'codeeditor');
     *     });
     *
     */
    public function registerFormWidgets(callable $definitions)
    {
        $this->formWidgetCallbacks[] = $definitions;
    }

    /**
     * resolveFormWidget returns a class name from a form widget code
     * Normalizes a class name or converts an code to its class name.
     * @param string $name Class name or form widget code.
     * @return string The class name resolved, or the original name.
     */
    public function resolveFormWidget($name)
    {
        if ($this->formWidgets === null) {
            $this->listFormWidgets();
        }

        $hints = $this->formWidgetHints;

        if (isset($hints[$name])) {
            return $hints[$name];
        }

        $_name = Str::normalizeClassName($name);
        if (isset($this->formWidgets[$_name])) {
            return $_name;
        }

        return $name;
    }
}
