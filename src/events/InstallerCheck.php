<?php

namespace exchangecore\filemanager\events;

class InstallerCheck
{
    /**
     * @param \yii\base\Event $event
     */
    public static function check($event)
    {
        $allowedControllers = [
            'exchangecore\\filemanager\\controllers\\InstallController',
            'yii\\debug\\controllers\\DefaultController',
        ];
        if (\Yii::$app->isInstalled === false &&
            !in_array(\Yii::$app->controller->className(), $allowedControllers)) {
            \Yii::$app->response->redirect(['install/index']);
        }
    }
}