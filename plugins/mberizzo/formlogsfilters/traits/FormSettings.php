<?php namespace Mberizzo\FormLogsFilters\Traits;

use Mberizzo\FormLogsFilters\Models\Settings;
use Renatio\FormBuilder\Models\Field;

trait FormSettings {

    protected function columns()
    {
        return $this->getSetting('columns');
    }

    protected function scopes()
    {
        return $this->getSetting('scopes');
    }

    private function getSetting($key)
    {
        return Settings::get("form_id_{$this->formId}")[$key] ?? [];
    }

    protected function fields()
    {
        return Field::select('name', 'label', 'options', 'field_type_id')
            ->where('form_id', $this->formId)
            ->with('field_type:id,code')
            ->get()
            ->keyBy('name');
    }
}
