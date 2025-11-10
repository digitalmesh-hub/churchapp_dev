<?php
namespace common\components;

use yii;
use yii\helpers\Json;
use yii\base\Component;
use yii\base\ErrorException;
use common\models\basemodels\BaseModel;
use common\models\extendedmodels\ExtendedPrivilege;
use common\models\basemodels\RememberAppConstModel;


class NotificationHandler extends Component
{
	/**
	 * To send push  notification (Android)
	 * @param unknown $registrationid
	 * @param unknown $message
	 * @param unknown $notificationType
	 * @param unknown $profileUpdateNotification
	 */
	public function sendGCMProfileUpdateNotification( $registrationid, $message,  $notificationType, $profileUpdateNotification,$memberId){
		
		 $data = $this->notificationContent($registrationid, $message,  $notificationType, $profileUpdateNotification,$memberId);	 
		 $deviceType = $profileUpdateNotification['devicetype'];
		 $deviceid = $profileUpdateNotification['deviceid'];
		 return Yii::$app->PushNotificationHandler->sendNotification($deviceType, $deviceid, $data);
	}
	
	/**
	 * To send push  notification (Ios)
	 * @param unknown $registrationid
	 * @param unknown $message
	 * @param unknown $notificationType
	 * @param unknown $profileUpdateNotification
	 */
	public function sendAPNSProfileUpdateNotification( $registrationid, $message,  $notificationType, $profileUpdateNotification,$memberId){
	
		$data = $this->notificationContent($registrationid, $message,  $notificationType, $profileUpdateNotification,$memberId);
		
		$badge = $data['notify-count'] + $data['bday-count'] +$data['news-count'] + $data['msg-count'] + $data['admin-count'];
		
	     $apps = ["alert"=> [
				"body"=> $profileUpdateNotification['institutionname'] . " - " . $message
				],
				"badge"=> $badge,
				"sound"=> "default"
				];
		$data['aps'] = $apps;
		
		$deviceType = $profileUpdateNotification['devicetype'];
		$deviceid = $profileUpdateNotification['deviceid'];
		return Yii::$app->PushNotificationHandler->sendNotification($deviceType, $deviceid, $data);
	}
	/**
	 * To create the notification data
	 */
	public function notificationContent($registrationid, $message,  $notificationType, $profileUpdateNotification,$memberId){
		
		$notificationTotalCount = 0; 
		$birthdayCount = 0; 
		$anniversaryCount = 0;
		$prayerRequestCount = 0; 
		$rsvpCount = 0; 
		$profileApprovalCount = 0; 
		$feedbackCount = 0; 
		$pendingAlbumCount = 0; 
		$foodOrdrCount = 0; 
		$adminCount = 0;
		$conversationTopicCount = 0;
			
		// conversationTopicNotification = InstitutionManager.GetUnreadConversationCountByUserId(profileUpdateNotification.UserId);
		
		$conversationTopicNotification =  BaseModel::getUnreadConversationCount($profileUpdateNotification['id']);
		
		if ($conversationTopicNotification->Status == true && $conversationTopicNotification->value != 0){
				
			$conversationTopicCount = $conversationTopicNotification->value;
		}
		$responseNotificationcount = BaseModel::getNotificationsLoginCount($userId($profileUpdateNotification['id']));
		$notificationTotalCount = ($responseNotificationcount['AnnouncementsCount'] + $responseNotificationcount['EventsCount']);
		$birthdayCount = $responseNotificationcount['MemberBirthdayCount'] + $responseNotificationcount['SpouseBirthdayCount'];
		$anniversaryCount = $responseNotificationcount['WeddingAnnuverseryCountCount'];
		$eventCount = $responseNotificationcount['EventsCount'];
		$prayerRequestCount = BaseModel::getAllPrayerRequestCount($profileUpdateNotification['id']);
		$rsvpCount = BaseModel::getRsvpEventsCount($profileUpdateNotification['id']);
		$profileApprovalCount = BaseModel::getMembersListForApprovalCount($profileUpdateNotification['id']);
		$feedbackCount = BaseModel::getallFeedbackCount($profileUpdateNotification['id']);
		$pendingAlbumCount = BaseModel::getAllPendingAlbumsCount($profileUpdateNotification['id']);
		$foodOrdrCount = BaseModel::getOrdersCount($profileUpdateNotification['id'], ExtendedPrivilege::MANAGE_FOOD_ORDERS, RememberAppConstModel::RESTAURANT);
		
		$adminCount = $prayerRequestCount + $rsvpCount + $profileApprovalCount + $feedbackCount + $pendingAlbumCount + $foodOrdrCount;
		//  CREATE REQUEST
		// 		WebRequest Request = WebRequest.Create(gcmServer);
		// 		Request.Method = "POST";
		// 		Request.ContentType = "application/json";
		// 		Request.Headers.Add(string.Format("Authorization: key={0}", serverKey));
		// 		Request.Headers.Add(string.Format("Sender: id={0}", senderID));
		
		$postData = ["ID" =>$memberId,'contentTitle'=> $notificationType,"item-id"=>$memberId,"type"=>$notificationType,
				"institution"=>$profileUpdateNotification['institutionname'],"message"=>$message,"msg-count"=>$conversationTopicCount,
				"notify-count"=>$notificationTotalCount,"news-count"=>$responseNotificationcount['AnnouncementsCount'],"events-count"=>$eventCount,
				"bday-count" =>$birthdayCount,"anniv-count"=>$anniversaryCount,"food-order-count"=>$foodOrdrCount,"admin-count"=>$adminCount,"institution-id"=>$profileUpdateNotification['institutionid']
		];
		
	}

	/**
	 * To save notifications
	 * @param $deviceId string
	 * @param $userId int
	 * @param $eventId int
	 * @param $memberId int
	 * @param $notificationType string
	 * @param $institutionId int
	 * @param $createdDateTime datetime
	 * @param $createdBy int
	*/
	public function saveNotificationDetails($deviceId, $userId, $eventId, $memberId, $notificationType, $institutionId, $createdBy){
		try {
			$deletedContacts = Yii::$app->db->createCommand("
				INSERT INTO notificationlog (deviceid,userid,eventid,memberid,notificationtype,institutionid,CreatedDateTime,CreatedBy) VALUES (:deviceId,:userId,:eventId,:memberId,:notificationType,:institutionId,:createdDateTime,:createdBy)")
				->bindValue(':deviceId', $deviceId)
				->bindValue(':userId', $userId)
				->bindValue(':eventId', $eventId)
				->bindValue(':memberId', $memberId)
				->bindValue(':notificationType', $notificationType)
				->bindValue(':institutionId', $institutionId)
				->bindValue(':createdDateTime', gmdate("Y-m-d H:i:s"))
				->bindValue(':createdBy', $createdBy)
				->execute();
			return true;
		} catch (ErrorException $e) {
			yii::error($e->getMessage());
			return false;
		}
	}

	/**
	 * Add event sent details
	 * @param $deviceId string
	 * @param $userId int
	 * @param $eventId int
	 * @param $notificationType string
	 * @param $institutionId int
	 * @param eventType string
	*/
	public function addEventSentDetails($deviceId, $userId, $eventId, $notificationType, $institutionId, $eventType){
		try {
			$query = Yii::$app->db->createCommand("CALL addeventsent(:deviceId,:eventId,:userId,:eventType,:notificationType,:institutionId,:sentDate)")
				->bindValue(':deviceId', $deviceId)
				->bindValue(':userId', $userId)
				->bindValue(':eventId', $eventId)
				->bindValue(':eventType', $eventType)
				->bindValue(':notificationType', $notificationType)
				->bindValue(':institutionId', $institutionId)
				->bindValue(':sentDate', gmdate("Y-m-d"))
				->execute();
			return true;
		} catch (ErrorException $e) {
			yii::error($e->getMessage());
			return false;
		}
	}

	/**
	 * Add succesfull event notification details
	 * @param $deviceId string
	 * @param $userId int
	 * @param $eventId int
	 * @param $memberId int
	 * @param $notificationType string
	 * @param $institutionId int
	 * @param $createdDateTime datetime
	 * @param $createdBy int
	*/
	public function saveSuccessEventDetails($deviceId, $userId, $eventId, $notificationType, $institutionId, $type){
		try {
			$deletedContacts = Yii::$app->db->createCommand("INSERT INTO successfulleventsent(sentto,notificationid,userid,notificationsenton,notificationtype,institutionid,type) VALUES(:deviceId,:eventId,:userId,
				:sentdate,:notificationType,:institutionId,:type);")
				->bindValue(':deviceId', $deviceId)
				->bindValue(':userId', $userId)
				->bindValue(':eventId', $eventId)
				->bindValue(':notificationType', $notificationType)
				->bindValue(':institutionId', $institutionId)
				->bindValue(':type', $type)
				->bindValue(':sentdate', gmdate("Y-m-d H:i:s"))
				->execute();
			return true;
		} 
		catch (ErrorException $e) {
			yii::error($e->getMessage());
			return false;
		}
	}
}
