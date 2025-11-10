<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info', 'trace'],
                ],
            ],
        ],
        'errorHandler' => [
            'class' => 'frontend\components\error\ErrorHandler',
            'errorAction' => 'site/error'
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<controller:\w+\-*\w*>/<id:\w+>' => '<controller>/view',
                '<controller:\w+\-*\w*>/<action:\w+\-*\w*>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+\-*\w*>/<action:\w+\-*\w*>' => '<controller>/<action>',
                '/API/account/logon-json' => 'account/old-system-support',
                '/API/account/ChangeUser-json' => 'account/old-system-support',
                '/API/account/generateOtp-json' => 'account/old-system-support'
            ],
        ],
        
    ],
    'params' => $params,
];
