<?php
/**
 * Created by IntelliJ IDEA.
 * User: jmeyer
 * Date: 2/25/15
 * Time: 12:57 PM
 */

namespace exchangecore\filemanager\components;

use yii\web\Controller;

abstract class BaseAuthenticationController extends Controller
{

    public $layout = '@core/views/layouts/authentication';
    public $defaultAction = 'login';

    abstract public function actionLogin();
} 