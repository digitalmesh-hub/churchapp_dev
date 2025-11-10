<?php

/**
 * @author Suvin
 * This component handles the push notification services 
 * for Android devices
 */

namespace common\components;

use yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class APNClient{

	private $AuthKey;
	private $curlClient;
	private $ServiceURL;

	public function __construct(){
	        
		$this->AuthKey = yii::$app->params['pushNotification']['android']['authKey'];
		$this->ServiceURL =	yii::$app->params['pushNotification']['android']['path'];
		\Yii::info("APN Client initialized with Key{$this->AuthKey}",'APNpushNotifications');
	}
	
	public function actions() {
        return [
                  'error' => [
                            'class' => 'yii\web\ErrorAction',
                  ],
        ];
    }

	private function getDefaultCurlConfig(){
		$curlOpt	=	[
			CURLOPT_URL				=>	$this->ServiceURL,
			CURLOPT_POST			=>	TRUE,
			CURLOPT_RETURNTRANSFER	=>	TRUE,
			CURLOPT_VERBOSE			=>	TRUE,
			CURLOPT_SSL_VERIFYHOST  =>  0,
			CURLOPT_SSL_VERIFYPEER  =>  false,
		];
 		if(yii::$app->params['proxyEnabled']){
			if (!is_null(\Yii::$app->params['proxy']['host'])) {
				$proxy = \Yii::$app->params['proxy']['host'];
				$proxy .= ':' . (\Yii::$app->params['proxy']['port'] == -1 ? '80' : \Yii::$app->params['proxy']['port']);
				$curlOpt[CURLOPT_PROXY] = $proxy;
			}
		}
		return $curlOpt;
	}

	public function configureCurlOptions($post_data){
		$curlOptions	=	$this->getDefaultCurlConfig();
		$curlOptions[CURLOPT_HTTPHEADER]	=	$this->getHeaders();
		$curlOptions[CURLOPT_POSTFIELDS]	=	$post_data;
		\Yii::trace('Curl Options : '.print_r($curlOptions, true), 'APNpushNotifications');
		// echo "<pre>";
		// print_r($curlOptions);
		// die();
		return $curlOptions;
	}

	public function getHeaders(){
		return [
					'Authorization: key=' . $this->AuthKey,
					'Content-Type: application/json'
				];
	}

	public function send($post_data){

	    \Yii::info('Initializing cURL', __METHOD__);
		$this->curlClient	=	curl_init();
		curl_setopt_array($this->curlClient, $this->configureCurlOptions($post_data));
		\Yii::trace('Data : ' .print_r($post_data, true), __METHOD__);
		$result = curl_exec( $this->curlClient );
		\Yii::info('cURL Completed', __METHOD__);
		if ( curl_errno( $this->curlClient ) ){
			echo 'HTTP error: ' . curl_error( $this->curlClient );
			\Yii::error('cURL Error : '.curl_error( $this->curlClient ), 'APNpushNotifications'); 
			// enable this if logging is required.
		}
		curl_close( $this->curlClient );
		return $result;

	}

}

// To execute - Yii::$app->APNClient->send($post_data);
