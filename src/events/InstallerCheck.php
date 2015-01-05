<?php

namespace exchangecore\filemanager\events;

class InstallerCheck
{
    /**
     * @param \yii\base\Event $event
     */
    public static function check($event)
    {
        if (\Yii::$app->isInstalled === false &&
            \Yii::$app->controller->className() !== 'exchangecore\filemanager\controllers\InstallController') {
            \Yii::$app->response->redirect(['install/index']);
        }
    }
}