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
