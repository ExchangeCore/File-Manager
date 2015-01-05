<?php

namespace exchangecore\filemanager\controllers;


use exchangecore\filemanager\models\install\DbConfig;
use Yii;
use yii\db\Connection;
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

        if (Yii::$app->session->get('install-database') !== null) {
            $model->setAttributes(\Yii::$app->session->get('install-database'));
        }

        if ($model->load($_POST) && $model->validate()) {
            Yii::$app->session->set('install-database', $model->getAttributes(['type', 'host', 'database', 'username', 'password']));
            Yii::$app->session->set('db-config', $model->buildConnectionParams());
            return $this->actionMigrations();
        }

        return $this->renderPartial('dbConfig', ['model' => $model]);
    }

    public function actionMigrations()
    {
        $migrator = new \exchangecore\filemanager\components\Migrator();
        Yii::$app->set('db', new Connection(Yii::$app->session->get('db-config')));
        $migrator->migrationPath = '@core/migrations';
        $success = $migrator->upgrade();

        return $success;
    }
}