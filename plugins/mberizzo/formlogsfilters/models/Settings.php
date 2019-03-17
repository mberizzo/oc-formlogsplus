<?php

namespace Mberizzo\FormLogsFilters\Models;

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
}
