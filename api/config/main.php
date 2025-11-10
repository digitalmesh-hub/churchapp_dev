<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    /*'controllerNamespace' => 'api\controllers',*/
    'defaultRoute' => 'v3/account',
    'bootstrap' => ['log'],
    'modules' => [
    'v3' => [
            'class' => 'api\modules\v3\module'
        ]
    ],
    'controllerMap' =>['account' => 'api\modules\v3\controllers\AccountController'],
    'components' => [
        'request' => [
                'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\extendedmodels\ExtendedUserCredentials',
            'enableSession' => false,
            'loginUrl' => null,
            'enableAutoLogin' => true,
        ],
        'response'=> [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            'on beforeSend' => function ($event) {
            $logResp = [];
            $logResp['http_method'] = \Yii::$app->request->method;
            $logResp['post_data'] = \Yii::$app->request->bodyParams;
            $logResp['get_data'] = \Yii::$app->request->queryParams;
            $response = $event->sender;
            if ($response->data !== null) {
                if ($response->isServerError) {
                        $response->data = [
                        'statusCode' => isset($response->data['status'])? $response->data['status']
                        :api\components\ApiResponseCode::ERR_INTERNAL_SERVER_ERROR,
                        'message'=> api\components\ApiResponseCode::responseMessagesFromCode(api\components\ApiResponseCode::ERR_INTERNAL_SERVER_ERROR),
                        'data' => new \stdClass()
                        ];
                } elseif ($response->isSuccessful) {
                    $response->data = [
                        'statusCode' => \common\helpers\Utility::getValue($response->data, 'statusCode') ,
                        'message' => \common\helpers\Utility::getValue($response->data, 'message'),
                        'data' => \common\helpers\Utility::getValue($response->data,'data')
                    ]; 
                } elseif ($response->isClientError) {
                    $response->data = [
                        'statusCode' => isset($response->statusCode) ? ($response->statusCode == 401 ) ? 602 : $response->statusCode
                        :api\components\ApiResponseCode::ERR_INTERNAL_SERVER_ERROR,
                        'message'=> isset($response->statusCode) ? api\components\ApiResponseCode::responseMessagesFromCode(($response->statusCode == 401 ) ? 602 : $response->statusCode) : api\components\ApiResponseCode::responseMessagesFromCode(api\components\ApiResponseCode::ERR_INTERNAL_SERVER_ERROR),
                        'data' => new \stdClass()
                        ];
                    $response->statusCode = 200;
                }
            }
            $logResp['data'] = $response->data;
            \Yii::info("Response : ".var_export($logResp, true), 'api_request');
        },
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'info', 'trace'],
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'categories' => ['yii\web\HttpException:*'],
                    'logFile' => '@app/runtime/logs/http_errors.log',
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['api_request'],
                    'logFile' => '@app/runtime/logs/api_request.log',
                    'logVars' => [],
                ],
            ],
        ],
        'urlManager' => require(__DIR__.'/_urlManager.php'),
        'urlManagerBackend' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => Yii::getAlias('@backendUrl'),
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ],
    'params' => $params,
];
