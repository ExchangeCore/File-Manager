<?php

namespace exchangecore\filemanager\controllers;


use exchangecore\filemanager\components\Configuration;
use exchangecore\filemanager\components\Migrator;
use exchangecore\filemanager\models\install\DbConfig;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class InstallController extends Controller
{
    public $layout = '@core/views/layouts/install';

    public function init()
    {
        parent::init();

        /** @var DbConfig $dbConfig */
        $dbConfig = Yii::$app->session->get('db-config');
        if ($dbConfig !== null) {
            Yii::$app->set('db', $dbConfig->getConnection());
        }
    }

    public function actionIndex()
    {
        Yii::$app->session->open();
        Yii::$app->session->destroy();

        return $this->render('index');
    }

    public function actionDbConfig()
    {
        $model = new DbConfig();

        if (Yii::$app->session->get('db-config') !== null) {
            $model = Yii::$app->session->get('db-config');
        }

        if ($model->load($_POST) && $model->validate()) {
            Yii::$app->session->set('db-config', $model);

            Yii::$app->response->headers->add('X-Install-Load', Url::to(['install/migrations']));
            return null;
        }

        return $this->renderPartial('dbConfig', ['model' => $model]);
    }

    public function actionMigrations()
    {
        $migrator = new Migrator();
        $migrator->migrationPath = '@core/migrations';
        $migrations = $migrator->getNewMigrations();
        $migrationCount = Yii::$app->session->get('install-migrations');
        if ($migrationCount === null) {
            $migrationCount = count($migrations);
            Yii::$app->session->set('install-migrations', $migrationCount);
        }

        $success = true;
        if (!empty($migrations)) {
            $success = $migrator->upgrade(1);
            if ($success === true) {
                Yii::$app->response->headers->add('X-Install-Load', Url::to(['install/migrations']));
            } else {
                Yii::$app->response->headers->add('X-Install-Load', Url::to(['install/rollback']));
            }
        } else {
            Yii::$app->response->headers->add('X-Install-Load', Url::to(['install/write-config']));
        }

        return $this->renderPartial('migrations',
           [
                'success' => $success,
                'totalMigrationCount' => $migrationCount,
                'runMigrationCount' => $migrationCount - count($migrations) + 1
            ]
        );
    }

    public function actionRollback()
    {
        $migrator = new Migrator();
        $migrator->migrateTo($migrator::BASE_MIGRATION);
        Yii::$app->response->headers->add('X-Install-Load', URL::to(['install/db-config']));

        $this->renderPartial('rollback');
    }

    public function actionWriteConfig()
    {
        /** @var DbConfig $dbConfig */
        $dbConfig = Yii::$app->session->get('db-config');
        $configuration = [
            'basePath' => Yii::getAlias('@app'),
            'components' => [
                'request' => [
                    'cookieValidationKey' => Yii::$app->security->generateRandomString(32)
                ],
                'db' => $dbConfig->getConfig(),
           ],
            'params' => [

            ],
        ];

        $config = new Configuration();
        $config->createConfiguration('@app/config/main.php', $configuration);

        return $this->renderPartial('finish');
    }
}