<?php namespace Mberizzo\FormLogsPlus;

use Backend;
use Illuminate\Support\Facades\Event;
use Mberizzo\FormLogsPlus\Controllers\Logs;
use Mberizzo\FormLogsPlus\Models\Log;
use Mberizzo\FormLogsPlus\Models\Settings;
use Renatio\FormBuilder\Models\Form as RenatioForm;
use Renatio\FormBuilder\Models\FormLog;
use System\Classes\PluginBase;

/**
 * FormLogsPlus Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * @var array Plugin dependencies
     */
    public $require = ['Renatio.FormBuilder'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'FormLogsPlus',
            'description' => 'No description provided yet...',
            'author'      => 'Mberizzo',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        RenatioForm::extend(function($model) {
            $model->hasMany['logs'] = FormLog::class;
        });
    }

    public function registerListColumnTypes()
    {
        return [
            'mberizzo.json' => function($value, $column, $record) {
                $attributes = explode('.', $column->columnName);
                $field = array_shift($attributes);
                $data = json_decode($record->{$field});
                $value = $data->{$attributes[0]};

                for ($i = 1; $i < count($attributes); $i++) {
                    $value = $value->{$attributes[$i]};
                }

                return $value;
            }
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'mberizzo.formlogsplus.access_logs' => [
                'tab' => 'Form Logs Plus',
                'label' => 'Messages'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        $menu = $this->getMainMenuNavigation();

        // @TODO: move this as Setting attribute or scope form RenatioForm model
        // This code is also in plugins/mberizzo/formlogsplus/controllers/Forms.php
        $formsIds = filter_var_array(
            array_keys(Settings::instance()->value),
            FILTER_SANITIZE_NUMBER_INT
        );

        // Build sidebar navigation
        RenatioForm::findMany($formsIds)->each(function ($form, $index) use (&$menu) {
            $menu['formlogsplus']['sideMenu'][$form->id] = [
                'label' => $form->name,
                'icon' => Settings::get("form_id_$form->id")['icon'],
                'url' => Backend::url("mberizzo/formlogsplus/logs/index/{$form->id}"),
            ];
        });

        return $menu;
    }

    private function getMainMenuNavigation()
    {
        return [
            'formlogsplus' => [
                'label'       => 'Messages',
                'url'         => Backend::url('mberizzo/formlogsplus/forms'),
                'icon'        => 'icon-envelope',
                'permissions' => ['mberizzo.formlogsplus.*'],
                'order'       => 500,
            ],
        ];
    }

    public function registerSettings()
    {
        Event::listen('backend.form.extendFields', function ($form) {
            if (! $form->model instanceof Settings) {
                return;
            }

            Settings::buildFieldsDotYaml($form);
        });

        return Settings::$config;
    }
}
