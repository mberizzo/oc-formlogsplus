<?php

namespace Mberizzo\FormLogsFilters\Models;

use Renatio\FormBuilder\Models\Form as RenatioForm;
use October\Rain\Database\Model;

/**
 * Class Settings
 * @package Mberizzo\FormLogsFilters\Models
 */
class Settings extends Model
{

    /**
     * @var array
     */
    public $implement = ['System.Behaviors.SettingsModel'];

    /**
     * @var string
     */
    public $settingsCode = 'mberizzo_formlogsplus_settings';

    /**
     * @var string
     */
    public $settingsFields = 'fields.yaml';

    public static $allowedTypeFields = ['text', 'email', 'radio_list', 'checkbox_list', 'checkbox', 'date'];

    public static $config = [
        'settings' => [
            'label' => 'Logs Plus',
            'description' => 'Description',
            'category' => 'renatio.formbuilder::lang.settings.category',
            'icon' => 'icon-envelope',
            'class' => 'Mberizzo\FormLogsFilters\Models\Settings',
            'order' => 600,
            'keywords' => 'form builder contact messages',
            // 'permissions' => ['mberizzo.company.access_company'],
        ],
    ];

    protected static function buildFieldsDotYaml($form): void
    {
        RenatioForm::all()->each(function ($formItem) use ($form) {
            if ($form->isNested) {
                return;
            }

            $fields = [];
            $formItem->fields->each(function($field, $key) use (&$fields) {
                if (in_array($field->field_type->code, self::$allowedTypeFields)) {
                    $fields[$field->name] = $field->label;
                }
            });

            if ($fields) {
                $form->addTabFields([
                    "form_id_{$formItem->id}" => [
                        'type' => 'nestedform',
                        'tab' => $formItem->name,
                        'form' => [
                            'fields' => [
                                'columns' => [
                                    'label' => 'Columns',
                                    'type' => 'checkboxlist',
                                    'span' => 'auto',
                                    'options' => $fields,
                                ],
                                'scopes' => [
                                    'label' => 'Scopes',
                                    'type' => 'checkboxlist',
                                    'span' => 'auto',
                                    'options' => $fields,
                                ],
                            ],
                        ],
                    ],
                ]);
            }
        });
    }

    public function beforeSave()
    {
        $this->value = $this->cleanFieldsWithoutOptions($this->value);
    }

    private function cleanFieldsWithoutOptions($options)
    {
        $value = [];

        foreach ($options as $formKey => $fields) {
            foreach ($fields as $fieldName => $fieldValue) {
                if (! empty($options[$formKey][$fieldName])) {
                    $value[$formKey][$fieldName] = $fieldValue;
                }
            }
        }

        ksort($value); // by key ASC
        return $value;
    }
}
