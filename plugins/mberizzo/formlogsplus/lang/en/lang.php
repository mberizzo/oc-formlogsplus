<?php

return [
    'plugin' => [
        'name'        => 'Form Logs Plus',
        'description' => 'Add list, filters and export features to Form Builder plugin.'
    ],
    'settings' => [
        'label'       => 'Plus',
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
        ],
        'comments' => [
            'icon' => 'The icon will be shown on sidebar menu.',
        ],
    ],
];
