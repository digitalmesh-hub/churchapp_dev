<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\basemodels\BaseModel;
use common\components\PushNotificationRequestParamKeys;

class PublishNotificationController extends Controller
{

    public $eventType;
    public $eventId;
    public $noteHead;
    public $institutionId;
    public $familyUnitId;
    public $institutionName;
    public $createdBy;
    public $activityDate;
    public $venue;
    public $expiryDate; 
    public $batch;

    
    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            [
                'eventType',
                'eventId',
                'noteHead',
                'institutionId',
                'familyUnitId',
                'institutionName',
                'createdBy',
                'activityDate',
                'venue',
                'expiryDate',
                'batch'
            ]
        );
    
    }
    
    
    public function actionPublish()
    {
        $eventType = $this->eventType;
        $eventId   = $this->eventId; 
        $noteHead  = $this->noteHead; 
        $institutionid = $this->institutionId; 
        $familyUnitId = !empty($this->familyUnitId)?$this->familyUnitId:null;
        $institutionName = $this->institutionName;
        $activityDate = $this->activityDate;
        $venue = $this->venue;
        $pushNotificationHandler = Yii::$app->PushNotificationHandler;
        $notificationHandler = Yii::$app->NotificationHandler;
        $createdBy = $this->createdBy;
        $batches =  !empty($this->batch) ? explode(',',$this->batch) : null;
       // Yii::error('testr'.print_r($batches,1));
        if ($eventType == "E") {
            $deviceList = BaseModel::getAllEventDevices($institutionid, $eventId, $familyUnitId);
        } else {
            $deviceList = BaseModel::getAnnouncementDevices($eventId, $institutionid, $familyUnitId);
        }
        Yii::error('device'.print_r($deviceList,1));
        if($deviceList){
            $batchArray = [];
            $baseModel = new BaseModel();
            $batchArrays = $baseModel->getMemberBatchArray($institutionid);
            
            foreach($batchArrays as $batch) {
                $batchArray[$batch['userid']] =  $batch['batch'];
            }
  
            foreach($deviceList as $device){  
                if($batches!=NULL) {
                    //$memberbatch = BaseModel  :: getMemberInstitutionBatch($device['userid'],$institutionid);
                    if(!in_array($batchArray[$device['userid']], $batches)) {
                        //Yii::error('notin'.print_r($device['batch'],1));
                        continue;
                    } 
                    // else  {
                    //     Yii::error('innnarr'.print_r($device['batch'],1));
                    // }
                }
                
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
        
                //Conversation topic count
                // $conversationTopicNotification =  BaseModel::getUnreadConversationCount($device['id']);
                // if ($conversationTopicNotification->Status == true && $conversationTopicNotification->value != 0){
                //     $conversationTopicCount = $conversationTopicNotification->value;
                // }
                if ($sentMemberId) {
                    if (!empty($activityDate)){
                        if($eventType == "E") {
                           $activitydate = date_format(date_create($activityDate),Yii::$app->params['dateFormat']['notificationDateFormat']);
                            if ($venue) {
                                $message = $noteHead . " at " . $venue . " on " .$activitydate;
                            } else {
                                $message = $noteHead . " on " .$activitydate;
                           }
                        } else {
                            $activitydate = date('d/m/Y', strtotimeNew($activityDate));
                            $message = $noteHead . " on " .$activitydate;
                        }

                    } else {
                        $message = $noteHead;
                    }
        
                    //Sent successfull event notification details
                    $notifyType = $eventType;
                    $status = $notificationHandler->addEventSentDetails($device['deviceid'], $userId, $eventId, $notifyType, $institutionid, 'device');
                   
                    //Notification data
                    if($eventType == "E"){
                        $notificationType = 'event';
                    }else{
                        $notificationType = 'announcements';
                    }
                    // $notificationData = $pushNotificationHandler->setPushNotificationRequest($device['deviceid'], $message, $notificationType, $institutionid, $eventId, $institutionName, strtolower($device['devicetype']),$device['userid']);
                    $notificationData  = $pushNotificationHandler->getPushNotificationRequestUsingInfo([
    						PushNotificationRequestParamKeys::MESSAGE => $message,
    						PushNotificationRequestParamKeys::NOTIFICATION_TYPE => $notificationType,
    						PushNotificationRequestParamKeys::INSTITUTION_ID => $institutionid,
    						PushNotificationRequestParamKeys::INSTITUTION_NAME => $institutionName,
    						PushNotificationRequestParamKeys::NOTIFICATION_ID => $eventId,
    						PushNotificationRequestParamKeys::DEVICE_TYPE => strtolower($device['devicetype']),
    						PushNotificationRequestParamKeys::USER_ID => $device['userid'],
    						PushNotificationRequestParamKeys::EXPIRY_ON => $this->expiryDate
    					]);
                    //Push notification
                    if($notificationData){
                        //return $notificationData;
                        $pushNotificationHandler->sendNotification($device['devicetype'], $device['deviceid'], $notificationData);
        
                        $isSent = true;
                        $notificationStatus = $notificationHandler->saveNotificationDetails($device['devicedetailid'], $userId, $eventId, null,  $notifyType, $institutionid, $createdBy);
                        $notificationStatus = $notificationHandler->saveSuccessEventDetails($device['deviceid'], $userId, $eventId, $notifyType, $institutionid, 'device');
                        
                    }
                }
            }
        }  
    }
} 
