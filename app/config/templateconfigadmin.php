<?php

return [
    'template' => [
        'wrapper_start'     => TEMPLATE_PATH . 'wrapperstart.php',
        'header'            => TEMPLATE_PATH . 'header.php',
        'nav'               => TEMPLATE_PATH . 'nav.php',
        ':view'             => ':action_view',
        'wrapper_end'       => TEMPLATE_PATH . 'wrapperend.php'
    ],
    'header_resources' => [
        'css' => [
            'normalize'         => ADMIN_CSS . 'normalize.css',
            'fawsome'           => ADMIN_CSS . 'fawsome.min.css',
            'gicons'            => ADMIN_CSS . 'googleicons.css',
            'main'              => ADMIN_CSS . 'main' . $_SESSION['lang'] . '.css'
        ],
        'js' => [
            'modernizr'         => ADMIN_JS . 'vendor/modernizr-2.8.3.min.js'
        ]
    ],
    'footer_resources' => [
        'jquery'                => ADMIN_JS . 'vendor/jquery-1.12.0.min.js',
        'helper'                => ADMIN_JS . 'helper.js',
        'datatables'            => ADMIN_JS . 'datatables' . $_SESSION['lang'] . '.js',
        'main'                  => ADMIN_JS . 'main.js'
    ]
];
