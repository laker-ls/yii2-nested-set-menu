<?php

return [
    'id' => 'nested-set-menu-tests',
    'basePath' => dirname(__DIR__),
    //'bootstrap' => ['lakerLS\nestedSet\Bootstrap'],
    'language' => 'ru-RU',
    'aliases' => [
        '@laker-ls/nested-set-menu' => dirname(dirname(dirname(__DIR__))),
        '@tests' => dirname(dirname(__DIR__)),
        '@vendor' => VENDOR_DIR,
        '@bower' => VENDOR_DIR . '/bower-asset',
    ],
    'components' => [
        'mailer' => [
            'useFileTransport' => true,
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
        ],
    ],
    'params' => [],
];