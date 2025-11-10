<?php
return [
    'class'=>'yii\web\UrlManager',
    'enablePrettyUrl'=> true,
    'showScriptName'=> false,
    'rules'=>[
        '<controller:\w+>/<id:\d+>' => '<controller>/view',
        '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
   ]
];

?>