<?php

namespace exchangecore\filemanager\controllers;

use Yii;
use exchangecore\filemanager\components\BaseAuthenticationController;
use exchangecore\filemanager\models\authentication\types\LocalLoginForm;

class AuthenticationLocalController extends BaseAuthenticationController
{
    public function actionLogin()
    {
        if(!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LocalLoginForm();
        if($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render(
                '//authentication/local-login',
                [
                    'model' => $model
                ]
            );
        }
    }
}