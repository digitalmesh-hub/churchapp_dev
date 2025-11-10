<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Eventsentdetails;

/**
 * This is the model class for table "eventsentdetails".
 *
 * @property int $id
 * @property string $sentto
 * @property int $notificationid
 * @property int $userid
 * @property string $notificationsenton
 * @property string $notificationtype
 * @property int $institutionid
 * @property string $type
 *
 * @property Institution $institution
 * @property Usercredentials $user
 */
class ExtendedEventsentdetails extends Eventsentdetails
{
	/**
	 * To delete the notificaion records before one month
	 * @return number|boolean
	 */
	public static function deleteEventSentDetails(){
		
		try {
			$eventDetails = Yii::$app->db->createCommand("
								CALL deleteEventSentDetails()")
										->execute();
			return $eventDetails;
				
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * To delete the notificaion successfully sent records before one month
	 * @return number|boolean
	 */
	public static function deleteSuccessfullSentDetails(){
	
		try {
			$eventDetails = Yii::$app->db->createCommand("
								CALL deleteSuccessfullEventSent()")
									->execute();
			return $eventDetails;
	
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * To get the event sent details count
	 * @param unknown $memberUserId
	 * @param unknown $institutionId
	 * @return \yii\db\false|boolean
	 */
	public static function getUserEventSentDetailsCount($memberUserId, $institutionId){
		
		try {
			 
			$conversationCount = Yii::$app->db->createCommand("select count(id) as eventsentdetailscount from eventsentdetails where userid =:memberuserid and institutionid=:institutionid")
			->bindValue(':memberuserid', $memberUserId)
			->bindValue(':institutionid', $institutionId)
			->queryOne();
			return $conversationCount;
		} catch (Exception $e) {
			return false;
		}	
	}
	
	/**
	 * To delete event sent details using user id
	 * @param unknown $memberUserId
	 * @param unknown $institutionId
	 * @return boolean
	 */
	public static function deleteEventSentDetailsUsingUserid($memberUserId, $institutionId){
		
		try{
			$command = Yii::$app->db->createCommand("delete from eventsentdetails where userid=:userid and institutionid=:institutionid")
			->bindValue(':userid', $memberUserId)
			->bindValue(':institutionid', $institutionId);
			$command->execute();
		
		}
		catch(ErrorException $e){
			yii::error($e->getMessage());
			return false;
		}
	}
	/**
	 * To get the event seen details count
	 * @param unknown $memberUserId
	 * @param unknown $institutionId
	 * @return \yii\db\false|boolean
	 */
	public static function getUserEventSeenDetailsCount($memberUserId,$institutionId)
	{
		try {
			$eventSeenCount = Yii::$app->db->createCommand("select count(eventid) as eventseendetailscount from eventseendetails where userid =:memberuserid and institutionid=:institutionid")
								->bindValue(':memberuserid', $memberUserId)
								->bindValue(':institutionid', $institutionId)		
								->queryOne();
			return $eventSeenCount;
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
	public static function deleteEventSeenDetailsUsingUserid($memberUserId, $institutionId)
	{
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
