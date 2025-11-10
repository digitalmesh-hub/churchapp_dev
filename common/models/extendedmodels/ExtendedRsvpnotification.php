<?php

namespace common\models\extendedmodels;
use Yii;
use common\models\basemodels\Rsvpnotification;
use common\models\extendedmodels\ExtendedRsvpdetails;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedDevicedetails;


/**
 * This is the model class for table "rsvpnotification".
 *
 * @property int $id
 * @property int $userid
 * @property int $rsvpid
 * @property string $createddatetime
 *
 * @property Rsvpdetails $rsvp
 * @property Usercredentials $user
 */
class ExtendedRsvpnotification extends Rsvpnotification
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rsvpnotification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'rsvpid', 'createddatetime'], 'required'],
            [['userid', 'rsvpid'], 'integer'],
            [['createddatetime'], 'safe'],
            [['rsvpid'], 'exist', 'skipOnError' => true, 'targetClass' => Rsvpdetails::className(), 'targetAttribute' => ['rsvpid' => 'id']],
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
            'rsvpid' => 'Rsvpid',
            'createddatetime' => 'Createddatetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRsvp()
    {
        return $this->hasOne(ExtendedRsvpdetails::className(), ['id' => 'rsvpid']);
    }
    /**
     * 
     */
    public function deleteFromRsvpNotification($rsvpId)
    {
    	$sql = "SET sql_safe_updates=0; DELETE FROM rsvpnotification WHERE rsvpid=:rsvpid";
    	 
    	$result = Yii::$app->db->createCommand($sql)
    	->bindValue(':rsvpid' , $rsvpId )
    	->execute();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(ExtendedUserCredentials::className(), ['id' => 'userid']);
    }
    public static function rsvpNotification($model,$institutionId,$itemId,$userId,$userType)
    {
    	$pushNotificationSender = Yii::$app->PushNotificationHandler;
    	
    	$rsvpId = $model->id;
    	$rsvpnotificationModel = new ExtendedRsvpnotification();
    	$rsvpNotificationSentModel = new ExtendedRsvpnotificationsent();
    	$deviceDetailsModel = new ExtendedDevicedetails();
    	
    	//deleting from notification tables
    	$rsvpnotificationModel->deleteFromRsvpNotification($rsvpId); 
    	$rsvpNotificationSentModel->deleteFromRsvpNotificationSent($rsvpId);
    	
    	$eventResponse = ExtendedEvent::getEventDetailsByEventdId($itemId);
    	$memberDetails = ExtendedMember::getMemberId($userId, $institutionId, $userType);
    	$memberId = $memberDetails['memberid'];
    	$memberResponse = ExtendedMember::getExistingMemberDetails($memberId);
    	$rsvpPrivilegeId = ExtendedPrivilege::MANAGE_EVENT_RSVP;
    	$deviceDetails = $deviceDetailsModel->getDeviceDetails($institutionId, $rsvpPrivilegeId);
    	$fields    = ['userid','rsvpid','createddatetime'];
    	
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
    			
    			$bachInsert[] = [$userId,$rsvpId,gmdate('Y-m-d H:i:s')];
    		
    			$firstName  =  $memberResponse['firstName'] ? $memberResponse['firstName']:' ';
    			$middleName =  $memberResponse['middleName'] ? $memberResponse['middleName']:' ';
    			$lastName   =  $memberResponse['lastName'] ? $memberResponse['lastName']:'';
    			$memberName = $firstName . ''.$middleName. ''. $lastName;
    			$message    = $memberName . " expressed an interest on attending the event " . $eventResponse['notehead'];
    		
    			$notificationType = 'rsvp';
    			$requestData  = $pushNotificationSender->setPushNotificationRequest($sentMemberId,$message,$notificationType,$institutionId,$itemId,$institutionName,strtolower($devicetype),$userId, $memberId);
    			//print_r($requestData);die;
    			$response     = $pushNotificationSender->sendNotification(strtolower($devicetype), $sentMemberId, $requestData);
    			if($response){
    				$notificationSent[] = [$userId,$rsvpId,gmdate('Y-m-d H:i:s')];
    			}
    		}
    		}
    		if (count($bachInsert)>0){
    			 
    			Yii::$app->db->createCommand()->batchInsert('rsvpnotification', $fields,$bachInsert )->execute();
    		
    		}
    		
    		if (count($notificationSent)>0){
    			 
    			Yii::$app->db->createCommand()->batchInsert('rsvpnotificationsent', $fields,$notificationSent )->execute();
    			 
    		}
    }
}
