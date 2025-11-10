<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Feedbacknotification;

/**
 * This is the model class for table "feedbacknotification".
 *
 * @property int $id
 * @property int $userid
 * @property int $feedbackid
 * @property string $createddatetime
 *
 * @property Feedback $feedback
 * @property Usercredentials $user
 */
class ExtendedFeedbacknotification extends Feedbacknotification
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feedbacknotification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'feedbackid', 'createddatetime'], 'required'],
            [['userid', 'feedbackid'], 'integer'],
            [['createddatetime'], 'safe'],
            [['feedbackid'], 'exist', 'skipOnError' => true, 'targetClass' => Feedback::className(), 'targetAttribute' => ['feedbackid' => 'feedbackid']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'Userid',
            'feedbackid' => 'Feedbackid',
            'createddatetime' => 'Createddatetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedback()
    {
        return $this->hasOne(Feedback::className(), ['feedbackid' => 'feedbackid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
    }
    /**
     * To send feedback notification
     */
    public static function feedbackNotification($model,$institutionId,$userId,$userType)
    {
    	$pushNotificationSender = Yii::$app->PushNotificationHandler;
    	$feedbackId = $model->feedbackid;
    	
    	$memberDetails = ExtendedMember::getMemberId($userId, $institutionId, $userType);
    	$memberId = $memberDetails['memberid'];
        if($userType == 'M'){
    	   $memberResponse = ExtendedMember::getMemberName($memberId);
        }
        else{
            $memberResponse = ExtendedMember::getSpouseName($memberId);
        }
yii::error('memberres'.print_r($memberResponse,true));
    	$feedbackPrivilegeId = ExtendedPrivilege::MANAGE_FEEDBACKS;
    	$deviceDetailsModel = new ExtendedDevicedetails();
    	$deviceDetails = $deviceDetailsModel->getDeviceDetails($institutionId, $feedbackPrivilegeId);
    	$fields    = ['userid','feedbackid','createddatetime'];
    	$bachInsert = [];
    	$notificationSent =[];
    	foreach ($deviceDetails as $deviceDetail){


    		$userId =		   	$deviceDetail[ 'id'];
    		$deviceid 	= 	$deviceDetail['deviceid'];
    		$devicetype =   $deviceDetail['devicetype'];
    		//$memberId	=   $deviceDetail['memberid'];
    		$usertype	=  $deviceDetail['membertype'];
    		$institutionId = $deviceDetail['institutionid'];
    		$institutionName =	$deviceDetail ['institutionname'];
    		$membernotification = $deviceDetail['membernotification'];
    		$spousenotification = $deviceDetail['spousenotification'];
    		
    		
    		$sentMemberId = null;
    		if (strtolower($usertype) == "m" && $membernotification == 1)
    		{
    			$sentMemberId = $deviceid;
    		}
    		if (strtolower($usertype) == "s" && $spousenotification == 1)
    		{
    			$sentMemberId = $deviceid;
    		}
    		if ($sentMemberId){
    			$bachInsert[] = [$userId,$feedbackId,gmdate('Y-m-d H:i:s')];
    		
    			$firstName  =  $memberResponse['firstName'] ? $memberResponse['firstName']:'';
    			$middleName =  !empty($memberResponse['middleName']) ? $memberResponse['middleName']: '';
    			$lastName   =  $memberResponse['lastName'] ? $memberResponse['lastName']:'';
    			$memberName = $firstName . ' '.$middleName. ' '. $lastName;
    			$message    = "New feedback received from " . $memberName;
    		
    			$notificationType = 'feedback';
    			$requestData  = $pushNotificationSender->setPushNotificationRequest($sentMemberId,$message,$notificationType,$institutionId,$feedbackId,$institutionName,strtolower($devicetype),$userId);
    			$response     = $pushNotificationSender->sendNotification(strtolower($devicetype), $sentMemberId, $requestData);
    		
    			if($response){
    				$notificationSent[] = [$userId,$feedbackId,gmdate('Y-m-d H:i:s')];
    			}
    		}
    		 
    	}
    	if (count($bachInsert)>0){
    	
    		Yii::$app->db->createCommand()->batchInsert('feedbacknotification', $fields,$bachInsert )->execute();
    	
    	}
    	
    	if (count($notificationSent)>0){
    	
    		Yii::$app->db->createCommand()->batchInsert('feedbacknotificationsent', $fields,$notificationSent )->execute();
    	
    	}
    }
}
