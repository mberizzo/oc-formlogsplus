<?php namespace Mberizzo\FormLogsFilters\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

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

    protected $formId;

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Renatio.FormBuilder', 'formlogs');
    }

    public function index($formId)
    {
        // Store the routed parameter to use later
        $this->formId = $formId;

        $this->asExtension('ListController')->index();
    }

    public function listExtendQuery($query)
    {
       // Extend the list query to filter by the form id
        $query->where('form_id', $this->formId);
    }
}
