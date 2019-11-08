<?php

namespace tas\social\controllers;

use yii\web\Controller;

/**
 * Default controller for the `social` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->redirect(['messenger/index']);
    }
}
