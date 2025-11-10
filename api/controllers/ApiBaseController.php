<?php

namespace api\controllers;

use yii\helpers\ArrayHelper;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use api\components\ApiAuth;

class ApiBaseController extends \yii\rest\Controller
{
    /*
    * Set JSON as default API format
    */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'bootstrap'=> [
                    'class' => ContentNegotiator::className(),
                    'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    ],
                ],
                'authenticator' => [
                    'class' => ApiAuth::className(),
                ],
            ]
        );
    }
}