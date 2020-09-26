<?php

return [

    'namespace'       => env('FILE_COMPONENT_NAMESPACE', ''),
    'folder_edit'     => env('FOLDER_EDIT', 'pages'),
    'auth_middleware' => [
        'admin'    => [
            'middleware' => '',
            'except'     => [],
        ],
        'frontend' => [
            'middleware' => '',
            'except'     => [],
        ],
    ],

];
