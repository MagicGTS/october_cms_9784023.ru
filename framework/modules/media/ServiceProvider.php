<?php namespace Media;

use App;
use Backend;
use BackendMenu;
use BackendAuth;
use Media\Widgets\MediaManager;
use System\Classes\MarkupManager;
use Backend\Classes\RoleManager;
use Backend\Classes\WidgetManager;
use October\Rain\Support\ModuleServiceProvider;

/**
 * ServiceProvider for Media module
 */
class ServiceProvider extends ModuleServiceProvider
{
    /**
     * register the service provider.
     */
    public function register()
    {
        parent::register('media');

        $this->registerMarkupTags();

        // Backend specific
        if (App::runningInBackend()) {
            $this->registerBackendNavigation();
            $this->registerBackendWidgets();
            $this->registerBackendPermissions();
            $this->registerGlobalInstance();
        }
    }

    /**
     * boot the module events.
     */
    public function boot()
    {
        parent::boot('media');
    }

    /**
     * registerBackendNavigation
     */
    protected function registerBackendNavigation()
    {
        BackendMenu::registerCallback(function ($manager) {
            $manager->registerMenuItems('October.Media', [
                'media' => [
                    'label' => 'backend::lang.media.menu_label',
                    'icon' => 'icon-image',
                    'iconSvg' => 'modules/media/assets/images/media-icon.svg',
                    'url' => Backend::url('media'),
                    'permissions' => ['media.library'],
                    'order' => 200
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
            $manager->registerPermissions('October.Media', [
                'media.library' => [
                    'label' => 'Access the Media Manager',
                    'tab' => 'Media',
                    'order' => 300
                ],
                'media.library.create' => [
                    'label' => 'Upload Media',
                    'comment' => 'backend::lang.permissions.manage_media',
                    'tab' => 'Media',
                    'order' => 400
                ],
                // 'media.library.update' => [
                //     'label' => 'Modify Media',
                //     'comment' => 'Change meta data and other information',
                //     'tab' => 'Media',
                //     'order' => 500
                // ],
                'media.library.delete' => [
                    'label' => 'Delete Media',
                    'tab' => 'Media',
                    'order' => 600
                ]
            ]);
        });
    }

    /**
     * registerBackendWidgets
     */
    protected function registerBackendWidgets()
    {
        WidgetManager::instance()->registerFormWidgets(function ($manager) {
            $manager->registerFormWidget(\Media\FormWidgets\MediaFinder::class, 'mediafinder');
        });
    }

    /**
     * registerMarkupTags
     */
    protected function registerMarkupTags()
    {
        MarkupManager::instance()->registerCallback(function ($manager) {
            $manager->registerFilters([
                'media' => [\Media\Classes\MediaLibrary::class, 'url'],
            ]);
        });
    }

    /**
     * registerGlobalInstance ensures media Manager widget is available on all backend pages
     */
    protected function registerGlobalInstance()
    {
        \Backend\Classes\Controller::extend(function($controller) {
            if (!BackendAuth::userHasAccess('media.library')) {
                return;
            }

            $manager = new MediaManager($controller, 'ocmediamanager');
            $manager->bindToController();
        });
    }
}
