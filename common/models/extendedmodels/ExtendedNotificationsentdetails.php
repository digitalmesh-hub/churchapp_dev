<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Notificationsentdetails;

/**
 * This is the model class for table "notificationsentdetails".
 *
 * @property int $id
 * @property string $reminderdate
 * @property string $body
 * @property int $institutionid
 * @property string $subject
 * @property int $messagetype
 *
 * @property Institution $institution
 */
class ExtendedNotificationsentdetails extends Notificationsentdetails
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notificationsentdetails';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reminderdate', 'body', 'institutionid', 'subject', 'messagetype'], 'required'],
            [['reminderdate'], 'safe'],
            [['institutionid', 'messagetype'], 'integer'],
            [['body'], 'string', 'max' => 8000],
            [['subject'], 'string', 'max' => 250],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reminderdate' => 'Reminderdate',
            'body' => 'Body',
            'institutionid' => 'Institutionid',
            'subject' => 'Subject',
            'messagetype' => 'Messagetype',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionid']);
    }
    /**
     * To get the admin count
     * @param $userId int
     * @param $currentDate
     */
    public static function getManagementCount($userId,$currentDate,$manageFoodOrders,$propertyId)
    {
    	
    	try {
    		$prayerRequestCount = ExtendedPrayerrequest::getAllPrayerRequestCount($userId, $currentDate);
    		$rsvpCount = ExtendedRsvpdetails::getRsvpCount($userId, $currentDate);
    		$profileApprovalCount = ExtendedUserMember::getPendingMembersCount($userId);
    		$feedbackCount = ExtendedFeedback::getFeedbackCount($userId, $currentDate);
    		$albumCount = ExtendedAlbum::getPendingAlbumCount($userId);
    		$orderCount = ExtendedOrders::getOrdersCount($userId, $manageFoodOrders, $propertyId,$currentDate);
    		
    		return [
    				'prayerRequestCount' => $prayerRequestCount,
    				'rsvpCount' => $rsvpCount,
    				'profileApprovalCount' => $profileApprovalCount,
    				'feedbackCount' => $feedbackCount,
    				'albumCount' => $albumCount,
    				'orderCount' => $orderCount
    		];
    	} catch (Exception $e) {
    		return false;
    	}
    	
    }
    
    public static function addNotification($subject,$messageContent,$institutionId,$messageType,$date){
    	
    	try {
    		$data = Yii::$app->db->createCommand(
    				"CALL addnotificationsent(:messageContent,:subject,:institutionId,:messageType,:date) ")
    				->bindValue(':messageContent' , $messageContent)
    				->bindValue(':subject' , $subject)
    				->bindValue(':institutionId' , $institutionId)
    				->bindValue(':messageType', $messageType)
    				->bindValue(':date', $date)
    				->queryOne();
    		 
    		return $data;
    		 
    	} catch (Exception $e) {
    		return false;
    	}
    }
}
