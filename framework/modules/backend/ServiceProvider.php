<?php namespace Backend;

use App;
use Event;
use Backend;
use BackendMenu;
use BackendAuth;
use System\Classes\MailManager;
use System\Classes\CombineAssets;
use System\Classes\SettingsManager;
use Backend\Classes\RoleManager;
use Backend\Classes\WidgetManager;
use Backend\Models\UserRole;
use October\Rain\Auth\AuthException;
use October\Rain\Support\ModuleServiceProvider;

/**
 * ServiceProvider for Backend module
 */
class ServiceProvider extends ModuleServiceProvider
{
    /**
     * register the service provider.
     */
    public function register()
    {
        parent::register('backend');

        $this->registerMailer();
        $this->registerAssetBundles();

        // Backend specific
        if (App::runningInBackend()) {
            $this->registerBackendNavigation();
            $this->registerBackendReportWidgets();
            $this->registerBackendWidgets();
            $this->registerBackendPermissions();
            $this->registerBackendSettings();
        }
    }

    /**
     * boot the module events.
     */
    public function boot()
    {
        parent::boot('backend');

        $this->bootAuth();
    }

    /**
     * bootAuth boots authentication based logic.
     */
    protected function bootAuth(): void
    {
        AuthException::setDefaultErrorMessage('backend::lang.auth.invalid_login');
    }

    /**
     * registerMailer templates
     */
    protected function registerMailer()
    {
        MailManager::instance()->registerCallback(function ($manager) {
            $manager->registerMailTemplates([
                'backend::mail.invite',
                'backend::mail.restore',
            ]);
        });
    }

    /**
     * registerAssetBundles
     */
    protected function registerAssetBundles()
    {
        // Rich Editor is protected by DRM
        CombineAssets::registerCallback(function ($combiner) {
            if (file_exists(base_path('modules/backend/formwidgets/richeditor/assets/vendor/froala_drm'))) {
                $combiner->registerBundle('~/modules/backend/formwidgets/richeditor/assets/js/build-plugins.js');
                $combiner->registerBundle('~/modules/backend/formwidgets/richeditor/assets/less/richeditor.less');
                $combiner->registerBundle('~/modules/backend/formwidgets/richeditor/assets/js/build.js');
            }
        });
    }

    /**
     * registerBackendNavigation
     */
    protected function registerBackendNavigation()
    {
        BackendMenu::registerCallback(function ($manager) {
            $manager->registerMenuItems('October.Backend', [
                'dashboard' => [
                    'label' => 'backend::lang.dashboard.menu_label',
                    'icon' => 'icon-dashboard',
                    'iconSvg' => 'modules/backend/assets/images/dashboard-icon.svg',
                    'url' => Backend::url('backend'),
                    'permissions' => ['dashboard'],
                    'order' => 10
                ]
            ]);
        });
    }

    /**
     * registerBackendReportWidgets
     */
    protected function registerBackendReportWidgets()
    {
        WidgetManager::instance()->registerReportWidgets(function ($manager) {
            $manager->registerReportWidget(\Backend\ReportWidgets\Welcome::class, [
                'label'   => 'backend::lang.dashboard.welcome.widget_title_default',
                'context' => 'dashboard'
            ]);
        });
    }

    /**
     * registerBackendPermissions
     */
    protected function registerBackendPermissions()
    {
        RoleManager::instance()->registerCallback(function ($manager) {
            $manager->registerPermissions('October.Backend', [
                // General
                'general.backend' => [
                    'label' => 'Access the Backend Panel',
                    'tab' => 'General',
                    'order' => 200
                ],
                'general.backend.view_offline' => [
                    'label' => 'View Backend During Maintenance',
                    'tab' => 'General',
                    'order' => 300
                ],
                'general.backend.perform_updates' => [
                    'label' => 'Perform Software Updates',
                    'tab' => 'General',
                    'roles' => UserRole::CODE_DEVELOPER,
                    'order' => 300
                ],

                // Dashboard
                'dashboard' => [
                    'label' => 'system::lang.permissions.view_the_dashboard',
                    'tab' => 'Dashboard',
                    'order' => 200
                ],
                'dashboard.defaults' => [
                    'label' => 'system::lang.permissions.manage_default_dashboard',
                    'tab' => 'Dashboard',
                    'order' => 300,
                    'roles' => UserRole::CODE_DEVELOPER,
                ],

                // Administrators
                'admins.manage' => [
                    'label' => 'Manage Admins',
                    'tab' => 'Administrators',
                    'order' => 200
                ],
                'admins.manage.create' => [
                    'label' => 'Create Admins',
                    'tab' => 'Administrators',
                    'order' => 300
                ],
                // 'admins.manage.moderate' => [
                //     'label' => 'Moderate Admins',
                //     'comment' => 'Manage account suspension and ban admin accounts',
                //     'tab' => 'Administrators',
                //     'order' => 400
                // ],
                'admins.manage.roles' => [
                    'label' => 'Manage Roles',
                    'comment' => 'Allow users to create new roles and manage roles lower than their highest role.',
                    'tab' => 'Administrators',
                    'order' => 500
                ],
                'admins.manage.groups' => [
                    'label' => 'Manage Groups',
                    'tab' => 'Administrators',
                    'order' => 600
                ],
                'admins.manage.other_admins' => [
                    'label' => 'Manage Other Admins',
                    'comment' => 'Allow users to reset passwords and update emails.',
                    'tab' => 'Administrators',
                    'order' => 700
                ],
                'admins.manage.delete' => [
                    'label' => 'Delete Admins',
                    'tab' => 'Administrators',
                    'order' => 800
                ],

                // Preferences
                'preferences' => [
                    'label' => 'system::lang.permissions.manage_preferences',
                    'tab' => 'Preferences',
                    'order' => 400
                ],
                'preferences.code_editor' => [
                    'label' => 'system::lang.permissions.manage_editor',
                    'tab' => 'Preferences',
                    'order' => 500
                ],

                // Settings
                'settings.customize_backend' => [
                    'label' => 'system::lang.permissions.manage_branding',
                    'tab' => 'Settings',
                    'order' => 400
                ],
                'settings.editor_settings' => [
                    'label' => 'Global Editor Settings',
                    'comment' => 'backend::lang.editor.menu_description',
                    'tab' => 'Settings',
                    'order' => 500
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
            $manager->registerFormWidget(\Backend\FormWidgets\CodeEditor::class, 'codeeditor');
            $manager->registerFormWidget(\Backend\FormWidgets\RichEditor::class, 'richeditor');
            $manager->registerFormWidget(\Backend\FormWidgets\MarkdownEditor::class, 'markdown');
            $manager->registerFormWidget(\Backend\FormWidgets\FileUpload::class, 'fileupload');
            $manager->registerFormWidget(\Backend\FormWidgets\Relation::class, 'relation');
            $manager->registerFormWidget(\Backend\FormWidgets\DatePicker::class, 'datepicker');
            $manager->registerFormWidget(\Backend\FormWidgets\TimePicker::class, 'timepicker');
            $manager->registerFormWidget(\Backend\FormWidgets\ColorPicker::class, 'colorpicker');
            $manager->registerFormWidget(\Backend\FormWidgets\DataTable::class, 'datatable');
            $manager->registerFormWidget(\Backend\FormWidgets\RecordFinder::class, 'recordfinder');
            $manager->registerFormWidget(\Backend\FormWidgets\Repeater::class, 'repeater');
            $manager->registerFormWidget(\Backend\FormWidgets\TagList::class, 'taglist');
            $manager->registerFormWidget(\Backend\FormWidgets\NestedForm::class, 'nestedform');
            $manager->registerFormWidget(\Backend\FormWidgets\Sensitive::class, 'sensitive');
        });

        WidgetManager::instance()->registerFilterWidgets(function ($manager) {
            $manager->registerFilterWidget(\Backend\FilterWidgets\Group::class, 'group');
            $manager->registerFilterWidget(\Backend\FilterWidgets\Date::class, 'date');
            $manager->registerFilterWidget(\Backend\FilterWidgets\Text::class, 'text');
            $manager->registerFilterWidget(\Backend\FilterWidgets\Number::class, 'number');
        });
    }

    /**
     * registerBackendSettings
     */
    protected function registerBackendSettings()
    {
        SettingsManager::instance()->registerCallback(function ($manager) {
            $manager->registerSettingItems('October.Backend', [
                'administrators' => [
                    'label' => 'backend::lang.user.menu_label',
                    'description' => 'backend::lang.user.menu_description',
                    'category' => SettingsManager::CATEGORY_TEAM,
                    'icon' => 'octo-icon-users',
                    'url' => Backend::url('backend/users'),
                    'permissions' => ['admins.manage'],
                    'order' => 400
                ],
                'adminroles' => [
                    'label' => 'backend::lang.user.role.menu_label',
                    'description' => 'backend::lang.user.role.menu_description',
                    'category' => SettingsManager::CATEGORY_TEAM,
                    'icon' => 'octo-icon-id-card-1',
                    'url' => Backend::url('backend/userroles'),
                    'permissions' => ['admins.manage.roles'],
                    'order' => 410
                ],
                'admingroups' => [
                    'label' => 'backend::lang.user.group.menu_label',
                    'description' => 'backend::lang.user.group.menu_description',
                    'category' => SettingsManager::CATEGORY_TEAM,
                    'icon' => 'octo-icon-user-group',
                    'url' => Backend::url('backend/usergroups'),
                    'permissions' => ['admins.manage.groups'],
                    'order' => 420
                ],
                'branding' => [
                    'label' => 'backend::lang.branding.menu_label',
                    'description' => 'backend::lang.branding.menu_description',
                    'category' => SettingsManager::CATEGORY_SYSTEM,
                    'icon' => 'octo-icon-paint-brush-1',
                    'class' => 'Backend\Models\BrandSetting',
                    'permissions' => ['settings.customize_backend'],
                    'order' => 500,
                    'keywords' => 'brand style'
                ],
                'editor' => [
                    'label' => 'backend::lang.editor.menu_label',
                    'description' => 'backend::lang.editor.menu_description',
                    'category' => SettingsManager::CATEGORY_SYSTEM,
                    'icon' => 'icon-code',
                    'class' => 'Backend\Models\EditorSetting',
                    'permissions' => ['settings.editor_settings'],
                    'order' => 500,
                    'keywords' => 'html code class style'
                ],
                'myaccount' => [
                    'label' => 'backend::lang.myaccount.menu_label',
                    'description' => 'backend::lang.myaccount.menu_description',
                    'category' => SettingsManager::CATEGORY_MYSETTINGS,
                    'icon' => 'octo-icon-user-account',
                    'url' => Backend::url('backend/users/myaccount'),
                    'order' => 500,
                    'context' => 'mysettings',
                    'keywords' => 'backend::lang.myaccount.menu_keywords'
                ],
                'preferences' => [
                    'label' => 'backend::lang.backend_preferences.menu_label',
                    'description' => 'backend::lang.backend_preferences.menu_description',
                    'category' => SettingsManager::CATEGORY_MYSETTINGS,
                    'icon' => 'octo-icon-app-window',
                    'url' => Backend::url('backend/preferences'),
                    'permissions' => ['preferences'],
                    'order' => 510,
                    'context' => 'mysettings'
                ],
                'access_logs' => [
                    'label' => 'backend::lang.access_log.menu_label',
                    'description' => 'backend::lang.access_log.menu_description',
                    'category' => SettingsManager::CATEGORY_LOGS,
                    'icon' => 'octo-icon-lock',
                    'url' => Backend::url('backend/accesslogs'),
                    'permissions' => ['utilities.logs'],
                    'order' => 920
                ]
            ]);
        });
    }
}
