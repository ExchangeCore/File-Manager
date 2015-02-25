<?php
$config = [
    'aliases' => require(__DIR__ . '/aliases.php'),
    'id' => 'file-manager',
    'controllerNamespace' => 'exchangecore\filemanager\controllers',
    'bootstrap' => ['log'],
    'vendorPath' => dirname(dirname(dirname(dirname(__DIR__)))),
    'layout' => '@core/views/layouts/main.php',
    'components' => [
        'view' => [
            'class' => 'exchangecore\filemanager\components\View',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error'
        ],
        'mail' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/mail'
        ],
        'i18n' => [
            'translations' => [
                'core*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@core/messages',
                    'fileMap' => [
                        'core' => 'core.php'
                    ]
                ]
            ]
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true
        ],
        'user' => [
            'identityClass' => 'exchangecore\filemanager\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => [

    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;