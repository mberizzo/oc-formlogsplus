<?php namespace Mberizzo\FormLogsPlus\Classes;

use Illuminate\Support\Arr;
use Mberizzo\FormLogsPlus\Traits\FormSettings;

class FilterBuilder
{

    use FormSettings;

    protected $filter;
    protected $formId;
    protected $scopes;
    protected $codes = [
        'text' => 'text',
        'radio_list' => 'group',
        'checkbox' => 'checkbox',
        'date' => 'date',
    ];

    public function __construct($filter)
    {
        $this->filter = $filter;
        $this->formId = $filter->getController()->formId;
        $this->scopes = $this->scopes();
    }

    public function addScopes()
    {
        foreach ($this->scopes as $fieldName) {
            $this->filter->addScopes(
                $this->makeScope($this->fields()[$fieldName])
            );
        }

        $this->addCreatedAtScope();
    }

    protected function makeScope($field)
    {
        return [
            "form_data.{$field->name}.value" => [
                'label' => $field->label,
                'type' => $this->codes[$field->field_type->code],
                'conditions' => $this->getConditions($field),
                'options' => $this->getOptions($field),
            ],
        ];
    }

    protected function getConditions($field)
    {
        switch ($field->field_type->code) {
            case 'radio_list':
                $cond  = "JSON_EXTRACT(form_data, '$.{$field->name}.value') ";
                $cond .= "in (:filtered)";
                break;
            default:
                $cond  = 'LOWER(JSON_EXTRACT(form_data, "$.' . $field->name . '.value")) ';
                $cond .= 'LIKE LOWER("%":value"%")';
                break;
        }

        return $cond;
    }

    protected function getOptions($field)
    {
        if (! $field->options) {
            return [];
        }

        $options = array_map(function ($option) {
            return [$option['o_key'] => $option['o_label']];
        }, $field->options);

        return Arr::collapse($options);
    }

    protected function addCreatedAtScope()
    {
        $this->filter->addScopes([
            'created_at' => [
                'label' => 'Date',
                'type' => "daterange",
                'conditions' => "created_at >= ':after' AND created_at <= ':before'",
            ],
        ]);
    }
}
