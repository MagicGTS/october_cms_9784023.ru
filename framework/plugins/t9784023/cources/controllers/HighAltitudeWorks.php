<?php namespace T9784023\Cources\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * High Altitude Works Backend Controller
 */
class HighAltitudeWorks extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
        'ReaZzon.Gutenberg.Behaviors.GutenbergController'
    ];

    /**
     * @var string formConfig file
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string listConfig file
     */
    public $listConfig = 'config_list.yaml';

    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('T9784023.Cources', 'cources', 'highaltitudeworks');
    }
}
