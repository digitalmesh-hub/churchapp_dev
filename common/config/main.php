<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@runtime/cache',
        ],
         'authManager' => [
            'class' => 'yii\rbac\DbManager'
            // uncomment if you want to cache RBAC items hierarchy
            // 'cache' => 'cache',
        ],
        'checkAdminGroup' => [
            'class' => 'common\components\rbacGroup\UserGroupRule'
        ],
        'fileUploadHelper' => [
            'class' => 'common\components\FileuploadComponent'
        ],
        'communicationManager' => [
            'class' => 'common\components\CommunicationManager'
        ],
        'textMessageHandler' => [
            'class' => 'common\components\TextMessageHandler'
        ],
    	'PushNotificationHandler' => [
    		'class' => 'common\components\PushNotificationHandler',
    		//Push Notification Settings :TODO Need to change according to the Server
            'gcmAuthKey' => 'AIzaSyBdFrp0lbSudLPd-ZQuNIuSJ_V_NBL8gk8',
    		//'gcmAuthKey'=>'AIzaSyC_Bx9wTeRrKHr2hO3qmoX2peFZEdzyQWM', //app server key here
    		'serviceURL' =>'https://fcm.googleapis.com/fcm/send',
    	],
    	'NotificationHandler' => [
    		'class' => 'common\components\NotificationHandler'
    	],
        'BillExcelHandler' => [ 
            'class' => 'common\components\BillExcelHandler' 
        ],
        'BillCsvHandler' => [ 
            'class' => 'common\components\BillCsvHandler' 
        ],
        'MoneyFormat' => [
            'class' => 'common\components\MoneyFormat'
        ],
        'EmailHandler' => [ 
            'class' => 'common\components\EmailHandler' 
        ] ,
        'ExcelHandler' => [ 
            'class' => 'common\components\ExcelHandler' 
        ],
        'consoleRunner' => [
            'class' => 'vova07\console\ConsoleRunner',
            'file' => '@rootPath/yii' // or an absolute path to console file
        ],
    ],
];
