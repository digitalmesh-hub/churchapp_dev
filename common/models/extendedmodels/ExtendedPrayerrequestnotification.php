<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Prayerrequestnotification;

/**
 * This is the model class for table "prayerrequestnotification".
 *
 * @property int $id
 * @property int $prayerrequestid
 * @property int $userid
 * @property string $createddatetime
 *
 * @property Prayerrequest $prayerrequest
 * @property Usercredentials $user
 */
class ExtendedPrayerrequestnotification extends Prayerrequestnotification
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'prayerrequestnotification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['prayerrequestid', 'userid', 'createddatetime'], 'required'],
            [['prayerrequestid', 'userid'], 'integer'],
            [['createddatetime'], 'safe'],
            [['prayerrequestid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedPrayerrequest::className(), 'targetAttribute' => ['prayerrequestid' => 'prayerrequestid']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedUserCredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'prayerrequestid' => 'Prayerrequestid',
            'userid' => 'Userid',
            'createddatetime' => 'Createddatetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerrequest()
    {
        return $this->hasOne(ExtendedPrayerrequest::className(), ['prayerrequestid' => 'prayerrequestid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(ExtendedUserCredentials::className(), ['id' => 'userid']);
    }
    /**
     * To send prayer request notification
     */
    public static function prayerRequestNotification($model,$institutionId,$userId,$userType)
    {
    	
    	$pushNotificationSender = Yii::$app->PushNotificationHandler;
    	$prayerRequestId = $model->prayerrequestid;
    	$memberDetails = ExtendedMember::getMemberId($userId, $institutionId, $userType);
    	$memberId = $memberDetails['memberid'];
        if($userType == 'M'){
    	   $memberResponse = ExtendedMember::getMemberName($memberId);
        }
        else{
            $memberResponse = ExtendedMember::getSpouseName($memberId);
        }
    	$prayerRequestPrivilegeId = ExtendedPrivilege::MANAGE_PRAYER_REQUESTS;
    	$deviceDetailsModel = new ExtendedDevicedetails();
    	$deviceDetails = $deviceDetailsModel->getDeviceDetails($institutionId, $prayerRequestPrivilegeId);
    	$fields    = ['prayerrequestid','userid','createddatetime'];
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
    			$bachInsert[] = [$prayerRequestId,$userId,gmdate('Y-m-d H:i:s')];
    	
    			$firstName  =  $memberResponse['firstName'] ? $memberResponse['firstName']:' ';
    			// $middleName =  $memberResponse['middleName'] ? $memberResponse['middleName']:'';
    			$lastName   =  $memberResponse['lastName'] ? $memberResponse['lastName']:'';
                if(!empty($memberResponse['middleName'])){
                    $memberName = $firstName . ' '.$memberResponse['middleName']. ' '. $lastName;
                }
                else{
                    $memberName = $firstName . ' '. $lastName;
                }
    			$message    = "New prayer request received from " . $memberName;
    	
    			$notificationType = 'prayer-request';
    			$requestData  = $pushNotificationSender->setPushNotificationRequest($sentMemberId,$message,$notificationType,$institutionId,$prayerRequestId,$institutionName,strtolower($devicetype),$userId);
    			$response     = $pushNotificationSender->sendNotification(strtolower($devicetype), $sentMemberId, $requestData);
    	
    			if($response){
    				$notificationSent[] = [$prayerRequestId,$userId,gmdate('Y-m-d H:i:s')];
    			}
    		}
    		 
    	}
    	if (count($bachInsert)>0){
    		 
    		Yii::$app->db->createCommand()->batchInsert('prayerrequestnotification', $fields,$bachInsert )->execute();
    		 
    	}
    	 
    	if (count($notificationSent)>0){
    		 
    		Yii::$app->db->createCommand()->batchInsert('prayerrequestnotificationsent', $fields,$notificationSent )->execute();
    		 
    	}
    }
}
