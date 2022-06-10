<?php namespace Tailor;

use App;
use Event;
use BackendMenu;
use Backend\Models\UserRole;
use Cms\Classes\ComponentManager;
use Tailor\Classes\FieldManager;
use Tailor\Classes\BlueprintIndexer;
use Backend\Classes\RoleManager;
use System\Classes\SettingsManager;
use October\Rain\Support\ModuleServiceProvider;

/**
 * ServiceProvider for Tailor module
 */
class ServiceProvider extends ModuleServiceProvider
{
    /**
     * register the service provider
     */
    public function register()
    {
        parent::register('tailor');

        $this->registerContentFields();
        $this->registerDeferredContentBinding();
        $this->bootEditorEvents();
        $this->registerConsole();
    }

    /**
     * boot the module events
     */
    public function boot()
    {
        parent::boot('tailor');

        // Migrate blueprints
        Event::listen('system.updater.migrate', function ($updateManager) {
            BlueprintIndexer::instance()
                ->setNotesOutput($updateManager->getNotesOutput())->migrate();
        });
    }

    /**
     * registerComponents
     */
    public function registerComponents()
    {
        return [
           \Tailor\Components\GlobalComponent::class => 'global',
           \Tailor\Components\SectionComponent::class => 'section',
           \Tailor\Components\CollectionComponent::class => 'collection',
           \Cms\Components\Resources::class => 'resources'
        ];
    }

    /**
     * registerDeferredContentBinding
     */
    protected function registerDeferredContentBinding()
    {
        \Tailor\Models\EntryRecord::registerDeferredContentModel();
        \Tailor\Models\RepeaterItem::registerDeferredContentModel();
        \Tailor\Models\GlobalRecord::registerDeferredContentModel();
    }

    /**
     * registerNavigation
     */
    public function registerNavigation()
    {
        return BlueprintIndexer::instance()->getNavigationMainMenu() +
            BlueprintIndexer::instance()->getNavigationContentMainMenu();
    }

    /**
     * registerSettings
     */
    public function registerSettings()
    {
        return BlueprintIndexer::instance()->getNavigationSettingsMenu();
    }

    /**
     * registerPermissions
     */
    public function registerPermissions()
    {
        return [
            // Editor
            'editor.tailor_blueprints' => [
                'label' => 'tailor::lang.permissions.manage_blueprints',
                'tab' => 'Editor',
                'roles' => UserRole::CODE_DEVELOPER,
                'order' => 100
            ]
        ] + BlueprintIndexer::instance()->getPermissionDefinitions();
    }

    /**
     * registerConsole
     */
    protected function registerConsole()
    {
        $this->registerConsoleCommand('tailor.refresh', \Tailor\Console\TailorRefresh::class);
        $this->registerConsoleCommand('tailor.migrate', \Tailor\Console\TailorMigrate::class);
    }

    /**
     * registerContentFields
     */
    protected function registerContentFields()
    {
        FieldManager::instance()->registerCustomFields(function ($manager) {
            $manager->registerCustomField(\Tailor\ContentFields\MixinField::class, 'mixin');
            $manager->registerCustomField(\Tailor\ContentFields\EntriesField::class, 'entries');
            $manager->registerCustomField(\Tailor\ContentFields\RepeaterField::class, 'repeater');
            $manager->registerCustomField(\Tailor\ContentFields\RichEditorField::class, 'richeditor');
            $manager->registerCustomField(\Tailor\ContentFields\MarkdownField::class, 'markdown');
            $manager->registerCustomField(\Tailor\ContentFields\FileUploadField::class, 'fileupload');
            $manager->registerCustomField(\Tailor\ContentFields\MediaFinderField::class, 'mediafinder');
            $manager->registerCustomField(\Tailor\ContentFields\CheckboxField::class, 'checkbox');
            $manager->registerCustomField(\Tailor\ContentFields\DataTableField::class, 'datatable');
            $manager->registerCustomField(\Tailor\ContentFields\TextareaField::class, 'textarea');
        });
    }

    /**
     * bootEditorEvents handles Editor events
     */
    protected function bootEditorEvents()
    {
        Event::listen('editor.extension.register', function () {
            return \Tailor\Classes\EditorExtension::class;
        });
    }
}
