<?php

return [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'pl',
    'timeZone'   => 'Europe/Warsaw',
    'components' => [
        'request' => [
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
        'mailer' => require __DIR__ . '/mailer.php',
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => ['_GET'],
                    'except' => ['yii\web\HttpException:404', 'szymon']
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'logVars' => [],
                    'categories' => ['szymon'],
                    'logFile' => '@runtime/logs/szymon.log',
                ],
            ],
        ],
        'db' => require __DIR__ . '/db.php',
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
        'hipchat' => require __DIR__ . '/hipchat.php',
        'gitlab' => require __DIR__ . '/gitlab.php',
    ],
    'params' => require __DIR__ . '/params.php',
];
