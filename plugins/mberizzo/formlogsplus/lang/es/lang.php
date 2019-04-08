<?php

return [
    'plugin' => [
        'name'        => 'Form Logs Plus',
        'description' => 'Agrego un listado inteligente de mensajes al plugin Form Builder'
    ],
    'settings' => [
        'label'       => 'Form Plus',
        'description' => 'Configurar el listado y los filtros',
    ],
    'permissions' => [
        'tab'         => 'Form Logs Plus',
        'access_logs' => 'Muestra, filtra y exporta el listado de mensajes',
    ],
    'navigation' => [
        'label' => 'Mensajes',
    ],
    'form' => [
        'labels' => [
            'icon'       => 'Icono',
            'columns'    => 'Columnas',
            'scopes'     => 'Filtros',
            'created_at' => 'Creado el',
            'from'       => 'Desde',
            'to'         => 'Hasta',
            'forms'      => 'Formularios',
            'logs_count' => 'Cant. de mensajes',
        ],
        'comments' => [
            'icon' => 'El icono se visualiza en el menu del sidebar.',
        ],
    ],
    'buttons' => [
        'export' => 'Exportar',
    ],
    'titles' => [
        'export'     => 'Exportar',
        'logs_list'  => 'Lista de mensajes',
        'forms_list' => 'Lista de formularios',
    ],
    'messages' => [
        'config_not_found' => 'No se encontraron configuraciones.',
    ],
];
