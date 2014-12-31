<?php
return [
    'aliases' => [
        '@core' => dirname(__DIR__)
    ],
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
        ]
    ],
    'params' => [

    ],
];