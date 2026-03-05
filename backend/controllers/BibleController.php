<?php
namespace backend\controllers;

use yii\web\Controller;

class BibleController extends Controller
{
    public $layout = false;
    
    public function actionIndex()
    {
        return $this->render('index');
    }
}