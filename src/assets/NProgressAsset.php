<?php

namespace exchangecore\filemanager\assets;


use yii\web\AssetBundle;

class NProgressAsset extends AssetBundle
{
    public $sourcePath = '@bower/nprogress';

    public $css = [
        'nprogress.css'
    ];

    public $js = [
        'nprogress.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}