<?php

namespace exchangecore\filemanager\controllers;


use exchangecore\filemanager\models\install\DbConfig;
use yii\web\Controller;

class InstallController extends Controller
{
    public $layout = '@core/views/layouts/install';

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionDbConfig()
    {
        $model = new DbConfig();

        if (\Yii::$app->session->get('install-database') !== null) {
            $model->setAttributes(\Yii::$app->session->get('install-database'));
        }

        if ($model->load($_POST) && $model->validate()) {
            \Yii::$app->session->set('install-database', $model->getAttributes(['type', 'host', 'database', 'username', 'password']));
            return $this->actionMigrations();
        }

        return $this->renderPartial('dbConfig', ['model' => $model]);
    }

    public function actionMigrations()
    {

    }
}