<?php

namespace common\components;

use yii;
use yii\base\ErrorException;
use yii\helpers\Json;
use yii\base\Component;
use common\components\APNSClient;
use common\components\APNSClientHTTP;
use common\components\GCMClient;
use common\models\extendedmodels\ExtendedEvent;

use common\components\PushNotificationRequestParamKeys;

class PushNotificationHandler extends Component
{
    public $gcmAuthKey;
    public $serviceURL;

    /**
     * Public function to send Push Notification
     * @param String $device_type  Operating system of the device 'android' or 'ios'
     * @param String $devise_key   Unique ID of the device
     * @param String $message      Data to be sent
     */
    public function sendNotification($device_type, $devise_key, $data)
    {

        $result = true;
        $device_type = strtolower($device_type);
        $to[] = $devise_key;
        
        try {
            if (strtolower($device_type) == "android") { // Android push notification
                $gcm_client = new GCMClient($this->gcmAuthKey, $this->serviceURL);

                $post_data  = [
                    'token' => $devise_key,
                    'data' => $data,
                ];
                
                $resp = $gcm_client->sendMessage($post_data);
                return empty($resp['error']) ? true : false;
            } elseif (strtolower($device_type) == "ios") { // iOS push notification
                // $apnsClient = new APNSClient(); /* deprecated ios legacy push notification */
                $apnsClient = new APNSClientHTTP();


                // Sends notifications and returns an int on success and boolean false on error
                // $resp = $apnsClient->send($devise_key, $data); /* deprecated ios legacy push notification */
                $resp = $apnsClient->send($to, $data); 
                if (!empty($resp)) {
                    return true;
                } else {
                    return false;
                }
            }

        } catch (ErrorException $e) {
            Yii::error($e, 'api_request');
            return false;
        }
    }
    
    /**
     * Set push Notification rqst
     */
    
    public function setPushNotificationRequest($registrationid,$message,$notificationType,$institutionId,$notificationInserId,$institutionName,$deviceType,$userId =null, $memberId=null ){
    
    	$pushNotificationDetails 	= [];
    	$pushNotificationDetails['contentTitle']   = $notificationType;
    	$pushNotificationDetails['message']		   = $message;
    	$pushNotificationDetails['type']		   = $notificationType;
    	$pushNotificationDetails['item-id']	       = (int)($notificationType == 'birthday' || $notificationType == 'anniversary') ? '':(int)$notificationInserId;
    	$pushNotificationDetails['institution']	   = $institutionName;
    	$pushNotificationDetails['institution-id'] = (int)$institutionId;
        $pushNotificationDetails['member-id'] = (!empty($memberId)) ? (int)$memberId: 0;
    	if ($userId){
    	    $eventModel = new ExtendedEvent();
    	    $notificationCount = $eventModel->getEventCount($userId, gmdate("Y-m-d H:i:s"));
    	    if ($notificationCount){
    	    
    	    
    	        $data ['announcementCount'] = (int)$notificationCount['announcementcount'];
    	        $data ['birthdayCount'] = ((int)$notificationCount['memberbirthday'] +(int)$notificationCount['spousebirthday']);
    	        $data ['anniversaryCount'] = (int)$notificationCount['weddingannuversery'];
    	        $data ['eventsCount'] = (int)$notificationCount['eventcount'];
    	        
    	        $count = $data ['announcementCount']+$data ['birthdayCount']+ $data ['anniversaryCount']+ $data ['eventsCount'];
    	    }else{
    	        $count = 1;
    	    }
    	    
    	}else{
    	    $count = 1;
    	}
    	if ($deviceType == 'android'){
            // Change item-id, institution-id, member-id to string for android push notification
            $pushNotificationDetails['item-id']	       = (string) $pushNotificationDetails['item-id'];
            $pushNotificationDetails['institution-id'] = (string) $pushNotificationDetails['institution-id'];
            $pushNotificationDetails['member-id'] = (string) $pushNotificationDetails['member-id'];
    		return $pushNotificationDetails;
    	}
    	if ($deviceType == 'ios'){
    		$pushNotificationDetails ['aps'] = [
    				'alert' =>[
    						'body' => $institutionName . " - " . $message,
    				],
    				'badge' => (int)$count,
    				'sound' => 'default'
    		];
    		return $pushNotificationDetails;
		}
		return null;
    }
    


    public function getSilentPushNotificationBasicRequest($notificationType, $deviceType) {

        $pushNotificationDetails 	= [];
    	$pushNotificationDetails['contentTitle']   = $notificationType;
        $pushNotificationDetails['type']		   = $notificationType;

        if ($deviceType == 'android'){
    		return $pushNotificationDetails;
    	}
    	else if ($deviceType == 'ios'){
    		/* $pushNotificationDetails ['aps'] = [
                'sound' => '',
                "content-available" => "1"
            ]; */
            $pushNotificationDetails ['aps'] = [
                'alert' =>[
                        'body' => '',
                ],
                'badge' => 1,
                'sound' => '',
                "contentAvailable" => true
        ];
            
    		return $pushNotificationDetails;
		}

    }



    public function getPushNotificationRequestUsingInfo($info) {

        $notificationTitle = $info[PushNotificationRequestParamKeys::NOTIFICATION_TITLE] ?? $info[PushNotificationRequestParamKeys::NOTIFICATION_TYPE];
        $notificationType = $info[PushNotificationRequestParamKeys::NOTIFICATION_TYPE];
        $message = $info[PushNotificationRequestParamKeys::MESSAGE];
        $notificationID = $info[PushNotificationRequestParamKeys::NOTIFICATION_ID];
        $institutionID = $info[PushNotificationRequestParamKeys::INSTITUTION_ID];
        $institutionName = $info[PushNotificationRequestParamKeys::INSTITUTION_NAME];
        $deviceType = $info[PushNotificationRequestParamKeys::DEVICE_TYPE];

        if (!$notificationType) {
            throw new \Exception("PushNotificationRequestParamKeys => The key '" . PushNotificationRequestParamKeys::NOTIFICATION_TYPE . "' is required.");
        }
        else if (!$message) {
            throw new \Exception("PushNotificationRequestParamKeys => The key '" . PushNotificationRequestParamKeys::MESSAGE . "' is required.");
        } 
        else if (!$notificationID) {
            throw new \Exception("PushNotificationRequestParamKeys => The key '" . PushNotificationRequestParamKeys::NOTIFICATION_ID . "' is required.");
        }
        else if (!$institutionID) {
            throw new \Exception("PushNotificationRequestParamKeys => The key '" . PushNotificationRequestParamKeys::INSTITUTION_ID . "' is required.");
        }
        else if (!$institutionName) {
            throw new \Exception("PushNotificationRequestParamKeys => The key '" . PushNotificationRequestParamKeys::INSTITUTION_NAME . "' is required.");
        }
        else if (!$deviceType) {
            throw new \Exception("PushNotificationRequestParamKeys => The key '" . PushNotificationRequestParamKeys::DEVICE_TYPE . "' is required.");
        }

        $pushNotificationDetails 	= [];
    	$pushNotificationDetails['contentTitle']   = $notificationTitle;
    	$pushNotificationDetails['message']		   = $message;
    	$pushNotificationDetails['type']		   = $notificationType;
    	$pushNotificationDetails['item-id']	       = (int)($notificationType == 'birthday' || $notificationType == 'anniversary') ? '':(int)$notificationID;
    	$pushNotificationDetails['institution']	   = $institutionName;
        $pushNotificationDetails['institution-id'] = (int)$institutionID;
        $pushNotificationDetails['member-id'] = 0;

        if (isset($info[PushNotificationRequestParamKeys::MEMBER_ID])) {
            $memberId = trim($info[PushNotificationRequestParamKeys::MEMBER_ID]);
            $pushNotificationDetails['member-id'] = (!empty($memberId)) ? (int)($memberId) : 0;
        }

        if (isset($info[PushNotificationRequestParamKeys::EXPIRY_ON])) {
            $expiry = date_create($info[PushNotificationRequestParamKeys::EXPIRY_ON]);
            $pushNotificationDetails['expires-on'] = date_format($expiry,"Y-m-d");
        }


    	if (isset($info[PushNotificationRequestParamKeys::USER_ID])){
            $eventModel = new ExtendedEvent();
            
            $notificationCount = $eventModel->getEventCount(trim($info[PushNotificationRequestParamKeys::USER_ID]), gmdate("Y-m-d H:i:s"));
            
    	    if ($notificationCount){
    	        $data ['announcementCount'] = (int)$notificationCount['announcementcount'];
    	        $data ['birthdayCount'] = ((int)$notificationCount['memberbirthday'] + (int)$notificationCount['spousebirthday']);
    	        $data ['anniversaryCount'] = (int)$notificationCount['weddingannuversery'];
    	        $data ['eventsCount'] = (int)$notificationCount['eventcount'];
    	        
    	        $count = $data ['announcementCount']+$data ['birthdayCount']+ $data ['anniversaryCount']+ $data ['eventsCount'];
    	    }else{
    	        $count = 1;
    	    }
    	    
    	} else {
    	    $count = 1;
        }
        
    	if ($deviceType == 'ios'){
    		$pushNotificationDetails ['aps'] = [
    				'alert' =>[
    						'body' => $institutionName . " - " . $message,
    				],
    				'badge' => (int)$count,
    				'sound' => 'default'
    		];
        } else {
            $pushNotificationDetails['item-id']	       = (string) $pushNotificationDetails['item-id'];
            $pushNotificationDetails['institution-id'] = (string) $pushNotificationDetails['institution-id'];
            $pushNotificationDetails['member-id'] = (string) $pushNotificationDetails['member-id'];
    		return $pushNotificationDetails;
        }

		return $pushNotificationDetails;
    }
}
