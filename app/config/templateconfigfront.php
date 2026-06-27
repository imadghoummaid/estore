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
            'normalize'         => FRONT_CSS . 'normalize.css',
            'fawsome'           => FRONT_CSS . 'fawsome.min.css',
            'gicons'            => FRONT_CSS . 'googleicons.css',
            'main'              => FRONT_CSS . 'main' . $_SESSION['lang'] . '.css'
        ],
        'js' => [
            'modernizr'         => FRONT_JS . 'vendor/modernizr-2.8.3.min.js'
        ]
    ],
    'footer_resources' => [
        'jquery'                => FRONT_JS . 'vendor/jquery-1.12.0.min.js',
        'helper'                => FRONT_JS . 'helper.js',
        'datatables'            => FRONT_JS . 'datatables' . $_SESSION['lang'] . '.js',
        'main'                  => FRONT_JS . 'main.js'
    ]
];
