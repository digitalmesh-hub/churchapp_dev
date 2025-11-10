<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Profileupdatenotification;
use common\models\extendedmodels\ExtendedPrivilege;
use common\models\extendedmodels\ExtendedProfileupdatenotificationsent;
use common\models\extendedmodels\ExtendedDevicedetails;
use common\components\PushNotificationHandler;

/**
 * This is the model class for table "profileupdatenotification".
 *
 * @property int $id
 * @property int $userid
 * @property int $memberid
 * @property string $createddatetime
 *
 * @property Member $member
 * @property Usercredentials $user
 */
class ExtendedProfileupdatenotification extends Profileupdatenotification
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profileupdatenotification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'memberid', 'createddatetime'], 'required'],
            [['userid', 'memberid'], 'integer'],
            [['createddatetime'], 'safe'],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
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
            'memberid' => 'Memberid',
            'createddatetime' => 'Createddatetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
    }
    
    /**
     * To delete the profile notification details
     * @param unknown $memberId
     */
    public function deleteFromProfileNotification($memberId){
    	
    	$sql = " SET sql_safe_updates=0; DELETE FROM profileupdatenotification WHERE memberid=:memberId";
    	
    	$result = Yii::$app->db->createCommand($sql)
    	->bindValue(':memberId' , $memberId )
    	->execute();
    }
    /**
     * To send profile update notification
     */
    
    public static function profileUpdateNotification($model){
    
    	$pushNotificationSender = Yii::$app->PushNotificationHandler;
    	 
    	$memberId = $model->memberid;
    	$profileupdateNotificationModel = new ExtendedProfileupdatenotification();
    	$profileupdateNotificationsentModel = new ExtendedProfileupdatenotificationsent();
    	$deviceDetailsModel = new ExtendedDevicedetails();
    	 
    	//deleting from notification tables
    	$profileupdateNotificationModel->deleteFromProfileNotification($memberId);
    	$profileupdateNotificationsentModel->deleteFromProfileNotificationSent($memberId);
    	 
    	$institutionId = $model->institutionid;
    	$prayerrequestPrivilegeId= ExtendedPrivilege::APPROVE_PENDING_MEMBER;
    	$deviceDetails = $deviceDetailsModel->getDeviceDetails($institutionId, $prayerrequestPrivilegeId);
    	 
    	$fields     = ['userid','memberid','createddatetime'];
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
    			$bachInsert[] = [$userId,$memberId,gmdate('Y-m-d H:i:s')];
    
    			$firstName  =  $model->firstName ? $model->firstName:' ';
    			$middleName =  $model->middleName ? $model->middleName:' ';
    			$lastName   =  $model->lastName ? $model->lastName :'';
    			$memberName = $firstName . ' '.$middleName. ' '. $lastName;
    			$message    = $memberName . " requested for a profile update.";
    
    			$notificationType = 'profile-approval';
    			$requestData  = $pushNotificationSender->setPushNotificationRequest($sentMemberId,$message,$notificationType,$institutionId,$memberId,$institutionName,strtolower($devicetype),$userId);
    			$response     = $pushNotificationSender->sendNotification(strtolower($devicetype), $sentMemberId, $requestData);
    
    			if($response){
    				 
    				$notificationSent[] = [$userId,$memberId,gmdate('Y-m-d H:i:s')];
    			}
    		}
    	}
    	if (count($bachInsert)>0){
    			
    		Yii::$app->db->createCommand()->batchInsert('profileupdatenotification', $fields,$bachInsert )->execute();
    
    	}
    	 
    	if (count($notificationSent)>0){
    			
    		Yii::$app->db->createCommand()->batchInsert('profileupdatenotificationsent', $fields,$notificationSent )->execute();
    		 
    	}
    	 
    }
}
