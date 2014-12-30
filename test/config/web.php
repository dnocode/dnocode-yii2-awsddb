<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
       'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],

       'ddb' =>  [
           "class"=>'dnocode\awsddb\ar\Connection',
           'base_url'=>"http://localhost:8000",
           'key'    => 'KEY',
            'secret' => 'KEY',
            'region' => 'eu-west-1'
        ],
    ]

];



return $config;
