<?php namespace Mberizzo\FormLogsPlus\Classes;

class SettingFormHelper
{

    protected $form, $fields;

    public function __construct($form, $fields)
    {
        $this->form = $form;
        $this->fields = $fields;
    }

    public function getConfigAsArray()
    {
        return [
            "form_id_{$this->form->id}" => [
                'type' => 'nestedform',
                'tab' => $this->form->name,
                'form' => [
                    'fields' => [
                        'icon' => $this->getIcon(),
                        'columns' => $this->getColumns(),
                        'scopes' => $this->getScopes(),
                    ],
                ],
            ],
        ];
    }

    private function getIcon()
    {
        return [
            'label'   => 'mberizzo.formlogsplus::lang.form.labels.icon',
            'type'    => 'dropdown',
            'comment' => 'mberizzo.formlogsplus::lang.form.comments.icon'
        ];
    }

    private function getColumns()
    {
        return [
            'label'   => 'mberizzo.formlogsplus::lang.form.labels.columns',
            'type'    => 'checkboxlist',
            'span'    => 'auto',
            'options' => $this->fields,
        ];
    }

    private function getScopes()
    {
        return [
            'label'   => 'mberizzo.formlogsplus::lang.form.labels.scopes',
            'type'    => 'checkboxlist',
            'span'    => 'auto',
            'options' => $this->fields,
        ];
    }
}
