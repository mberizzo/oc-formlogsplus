<?php

namespace Mberizzo\FormLogsPlus\Models;

use Mberizzo\FormLogsPlus\Classes\IconList;
use October\Rain\Database\Model;
use Renatio\FormBuilder\Models\Form as RenatioForm;

/**
 * Class Settings
 * @package Mberizzo\FormLogsPlus\Models
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
            'label' => 'mberizzo.formlogsplus::lang.settings.label',
            'description' => 'mberizzo.formlogsplus::lang.settings.description',
            'category' => 'renatio.formbuilder::lang.settings.category',
            'icon' => 'icon-envelope',
            'class' => 'Mberizzo\FormLogsPlus\Models\Settings',
            'order' => 600,
            'keywords' => 'form builder contact messages',
            'permissions' => ['mberizzo.formlogsplus.*'],
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
                                'icon' => [
                                    'label' => 'mberizzo.formlogsplus::lang.form.labels.icon',
                                    'type' => 'dropdown',
                                    'comment' => 'mberizzo.formlogsplus::lang.form.comments.icon'
                                ],
                                'columns' => [
                                    'label' => 'mberizzo.formlogsplus::lang.form.labels.columns',
                                    'type' => 'checkboxlist',
                                    'span' => 'auto',
                                    'options' => $fields,
                                ],
                                'scopes' => [
                                    'label' => 'mberizzo.formlogsplus::lang.form.labels.scopes',
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
        $settings = [];

        foreach ($options as $formKey => $setting) {
            if ($setting['columns'] || $setting['scopes']) {
                foreach ($setting as $name => $value) {
                    if (! empty($options[$formKey][$name])) {
                        $settings[$formKey][$name] = $value;
                    }
                }
            }
        }

        ksort($settings); // by key ASC
        return $settings;
    }

    public function getIconOptions()
    {
        return (new IconList)->getList();
    }
}
