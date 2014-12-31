<?php

namespace exchangecore\filemanager\components;

use Yii;
use yii\base\InvalidCallException;
use yii\base\ViewContextInterface;

class View extends \yii\web\View
{
    /**
     * @inheritdoc
     */
    protected function findViewFile($view, $context = null)
    {
        if (strncmp($view, '@', 1) === 0) {
            // e.g. "@app/views/main"
            $file = Yii::getAlias($view);
        } else {
            if (strncmp($view, '//', 2) === 0) {
                // e.g. "//layouts/main"
                $file = $this->findViewFileHelper(ltrim($view, '/'));
            } elseif (strncmp($view, '/', 1) === 0) {
                // e.g. "/site/index"
                if (Yii::$app->controller !== null) {
                    $file = Yii::$app->controller->module->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
                } else {
                    throw new InvalidCallException("Unable to locate view file for view '$view': no active controller.");
                }
            } elseif ($context instanceof ViewContextInterface) {
                if (!(Yii::$app->controller->module instanceof \yii\web\Application)) {
                    $file = $context->getViewPath() . DIRECTORY_SEPARATOR . $view;
                } else {
                    $file = $this->findViewFileHelper(Yii::$app->controller->id . '/' . $view);
                }
            } elseif (($currentViewFile = $this->getViewFile()) !== false) {
                $file = dirname($currentViewFile) . DIRECTORY_SEPARATOR . $view;

                if (!file_exists($file)) {
                    $applicationViews = Yii::$app->getViewPath();
                    $coreViews = Yii::getAlias('@core/views');
                    $file = str_replace($applicationViews, $coreViews, $file);
                }
            }
        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }
        $path = $file . '.' . $this->defaultExtension;
        if ($this->defaultExtension !== 'php' && !is_file($path)) {
            $path = $file . '.php';
        }

        return $path;
    }

    protected function findViewFileHelper($file)
    {
        $applicationViews = Yii::$app->getViewPath();
        $coreViews = Yii::getAlias('@core/views');

        if (pathinfo($file, PATHINFO_EXTENSION) === '') {
            $file = $file . '.' . $this->defaultExtension;
        }

        if (file_exists($applicationViews . '/' . $file)) {
            $filePath = $applicationViews . '/' . $file;
        } elseif (file_exists($coreViews . '/' . $file)) {
            $filePath = $coreViews . '/' . $file;
        } else {
            $filePath = null;
        }

        if ($filePath === null) {
            if ($this->defaultExtension !== 'php') {
                if (file_exists($applicationViews . '/' . $file . '.php')) {
                    $filePath = $applicationViews . '/' . $file . '.php';
                } elseif (file_exists($coreViews . '/' . $file . '.php')) {
                    $filePath = $coreViews . '/' . $file . '.php';
                }
            }
        }

        if ($filePath === null) {
            throw new InvalidCallException('Unable to resolve view file for view ' . $file);
        }

        return $filePath;
    }
}