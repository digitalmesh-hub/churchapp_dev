<?php
namespace common\components;

use Yii;
use yii\base\Component;

class TextMessageHandler extends Component
{
	/**
	 * Sending sms to a single or group of numbers provided with given message
	 * @param array $numbers - Contains all the numbers to which sms to be send
	 * @param String $otp - otp number which replaces {V} in message
	 * @return String contain response code
	 */
	public function sendSMS($numbers,$message)
	{
		$status = false;
		if (!is_array($numbers)) {
			$numbers = array($numbers);
		}
		try{
			if(!empty($numbers)){
	            $numbers = implode(',', $numbers); // A single number or a comma-seperated list of numbers
	        }
	        $url= env('SMS_APIURL');
	        $apiKey = env('SMS_API_KEY');
	        $sender = env('SMS_SENDER');
            $route = env('SMS_ROUTE');
	        
	        // Prepare data for POST request
            $fields = [
                "sender_id" => $sender,
                "message" => $message,
                "route" => $route,
                "numbers" => $numbers,
            ];
	        
	        // Send the POST request with cURL
	        $ch = curl_init($url);
	        if(!$ch) {
	        	yii::error('curl init failed');
	        	return false;
	        	//handle well
	        }
	        
	        curl_setopt($ch, CURLOPT_POST, true);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "authorization: " . $apiKey,
                "Content-Type: application/json"
            ]);
	        if(yii::$app->params['proxyEnabled']){
	        	if (!is_null(\Yii::$app->params['proxy.host'])) {
	        		$proxy = \Yii::$app->params['proxy.host'];
	        		$proxy .= ':' . (\Yii::$app->params['proxy.port'] == -1 ? '80' : \Yii::$app->params['proxy.port']);
	        		curl_setopt($ch, CURLOPT_PROXY, $proxy);
	        	}
	        }
	        
	        $response = curl_exec($ch); // This is the result from the API
			yii::info('SMS content: '.$message);
	        yii::info($response); //log response data
	        $response = json_decode($response);
	        $status = $response?->return ;
	        
		} catch (\Exception $e){
			yii::error($e->getMessage());
			$status = false;
		}
		
		return $status;
		
	}
}