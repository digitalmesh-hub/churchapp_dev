<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Rsvpnotificationsent;

/**
 * This is the model class for table "rsvpnotificationsent".
 *
 * @property int $id
 * @property int $userid
 * @property int $rsvpid
 * @property string $createddatetime
 *
 * @property Rsvpdetails $rsvp
 * @property Usercredentials $user
 */
class ExtendedRsvpnotificationsent extends Rsvpnotificationsent
{
	/**
	 * To set rsvp notification
	 * @param unknown $institutionId
	 * @param unknown $manageEventRsvp
	 * @param unknown $eventId
	 * @return boolean
	 */
	public static function setRsvpNotification($institutionId,$manageEventRsvp,$eventId)
	{
		try {
			$response = ExtendedDevicedetails::getDeviceDetails($institutionId, $manageEventRsvp);
			if($response)
			{
				foreach ($response as $key => $value)
				{
					$userId = $value['id'];
					$rsvpId = $eventId;
					$createdDate = date('Y-m-d H:i:s');
					$addNotification = ExtendedRsvpnotificationsent::saveNotification($userId, $eventId, $createdDate);
					if($addNotification)
					{
						return true;
					}else{
						return false;
					}
				}
			}
			 
		} catch (Exception $e) {
		}
	}
	/**
	 * To save rsvp notification
	 * @param unknown $userId
	 * @param unknown $eventId
	 * @param unknown $createdDate
	 * @return boolean
	 */
	public static function saveNotification($userId,$eventId,$createdDate)
	{
		try {
			$saveData = Yii::$app->db->createCommand('
    						INSERT INTO rsvpnotificationsent(rsvpid,userid,createddatetime)
    						VALUES(:rsvpid,:userid,:date)')
	    						->bindValue(':rsvpid',$eventId)
	    						->bindValue(':userid', $userId)
	    						->bindValue(':date', $createdDate)
	    						->execute();
	         	return true;
	         	 
		} catch (Exception $e) {
			return false;
		}
	}
	public function deleteFromRsvpNotificationSent($rsvpId){
		 
		$sql = "SET sql_safe_updates=0; DELETE FROM rsvpnotificationsent WHERE rsvpid=:rsvpid";
		 
		$result = Yii::$app->db->createCommand($sql)
		->bindValue(':rsvpid' , $rsvpId )
		->execute();
	}
}

