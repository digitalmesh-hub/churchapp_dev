<?php
namespace backend\controllers;

use backend\controllers\BaseController;
use common\helpers\Utility;
use Yii;
use yii\debug\models\search\Base;
use yii\web\Controller;
use yii\web\HttpException;

/**
 * Site controller
 */
class SiteController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
       return [
        ];
    }

    public function actionException($exception = '')
    {
        return $this->render('error',['exception' => $exception]);
    }
    public function actionError()
    {
        $this->layout = false;
        $exception = Yii::$app->errorHandler->exception;
        if ($exception instanceof HttpException) {
            return $this->render('http-error',['exception' => $exception]);
        } else {
            return $this->render('error', ['exception' => $exception]);
        }
    }
}
