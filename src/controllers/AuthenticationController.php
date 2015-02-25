<?php

namespace exchangecore\filemanager\controllers;

use Yii;
use yii\web\Controller;
use exchangecore\filemanager\models\authentication\AuthenticationType;

class AuthenticationController extends Controller
{
    public $defaultAction = 'login';

    public function actionLogin()
    {
        $authTypes = AuthenticationType::findDefaultAuthenticationType();
        $this->redirect(['authentication-' . strtolower($authTypes->Handle) . '/login']);
    }

    public function actionLogout()
    {
        Yii::$app->getUser()->logout();
        $this->redirect('/');
    }
}