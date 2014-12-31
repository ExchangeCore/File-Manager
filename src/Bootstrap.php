<?php
namespace exchangecore\filemanager;

use yii;
use yii\helpers\ArrayHelper;

class Bootstrap
{
    public function __construct($applicationConfig)
    {
        $coreConfig = include __DIR__ . '/config/main.php';
        $config = ArrayHelper::merge($coreConfig, $applicationConfig);

        $application = new Application($config);
        $application->run();
    }
}