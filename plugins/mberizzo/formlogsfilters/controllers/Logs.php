<?php namespace Mberizzo\FormLogsFilters\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Illuminate\Support\Arr;
use Mberizzo\FormLogsFilters\Classes\ListExtender;
use Mberizzo\FormLogsFilters\Models\Settings;
use Renatio\FormBuilder\Models\Field;
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
    protected $settings;

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

        // Used to know which columns and filters to show
        $this->settings = Settings::get("form_id_$formId");
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

    public function listFilterExtendScopes($filter)
    {
        $this->getScopes()->each(function ($col) use ($filter) {
            $field = $this->getField($col);
            $type = 'text';
            $conditions = 'LOWER(JSON_EXTRACT(form_data, "$.' . $col . '.value")) LIKE LOWER("%":value"%")';
            $options = [];

            if ($field->field_type->code == 'radio_list') {
                $type = 'group';
                $conditions = "JSON_EXTRACT(form_data, '$.field_sexo.value') in (:filtered)";
                $options = array_map(function ($option) {
                    return [$option['o_key'] => $option['o_label']];
                }, $field->options);
            }

            $filter->addScopes([
                "form_data.{$col}.value" => [
                    'label' => $field->label,
                    'type' => $type,
                    'conditions' => $conditions,
                    'options' => Arr::collapse($options),
                ],
            ]);
        });

        $filter->addScopes([
            "created_at" => [
                'label' => 'Date',
                'type' => "daterange",
                'conditions' => "created_at >= ':after' AND created_at <= ':before'",
            ],
        ]);
    }

    private function getField($col)
    {
        return Field::where([
            'name' => $col,
            'form_id' => $this->formId
        ])->first();
    }

    private function getScopes()
    {
        return collect($this->settings['scopes'] ?? []);
    }
}
