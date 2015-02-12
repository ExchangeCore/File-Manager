<?php

namespace exchangecore\filemanager\assets;


use yii\web\AssetBundle;

class InstallAsset extends AssetBundle
{
    public $sourcePath = '@core/web/install';

    public $css = [
        'styles.css'
    ];

    public $js = [
        'install.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'exchangecore\filemanager\assets\NProgressAsset'
    ];
}