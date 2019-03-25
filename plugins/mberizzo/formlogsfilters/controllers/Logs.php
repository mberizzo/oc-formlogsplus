<?php namespace Mberizzo\FormLogsFilters\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Mberizzo\FormLogsFilters\Classes\FilterExtender;
use Mberizzo\FormLogsFilters\Classes\ListExtender;
use Renatio\FormBuilder\Models\Form;
use System\Classes\SettingsManager;

/**
 * Logs Back-end Controller
 */
class Logs extends Controller
{

    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.ImportExportController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $importExportConfig = 'config_import_export.yaml';

    public $formId;

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Mberizzo.FormLogsFilters', 'formlogsfilters');
        SettingsManager::setContext('Mberizzo.FormLogsFilters', 'settings');
    }

    public function index($formId = null)
    {
        $formId = $formId ?? $this->getFirstFormId();

        // Store the routed parameter to use later
        $this->formId = $formId;
        $this->asExtension('ListController')->index();

        // Export formId
        $this->vars['formId'] = $formId;
    }

    public function listExtendQuery($query)
    {
       // Extend the list query to filter by the form id
        $query->where('form_id', $this->formId);
    }

    public function export($formId)
    {
        $this->vars['formId'] = $formId;
        parent::export();
    }

    private function getFirstFormId()
    {
        return Form::orderBy('name')->first()->id ?? abort(404);
    }

    public function listExtendColumns($list): void
    {
        (new ListExtender($list))->addColumns();
    }

    public function listFilterExtendScopes($filter): void
    {
        (new FilterExtender($filter))->addScopes();
    }
}
