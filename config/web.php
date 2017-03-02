<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'pl',
    'timeZone'   => 'Europe/Warsaw',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'qPusFpsXU-6-4dYc_jiKS-Zg8PY3DYWt',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class' => 'app\components\User',
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => ['_GET', '_POST']
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'site/vote/<order:\d+>/<restaurant:\d+>' => 'site/vote',
                'site/unvote/<order:\d+>' => 'site/unvote',
                'site/unorder/<order:\d+>' => 'site/unorder',
                'site/order/<food:\d+>' => 'site/order',
                '<controller>/<action>/<id:\d+>' => '<controller>/<action>',
            ],
        ],
        'hipchat' => [
            'class' => 'app\components\HipChat',
            'mode' => 'test',
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
//    $config['bootstrap'][] = 'debug';
//    $config['modules']['debug'] = [
//        'class' => 'yii\debug\Module',
//    ];
//
//    $config['bootstrap'][] = 'gii';
//    $config['modules']['gii'] = [
//        'class' => 'yii\gii\Module',
//    ];
//}

return $config;
