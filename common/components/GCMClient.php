<?php
/**
 * @author Remya
 * This component handle the push notification services for Android devices
 */
namespace common\components;
use yii;
use Google\Client;

class GCMClient{
	private $AuthKey;
	private $curlClient;
	private $ServiceURL;
	private $accessToken = '';
	private $tokenExpiresAt = 0;

	public function __construct($AuthKey,$ServiceURL){
	        
		$this->AuthKey		=	$AuthKey;
		$this->ServiceURL	=	$ServiceURL;
		\Yii::info("GCM Client initialized with Key{$this->AuthKey}",'FcmspushNotifications');
		$token = $this->getAccessToken();
		$this->accessToken = $token['access_token'];
		$this->tokenExpiresAt = $token['expires_at'];
	}
	private function getDefaultCurlConfig(){
		$curlOpt	=	[
			CURLOPT_URL				=>	$this->ServiceURL,
			CURLOPT_POST			=>	TRUE,
			CURLOPT_RETURNTRANSFER	=>	TRUE,
			CURLOPT_VERBOSE			=>	TRUE,
			CURLOPT_SSL_VERIFYHOST  => 0,
			CURLOPT_SSL_VERIFYPEER  => false,
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
		\Yii::trace('Curl Options : '.print_r($curlOptions, true), 'FcmspushNotifications');//echo "<pre>";print_r($curlOptions);die;
		return $curlOptions;
	}

    private function getAccessToken()
    {
        $client = new Client();
        $client->setAuthConfig(\Yii::$app->params['android.service.account.path']);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->useApplicationDefaultCredentials();
        $token = $client->fetchAccessTokenWithAssertion();
        $token['expires_at'] = time() + ($token['expires_in'] -5);
        return $token;
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
			Yii::error('cURL Error : '.curl_errno( $this->curlClient ), 'FcmspushNotifications'); //enable this if logging is required.
		}
		curl_close( $this->curlClient );
		return $result;
	}

	function sendMessage($message) 
	{
		if (!$this->accessToken || $this->tokenExpiresAt < time()) {
			$token = $this->getAccessToken();
			$this->accessToken = $token['access_token'];
			$this->tokenExpiresAt = $token['expires_at'];
		}
		$url = 'https://fcm.googleapis.com/v1/projects/' . \Yii::$app->params['android.project.id'] . '/messages:send';
		$headers = [
		 'Authorization: Bearer ' . $this->accessToken,
		 'Content-Type: application/json',
		 ];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => $message]));
		$response = curl_exec($ch);
		Yii::info($response, 'andriod_request_response');
		 if ($response === false) {
			Yii::error(curl_error($ch), 'andriod_request_error');
		 	//throw new \Exception('Curl error: ' . curl_error($ch));
		 }
		curl_close($ch);
		return json_decode($response, true);
	  }
}
