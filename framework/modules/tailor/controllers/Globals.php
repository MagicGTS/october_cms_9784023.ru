<?php namespace Tailor\Controllers;

use BackendMenu;
use Tailor\Models\GlobalRecord;
use Tailor\Classes\BlueprintIndexer;
use Backend\Classes\WildcardController;
use ApplicationException;

/**
 * Globals controller
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class Globals extends WildcardController
{
    /**
     * @var array implement extensions
     */
    public $implement = [
        \Backend\Behaviors\FormController::class
    ];

    /**
     * @var string formConfig is `FormController` configuration.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var array requiredPermissions to view this page
     */
    // public $requiredPermissions = ['tailor.access_collections'];
    public $requiredPermissions = [];

    /**
     * @var GlobalBlueprint activeSource
     */
    protected $activeSource;

    /**
     * @var GlobalBlueprint[] allSources
     */
    protected $allSources;

    /**
     * beforeDisplay
     */
    public function beforeDisplay()
    {
        // Pop off first parameter as source handle
        $sourceHandle = array_shift($this->params);

        $this->makeBlueprintSources($sourceHandle);

        if (!$this->activeSource) {
            $this->handleError(new ApplicationException("Cannot find [${sourceHandle}] global"));
            return;
        }

        $this->setNavigationContext();
    }

    /**
     * index action
     */
    public function index()
    {
        if ($this->hasFatalError()) {
            return;
        }

        $this->pageTitle = 'Update Global';

        $this->asExtension('FormController')->update();

        $this->prepareVars();
    }

    /**
     * prepareVars
     */
    protected function prepareVars()
    {
        $this->vars['entityName'] = $this->activeSource->name ?? '';
        $this->vars['activeSource'] = $this->activeSource;
        $this->vars['sources'] = $this->allSources;
    }

    /**
     * onSave
     */
    public function onSave()
    {
        return $this->asExtension('FormController')->update_onSave();
    }

    /**
     * formFindModelObject
     */
    public function formFindModelObject($recordId)
    {
        return GlobalRecord::findForGlobalUuid($this->activeSource->uuid);
    }

    /**
     * makeBlueprintSources
     */
    protected function makeBlueprintSources($activeSource = null)
    {
        $this->allSources = BlueprintIndexer::instance()->listGlobals();

        if (!$activeSource) {
            $this->activeSource = $this->allSources[0] ?? null;
        }
        else {
            $this->activeSource = $activeSource
                ? BlueprintIndexer::instance()->findGlobalByHandle($activeSource)
                : null;
        }
    }

    /**
     * setNavigationContext
     */
    protected function setNavigationContext()
    {
        $item = BlueprintIndexer::instance()->findSecondaryNavigation($this->activeSource->uuid);
        if ($item) {
            $item->setBackendControllerContext();
        }
        else {
            BackendMenu::setContext('October.Tailor', 'tailor');
        }
    }
}
