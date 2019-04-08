<?php

return [
    'plugin' => [
        'name'        => 'Form Logs Plus',
        'description' => 'Add list, filters and export features to Form Builder plugin.'
    ],
    'settings' => [
        'label'       => 'Form Plus',
        'description' => 'List and filters configurations',
    ],
    'permissions' => [
        'tab'         => 'Form Logs Plus',
        'access_logs' => 'Show, filter and export messages list',
    ],
    'navigation' => [
        'label' => 'Messages',
    ],
    'form' => [
        'labels' => [
            'icon'       => 'Icon',
            'columns'    => 'Columns',
            'scopes'     => 'Filters',
            'created_at' => 'Created',
            'from'       => 'From',
            'to'         => 'To',
            'forms'      => 'Forms',
            'logs_count' => 'Logs Count',
        ],
        'comments' => [
            'icon' => 'The icon will be shown on sidebar menu.',
        ],
    ],
    'buttons' => [
        'export' => 'Export',
    ],
    'titles' => [
        'export'     => 'Export',
        'logs_list'  => 'Messages List',
        'forms_list' => 'Forms List',
    ],
    'messages' => [
        'config_not_found' => 'There are no configurations',
    ],
];
