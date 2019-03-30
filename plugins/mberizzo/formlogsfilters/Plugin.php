<?php namespace Mberizzo\FormLogsFilters;

use Backend;
use Illuminate\Support\Facades\Event;
use Mberizzo\FormLogsFilters\Controllers\Logs;
use Mberizzo\FormLogsFilters\Models\Log;
use Mberizzo\FormLogsFilters\Models\Settings;
use Renatio\FormBuilder\Models\Form as RenatioForm;
use Renatio\FormBuilder\Models\FormLog;
use System\Classes\PluginBase;

/**
 * FormLogsFilters Plugin Information File
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
            'name'        => 'FormLogsFilters',
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

        // @TODO: move this as Setting attribute
        $formsIds = filter_var_array(
            array_keys(Settings::instance()->value),
            FILTER_SANITIZE_NUMBER_INT
        );

        // Build sidebar navigation
        RenatioForm::withCount('logs')->findMany($formsIds)->each(function ($form, $index) use (&$menu) {
            $menu['formlogsfilters']['sideMenu'][$form->id] = [
                'label' => "{$form->name} ({$form->logs_count})",
                'icon' => Settings::get("form_id_$form->id")['icon'] ?? 'oc-icon-envelope',
                'url' => Backend::url("mberizzo/formlogsfilters/logs/index/{$form->id}"),
            ];
        });

        return $menu;
    }

    private function getMainMenuNavigation()
    {
        return [
            'formlogsfilters' => [
                'label'       => 'Messages',
                'url'         => Backend::url('mberizzo/formlogsfilters/logs'),
                'icon'        => 'icon-envelope',
                'permissions' => ['mberizzo.formlogsfilters.*'],
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
