<?php

namespace exchangecore\filemanager\controllers;

use Yii;
use yii\web\Controller;
use exchangecore\filemanager\models\authentication\AuthenticationType;

class AuthenticationController extends Controller
{
    public function actionIndex()
    {
        $authTypes = AuthenticationType::findEnabledAuthenticationTypes();
        if(count($authTypes) == 1) {
            $this->redirect(['authentication' . $authTypes[0]->Handle . '/login']);
        }
        return $this->render('index');
    }

    public function actionLogout()
    {
        Yii::$app->getUser()->logout();
        $this->redirect('/');
    }
}