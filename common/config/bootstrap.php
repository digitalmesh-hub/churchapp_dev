<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('api', dirname(dirname(__DIR__)) . '/api');
Yii::setAlias('@service', dirname(dirname(__DIR__)) . '/service');
Yii::setAlias('payment', dirname(dirname(__DIR__)) . '/payment');
// Url Aliases
Yii::setAlias('@backendUrl', 'https://admin.re-member.co.in/');
