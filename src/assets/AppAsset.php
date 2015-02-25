<?php
namespace exchangecore\filemanager\assets;
use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@core/web/site';
    public $css = [
        'css/site.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}