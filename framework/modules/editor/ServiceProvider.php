<?php namespace Editor;

use App;
use Backend;
use BackendMenu;
use Backend\Models\UserRole;
use Backend\Classes\RoleManager;
use October\Rain\Support\ModuleServiceProvider;

/**
 * ServiceProvider for Editor module
 */
class ServiceProvider extends ModuleServiceProvider
{
    /**
     * register the service provider.
     */
    public function register()
    {
        parent::register('editor');

        // Backend specific
        if (App::runningInBackend()) {
            $this->registerBackendNavigation();
            $this->registerBackendPermissions();
        }
    }

    /**
     * boot the module events.
     */
    public function boot()
    {
        parent::boot('editor');
    }

    /**
     * registerBackendNavigation
     */
    protected function registerBackendNavigation()
    {
        BackendMenu::registerCallback(function ($manager) {
            $manager->registerMenuItems('October.Editor', [
                'editor' => [
                    'label' => 'editor::lang.editor.menu_label',
                    'icon' => 'icon-pencil',
                    'iconSvg' => 'modules/editor/assets/images/editor-icon.svg',
                    'url' => Backend::url('editor'),
                    'order' => 90,
                    'permissions' => [
                        'editor'
                    ]
                ]
            ]);
        });
    }

    /**
     * registerBackendPermissions
     */
    protected function registerBackendPermissions()
    {
        RoleManager::instance()->registerCallback(function ($manager) {
            $manager->registerPermissions('October.Editor', [
                'editor' => [
                    'label' => 'Access the Editor Tool',
                    'comment' => 'editor::lang.permissions.access_editor',
                    'tab' => 'Editor',
                    'roles' => UserRole::CODE_DEVELOPER,
                    'order' => 100
                ],
            ]);
        });
    }
}