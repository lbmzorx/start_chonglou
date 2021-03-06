<?php

namespace backend\modules\system\controllers;

use yii\web\Controller;

/**
 * Default controller for the `system` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionPhpInfo(){
        return $this->renderPartial('php-info');
    }
}
