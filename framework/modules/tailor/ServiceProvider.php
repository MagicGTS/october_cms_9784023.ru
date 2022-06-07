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

        $this->registerConsole();
        $this->registerContentFields();
        $this->registerComponents();
        $this->registerDeferredContentBinding();
        $this->bootEditorEvents();

        // Backend specific
        if (App::runningInBackend()) {
            $this->registerBackendNavigation();
            $this->registerBackendPermissions();
            $this->registerBackendSettings();
        }
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
    protected function registerComponents()
    {
        ComponentManager::instance()->registerComponents(function ($manager) {
            $manager->registerComponent(\Tailor\Components\GlobalComponent::class, 'global', $this);
            $manager->registerComponent(\Tailor\Components\SectionComponent::class, 'section', $this);
            $manager->registerComponent(\Tailor\Components\CollectionComponent::class, 'collection', $this);
            $manager->registerComponent(\Cms\Components\Resources::class, 'resources', $this);
        });
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
     * registerBackendNavigation
     */
    protected function registerBackendNavigation()
    {
        BackendMenu::registerCallback(function ($manager) {
            $manager->registerMenuItems(
                'October.Tailor',
                BlueprintIndexer::instance()->getNavigationContentMainMenu()
            );

            $manager->registerMenuItems(
                'October.Tailor',
                BlueprintIndexer::instance()->getNavigationMainMenu()
            );
        });
    }

    /**
     * registerBackendSettings
     */
    protected function registerBackendSettings()
    {
        SettingsManager::instance()->registerCallback(function ($manager) {
            $manager->registerSettingItems(
                'October.Tailor',
                BlueprintIndexer::instance()->getNavigationSettingsMenu()
            );
        });
    }

    /**
     * registerBackendPermissions
     */
    protected function registerBackendPermissions()
    {
        RoleManager::instance()->registerCallback(function ($manager) {
            $manager->registerPermissions('October.Tailor', [
                // Editor
                'editor.tailor_blueprints' => [
                    'label' => 'tailor::lang.permissions.manage_blueprints',
                    'tab' => 'Editor',
                    'roles' => UserRole::CODE_DEVELOPER,
                    'order' => 100
                ]
            ]);
        });

        RoleManager::instance()->registerCallback(function ($manager) {
            $manager->registerPermissions(
                'October.Tailor',
                BlueprintIndexer::instance()->getPermissionDefinitions()
            );
        });
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
