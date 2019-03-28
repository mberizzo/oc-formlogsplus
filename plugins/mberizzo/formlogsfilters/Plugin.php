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
        RenatioForm::findMany($formsIds)->each(function ($item, $key) use (&$menu) {
            $menu['formlogsfilters']['sideMenu'][$item->id] = [
                'label' => $item->name,
                'icon' => 'icon-envelope',
                'url' => Backend::url("mberizzo/formlogsfilters/logs/index/{$item->id}"),
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
