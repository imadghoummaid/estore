<?php

return [
    'area'     => 'front',
    'template' => [
        'header'            => TEMPLATE_PATH . DS . 'front' . DS . 'header.php',
        ':view'             => ':action_view',
        'footer'            => TEMPLATE_PATH . DS . 'front' . DS . 'footer.php',
    ],
    'header_resources' => [
        'css' => [
            'normalize'         => CSS . 'normalize.css',
            'main'              => CSS . 'main' . $_SESSION['lang'] . '.css'
        ],
        'js' => [
        ]
    ],
    'footer_resources' => [
        'jquery'                => JS . 'vendor/jquery-1.12.0.min.js',
    ]
];