<?php namespace Mberizzo\FormLogsPlus\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Mberizzo\FormLogsPlus\Classes\ExportManager;
use Mberizzo\FormLogsPlus\Classes\FilterBuilder;
use Mberizzo\FormLogsPlus\Classes\ListBuilder;
use Renatio\FormBuilder\Models\FormLog;

/**
 * Logs Back-end Controller
 */
class Logs extends Controller
{

    public $implement = [
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.ImportExportController'
    ];

    public $listConfig = 'config_list.yaml';
    public $importExportConfig = 'config_import_export.yaml';

    public $formId;

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Mberizzo.FormLogsPlus', 'formlogsplus');
    }

    public function index($formId)
    {
        // Store the routed parameter to use later
        $this->formId = $formId;

        $this->asExtension('ListController')->index();

        // Export formId
        $this->vars['formId'] = $formId;

        // Sidebar set active menu
        BackendMenu::setContext('Mberizzo.FormLogsPlus', 'formlogsplus', $formId);
    }

    public function show($logId)
    {
        $log = FormLog::find($logId);

        $this->pageTitle = $log->form->name;

        $this->vars['log'] = $log;

        // Sidebar set active menu
        BackendMenu::setContext('Mberizzo.FormLogsPlus', 'formlogsplus', $log->form_id);
    }

    public function listExtendQuery($query)
    {
       // Extend the list query to filter by the form id
        $query->where('form_id', $this->formId);
    }

    public function export($formId)
    {
        $this->vars['formId'] = $formId;

        $this->exportColumns = (new ExportManager($formId))->getExportableColumns();

        parent::export();
    }

    public function listExtendColumns($list): void
    {
        (new ListBuilder($list))->addColumns();
    }

    public function listFilterExtendScopes($filter): void
    {
        (new FilterBuilder($filter))->addScopes();
    }
}
