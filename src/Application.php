<?php

namespace exchangecore\filemanager;

use Yii;
use yii\base\InvalidConfigException;

class Application extends \yii\web\Application
{
    public $applicationControllerNamespace = 'application\controllers';
    public $isInstalled = true;

    /**
     * @inheritdoc
     */
    public function preInit(&$config)
    {
        if (!isset($config['basePath'])) {
            $config['basePath'] = dirname($_SERVER['SCRIPT_FILENAME']) . '/application';
            $config['components']['request']['enableCookieValidation'] = false;

            $this->isInstalled = false;
        }

        parent::preInit($config);
    }

    protected function bootstrap()
    {
        parent::bootstrap();

        $this->on(self::EVENT_BEFORE_ACTION, ['exchangecore\filemanager\events\InstallerCheck', 'check']);
    }

    /**
     * @inheritdoc
     */
    public function createControllerByID($id)
    {
        $pos = strrpos($id, '/');
        if ($pos === false) {
            $prefix = '';
            $className = $id;
        } else {
            $prefix = substr($id, 0, $pos + 1);
            $className = substr($id, $pos + 1);
        }
        if (!preg_match('%^[a-z][a-z0-9\\-_]*$%', $className)) {
            return null;
        }
        if ($prefix !== '' && !preg_match('%^[a-z0-9_/]+$%i', $prefix)) {
            return null;
        }
        $className = str_replace(' ', '', ucwords(str_replace('-', ' ', $className))) . 'Controller';

        $vendorClassName = ltrim($this->controllerNamespace . '\\' . str_replace('/', '\\', $prefix)  . $className, '\\');
        $applicationClassName = ltrim($this->applicationControllerNamespace . '\\' . str_replace('/', '\\', $prefix)  . $className, '\\');
        if (strpos($className, '-') !== false) {
            return null;
        }

        if (class_exists($applicationClassName)) {
            $className = $applicationClassName;
        } elseif (class_exists($vendorClassName)) {
            $className = $vendorClassName;
        } else {
            return null;
        }

        if (is_subclass_of($className, 'yii\base\Controller')) {
            return Yii::createObject($className, [$id, $this]);
        } elseif (YII_DEBUG) {
            throw new InvalidConfigException("Controller class must extend from \\yii\\base\\Controller.");
        } else {
            return null;
        }
    }
}