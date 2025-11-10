<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

$config = [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'defaultRoute' => 'account/home',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\extendedmodels\ExtendedUserCredentials',
            'enableAutoLogin' => true,
            'loginUrl' => ['account/login']
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                ],
                // Uncomment this to log database queries for debugging
                // [
                //     'class' => 'yii\log\FileTarget',
                //     'categories' => ['yii\db\Command::query', 'yii\db\Command::execute'],
                //     'levels' => ['info'],
                //     'logFile' => '@runtime/logs/db.log',
                //     'logVars' => [],
                // ],
            ],
        ],
        'errorHandler' => [
            'class' => 'backend\components\error\ErrorHandler',
            'errorAction' => 'site/error'
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'cache' => 'cache', // Use the configured cache component
            'enableStrictParsing' => false,
            'rules' => [
                '<controller:\w+\-*\w*>/<id:\d+>' => '<controller>/view',
                '<controller:\w+\-*\w*>/<action:\w+\-*\w*>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+\-*\w*>/<action:\w+\-*\w*>' => '<controller>/<action>',
            	'member/member-edit/<id:\w+>' => 'member/member-edit',
		        '/API/account/logon-json' => 'account/old-system-support',
 		        '/API/account/ChangeUser-json' => 'account/old-system-support',
                '/API/account/generateOtp-json' => 'account/old-system-support',
                '/API/notifications/setViewedAnnouncementsAndEvents-json' => 'account/old-system-support',
                '/API/account/checkAppUpateAvailable-json' => 'account/old-system-support',
                '/service/InstitutionImage/Original/Thumbnail/Thumbnail/48.jpg' => 'account/old-system-support',
                '//service/InstitutionImage/Original/Thumbnail/Thumbnail/48.jpg' => 'account/old-system-support',
                '/Account/LogOn' => 'account/login'
            ],
        ],
        'assetManager' => [
            'linkAssets' => true,
            'appendTimestamp' => true,
        ], 
        
    ], // End of components array
    'params' => $params,
];
return $config;
