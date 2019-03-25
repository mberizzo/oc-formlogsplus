<?php namespace Mberizzo\FormLogsFilters\Classes;

use Mberizzo\FormLogsFilters\Models\Settings;
use Renatio\FormBuilder\Models\Field;

class ListExtender
{

    protected $list;
    protected $controller;
    protected $columns = [];

    public function __construct($list)
    {
        $this->list = $list;
        $this->controller = $list->getController();
        $this->columns = $this->getColumns();
    }

    public function addColumns()
    {
        foreach ($this->columns as $col) {
            $field = $this->getField($col); // @TODO: avoid find() every time
            $this->list->addColumns($this->makeColumn($field));
        }

        $this->addCreatedAtColumn();
    }

    protected function makeColumn($field)
    {
        return [
            "form_data.{$field->name}.value" => [
                'label' => $field->label,
                'type' => 'mberizzo.json',
                'sortable' => false,
                'invisible' => false,
            ],
        ];
    }

    protected function addCreatedAtColumn()
    {
        $this->list->addColumns([
            "created_at" => [
                'label' => 'Date',
                'type' => 'date',
                'sortable' => true,
            ],
        ]);
    }

    protected function getColumns()
    {
        $config = Settings::get("form_id_{$this->controller->formId}");
        return $config['columns'] ?? [];
    }

    protected function getField($col)
    {
        return Field::where([
            'name' => $col,
            'form_id' => $this->controller->formId,
        ])->first();
    }
}
