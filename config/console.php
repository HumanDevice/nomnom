<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'pl',
    'timeZone'   => 'Europe/Warsaw',
    'controllerNamespace' => 'app\commands',
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
        'db' => $db,
        'hipchat' => [
            'class' => 'app\components\HipChat',
            'mode' => 'prod',
            'prod' => [
                'room' => 765869,
                'token' => 'ruC1LAqGbMZy4rOw2d5gT0PvFsJ7xzdISSqGwzvY'
            ],
            'test' => [
                'room' => 3147500,
                'token' => 'vwqKCZFW690NcYpIByebDAMOawy0rHGUX2OGIT5i'
            ],
        ],
    ],
    'params' => $params,
];

//if (YII_ENV_DEV) {
//    // configuration adjustments for 'dev' environment
//    $config['bootstrap'][] = 'gii';
//    $config['modules']['gii'] = [
//        'class' => 'yii\gii\Module',
//    ];
//}

return $config;
