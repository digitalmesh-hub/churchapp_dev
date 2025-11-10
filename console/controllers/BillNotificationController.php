<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\basemodels\BaseModel;
use common\components\PushNotificationRequestParamKeys;

class BillNotificationController extends Controller
{

    public $eventType;
    public $eventId;
    public $year;
    public $month;
    public $institutionId;
    public $institutionName;
    public $createdBy;

    
    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            [
                'eventType',
                'eventId',
                'year',
                'month',
                'institutionId',
                'institutionName',
                'createdBy'
            ]
        );
    
    }
    
    
    public function actionBillNotification()
    {
        $eventType = $this->eventType;
        $eventId   = $this->eventId; 
        $year  = $this->year; 
        $month  = $this->month; 
        $institutionid = $this->institutionId; 
        $institutionName = $this->institutionName;
        $pushNotificationHandler = Yii::$app->PushNotificationHandler;
        $notificationHandler = Yii::$app->NotificationHandler;
        $createdBy = $this->createdBy;
        $month_name = date("F", mktime(0, 0, 0, $month, 10));
        $deviceList = BaseModel::getInstitutionMemberDevices($institutionid);
        $message = "$month_name $year Bill is ready";
        Yii::error('device'.print_r($deviceList,1));
        if($deviceList){
  
            foreach($deviceList as $device){ 
                
                $userId = $device['userid'];
                $deviceId   =   $device['deviceid'];
                $deviceType =   $device['devicetype'];
                $userType   =  $device['usertype'];
                $memberNotification = $device['membernotification'];
                $spouseNotification = $device['spousenotification'];
                $sentMemberId = null;
                if (strtolower($userType) == "m" && $memberNotification == 1){
                    $sentMemberId = $deviceId;
                }
                if (strtolower($userType) == "s" && $spouseNotification == 1) {
                    $sentMemberId = $deviceId;
                }
        
                if ($sentMemberId) {
                    
        
                    //Sent successfull event notification details
                    $notifyType = $eventType;
                    $status = $notificationHandler->addEventSentDetails($device['deviceid'], $userId, $eventId, $notifyType, $institutionid, 'device');
                   
                    $notificationData  = $pushNotificationHandler->getPushNotificationRequestUsingInfo([
    						PushNotificationRequestParamKeys::MESSAGE => $message,
    						PushNotificationRequestParamKeys::NOTIFICATION_TITLE => 'New bill is ready',
    						PushNotificationRequestParamKeys::NOTIFICATION_TYPE => "bill-uploaded",
    						PushNotificationRequestParamKeys::INSTITUTION_ID => $institutionid,
    						PushNotificationRequestParamKeys::INSTITUTION_NAME => $institutionName,
    						PushNotificationRequestParamKeys::NOTIFICATION_ID => $eventId,
    						PushNotificationRequestParamKeys::DEVICE_TYPE => strtolower($device['devicetype']),
    						PushNotificationRequestParamKeys::USER_ID => $device['userid']
    					]);
                    //Push notification
                    if($notificationData){
                        //return $notificationData;
                        $pushNotificationHandler->sendNotification($device['devicetype'], $device['deviceid'], $notificationData);
        
                        $isSent = true;
                        /* $notificationStatus = $notificationHandler->saveNotificationDetails($device['devicedetailid'], $userId, $eventId, null,  $notifyType, $institutionid, $createdBy); */
                        $notificationStatus = $notificationHandler->saveSuccessEventDetails($device['deviceid'], $userId, $eventId, $notifyType, $institutionid, 'device');
                        
                    }
                }
            }
        }  
    }
} 
