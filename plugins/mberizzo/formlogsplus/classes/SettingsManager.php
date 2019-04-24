<?php namespace Mberizzo\FormLogsPlus\Classes;

use Renatio\FormBuilder\Models\Form as RenatioForm;
use Mberizzo\FormLogsPlus\Classes\SettingFormHelper;

class SettingsManager
{

    protected $allowed = [
        'text', 'email', 'radio_list',
        'checkbox_list', 'checkbox', 'date'
    ];

    public function register($form)
    {
        RenatioForm::all()->each(function ($formRenatio) use ($form) {
            if ($form->isNested) {
                return;
            }

            $fields = $this->filterRenatioFields($formRenatio);

            $config = (new SettingFormHelper($formRenatio, $fields))->getConfigAsArray();

            $form->addTabFields($config);
        });
    }

    /**
     * @param  Renatio\FormBuilder\Models\Form $form
     * @return array
     */
    public function filterRenatioFields($form)
    {
        $fields = [];

        $form->fields->each(function($field, $key) use (&$fields) {
            if (in_array($field->field_type->code, $this->allowed)) {
                $fields[$field->name] = $field->label;
            }
        });

        return $fields;
    }

}
