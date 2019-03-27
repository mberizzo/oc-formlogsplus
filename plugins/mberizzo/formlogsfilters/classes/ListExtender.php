<?php namespace Mberizzo\FormLogsFilters\Classes;

use Mberizzo\FormLogsFilters\Traits\FormSettings;

class ListExtender
{

    use FormSettings;

    protected $list;
    protected $formId;

    public function __construct($list)
    {
        $this->list = $list;
        $this->formId = $list->getController()->formId;
    }

    public function addColumns()
    {
        foreach ($this->columns() as $fieldName) {
            $this->list->addColumns(
                $this->makeColumn($this->fields()[$fieldName])
            );
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
}
