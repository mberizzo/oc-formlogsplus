<?php namespace Mberizzo\FormLogsFilters;

use Backend;
use Illuminate\Support\Facades\Event;
use Renatio\FormBuilder\Models\FormLog;
use System\Classes\PluginBase;

/**
 * FormLogsFilters Plugin Information File
 */
class Plugin extends PluginBase
{
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
        Event::listen('backend.menu.extendItems', function($manager) {
            $manager->addSideMenuItems('Renatio.FormBuilder', 'formbuilder', [
                'logs' => [
                    'label' => 'Curriculums',
                    'icon' => 'icon-graduation-cap',
                    'url' => Backend::url('mberizzo/formlogsfilters/logs/index'),
                ]
            ]);
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
        return []; // Remove this line to activate

        return [
            'mberizzo.formlogsfilters.some_permission' => [
                'tab' => 'FormLogsFilters',
                'label' => 'Some permission'
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
        return []; // Remove this line to activate

        return [
            'formlogsfilters' => [
                'label'       => 'FormLogsFilters',
                'url'         => Backend::url('mberizzo/formlogsfilters/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['mberizzo.formlogsfilters.*'],
                'order'       => 500,
            ],
        ];
    }
}
