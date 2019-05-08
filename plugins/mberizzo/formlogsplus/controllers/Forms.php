<?php namespace Mberizzo\FormLogsPlus\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Mberizzo\FormLogsPlus\Models\Settings;
use October\Rain\Support\Facades\Flash;

/**
 * Forms Back-end Controller
 */
class Forms extends Controller
{
    public $implement = [
        'Backend.Behaviors.ListController'
    ];

    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Mberizzo.FormLogsPlus', 'formlogsplus', 'forms');
    }

    public function index()
    {
        parent::index();
    }

    public function listExtendQuery($query)
    {
        // @TODO: change this code, is a hack to show empty list
        // when there are not configurations
        $formsIds = filter_var_array(
            array_keys(Settings::instance()->value ?? []),
            FILTER_SANITIZE_NUMBER_INT
        );

        // @TODO: hack to show empty list
        if (! $formsIds) {
            Flash::info(e(trans('mberizzo.formlogsplus::lang.messages.config_not_found')));
            $query->where('id', '<', 0);
        }

        // Extend the list query to filter by the form id
        $query->findMany($formsIds);
    }
}
