<?php namespace Mberizzo\FormLogsFilters;

use Backend;
use Illuminate\Support\Facades\Event;
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
        $menu = $this->getMainMenuNavigation();

        // Build sidebar navigation
        RenatioForm::all()->each(function ($item, $key) use (&$menu) {
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
        // Add fields dynamically
        Event::listen('backend.form.extendFields', function ($form) {
            if (! $form->model instanceof Settings) {
                return;
            }

            RenatioForm::all()->each(function ($item) use ($form) {
                $data = [];
                $item->fields->each(function($field, $key) use ($item, &$data) {
                    // We can config just the type="text" fields
                    $allowedTypeFields = ['text', 'email'];
                    if (in_array($field->field_type->code, $allowedTypeFields)) {
                        $data["{$item->id}_{$field->name}"] = [ // key: form_id + field_name
                            'label' => $field->label,
                            'tab' => $item->name,
                            'usePanelStyles' => false,
                            'type' => 'checkboxlist',
                            'span' => 'auto',
                            'options' => [
                                'is_column' => 'Add to columns',
                                'is_filter' => 'Add to filters',
                            ],
                        ];
                    }
                });

                $form->addTabFields($data);
            });
        });

        return Settings::$config;
    }
}
