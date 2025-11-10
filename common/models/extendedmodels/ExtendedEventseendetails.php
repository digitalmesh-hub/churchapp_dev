<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Eventseendetails;

/**
 * This is the model class for table "eventseendetails".
 *
 * @property int $eventid
 * @property int $userid
 * @property int $institutionid
 * @property int $viewedstatus
 * @property int $deviceid
 * @property string $vieweddate
 * @property string $type
 *
 * @property Devicedetails $device
 * @property Events $event
 * @property Institution $institution
 * @property Usercredentials $user
 */
class ExtendedEventseendetails extends Eventseendetails
{
	/**
	 * To set event seen details
	 * @param unknown $eventId
	 * @param unknown $userId
	 * @param unknown $institutionId
	 * @param unknown $viewedStatus
	 * @param unknown $viewedDate
	 * @param unknown $deviceId
	 * @param unknown $type
	 * @return number|boolean
	 */
	public static function setViewedEvent($eventId,$userId,$institutionId,
								$viewedStatus,$viewedDate,$deviceId,$type)
	{
		try {
			$setEventSeenDetails = Yii::$app->db->createCommand('
					insert into eventseendetails(eventid,userid,institutionid,viewedstatus,vieweddate,deviceid,type)
					values(:eventid,:userid,:institutionid,:viewedstatus,:vieweddate,:deviceid,:type)')
					->bindValue(':eventid',$eventId)
					->bindValue(':userid',$userId)
					->bindValue(':institutionid',$institutionId)
					->bindValue(':viewedstatus',$viewedStatus)
					->bindValue(':vieweddate',$viewedDate)
					->bindValue(':deviceid',$deviceId)
					->bindValue(':type',$type)
					->execute();
			return $setEventSeenDetails;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * To get the event seen details count
	 * @param unknown $memberUserId
	 * @param unknown $institutionId
	 * @return \yii\db\false|boolean
	 */
	public static function getUserEventSeenDetailsCount($memberUserId, $institutionId){
	
		try {
	
			$conversationCount = Yii::$app->db->createCommand("select count(eventid) as eventseendetailscount from eventseendetails where userid =:memberuserid and institutionid=:institutionid")
								->bindValue(':memberuserid', $memberUserId)
								->bindValue(':institutionid', $institutionId)
								->queryOne();
			return $conversationCount;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * To delete event seen details using user id
	 * @param unknown $memberUserId
	 * @param unknown $institutionId
	 * @return boolean
	 */
	public static function deleteEventSeenDetailsUsingUserid($memberUserId, $institutionId){
	
		try{
			$command = Yii::$app->db->createCommand("delete from eventseendetails where userid=:userid and institutionid=:institutionid")
					->bindValue(':userid', $memberUserId)
					->bindValue(':institutionid', $institutionId);
					$command->execute();
		}
		catch(ErrorException $e){
			yii::error($e->getMessage());
			return false;
		}
	}
}
