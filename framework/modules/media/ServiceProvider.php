<?php namespace Media;

use App;
use Backend;
use BackendMenu;
use BackendAuth;
use Media\Widgets\MediaManager;
use System\Classes\MarkupManager;
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
                    'icon' => 'icon-folder',
                    'iconSvg' => 'modules/media/assets/images/media-icon.svg',
                    'url' => Backend::url('media'),
                    'permissions' => ['media.*'],
                    'order' => 200
                ]
            ]);
        });
    }

    /**
     * Register permissions
     */
    protected function registerBackendPermissions()
    {
        BackendAuth::registerCallback(function ($manager) {
            $manager->registerPermissions('October.Media', [
                'media.manage_media' => [
                    'label' => 'backend::lang.permissions.manage_media',
                    'tab' => 'system::lang.permissions.name',
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
            $user = BackendAuth::getUser();
            if (!$user || !$user->hasAccess('media.*')) {
                return;
            }

            $manager = new MediaManager($controller, 'ocmediamanager');
            $manager->bindToController();
        });
    }
}
