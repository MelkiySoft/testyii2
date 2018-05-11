<?php

namespace app\controllers;

use yii\web\Controller;

class ClientController extends Controller
{
    public function actionIndex() {
        return $this->render('index');
    }

    public function actionGetsha1($str) {
        return sha1($str);
    }
}