<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\extendedmodels\ExtendedNotificationsentdetails;
use common\models\extendedmodels\ExtendedEventsentdetails;
use common\models\extendedmodels\ExtendedEvent;
use common\models\extendedmodels\ExtendedDevicedetails;
use common\models\basemodels\BaseModel;
use common\components\PushNotificationHandler;
use common\components\PushNotificationRequestParamKeys;

class NotificationSchedulerControllerlkl extends Controller
{
	/**
	 * To sent birthday and anniversary to the member 
	 */
	public function actionMemberNotifications()
	{

		$institutions = ExtendedInstitution::getAllInstitutions();

		foreach ($institutions as $institution) {
			$institutionId   = $institution['id'];
			$timeZone 	     = trim($institution['timezone']);
			$institutionName = trim($institution['name']);
			date_default_timezone_set($timeZone);
			$date =  date("Y-m-d H:i:s");
			/* if (!(date("H") >= 7 && date("H") < 10)) {
				continue;
			} */
			// get all devices
			/* $birthdayDevices = BaseModel::getNotificationDevices($institutionId, $date, "B");

			// Only proceed if there are devices to send notifications to
			if (empty($birthdayDevices)) {
				continue;
			} */
				
			//get notifications
			$notificationDetails = BaseModel::getNotifications($institutionId, $date);

			// Process each notification record to avoid duplicates when both member and spouse have birthdays
			$birthdayList = [];
			
			if (!empty($notificationDetails)) {
				foreach ($notificationDetails as $notification) {
					// Check member birthday
					if (!empty($notification['birthday'])) {
						$birthdayList[] = [
							'type' => 'member',
							'title' => $notification['membertitle'],
							'firstname' => $notification['firstname'],
							'lastname' => $notification['lastname']
						];
					}
					
					// Check spouse birthday (separate entry, not duplicate)
					if (!empty($notification['spousebirthday'])) {
						$birthdayList[] = [
							'type' => 'spouse',
							'title' => $notification['spousetitle'],
							'firstname' => $notification['spouse_firstname'],
							'lastname' => $notification['spouse_lastname']
						];
					}
				}
			}
			
			// Get dependants with birthdays today (independent of member/spouse birthdays)
			$dependantBirthdays = $this->getDependantBirthdays($institutionId, $date);

			print_r($dependantBirthdays);
			exit;

			// Send notification if there are ANY birthdays (members, spouses, or dependants)
			if (count($birthdayList) > 0 || count($dependantBirthdays) > 0) {

				$birthdayMsg = "Birthday's today - ";

				foreach ($birthdayList as $birthday) {
					$birthdayMsg .= $birthday['title'] . ' ' . $birthday['firstname'] . ' ' . $birthday['lastname'] . ', ';
				}
				
				foreach ($dependantBirthdays as $dependant) {
					$memberFullName = trim(($dependant['membertitle'] ?? '') . ' ' . $dependant['firstname'] . ' ' . ($dependant['middlename'] ?? '') . ' ' . $dependant['lastname']);
					$birthdayMsg .= $dependant['titlename'] . ' ' . $dependant['dependantname'] . ' (' . $dependant['relation'] . ' of ' . $memberFullName . '), ';
				}

				$birthdayMsg = rtrim($birthdayMsg, ', ');
				print_r($birthdayMsg);
				echo "\n";
				exit;
				$subject = $institutionName . " members Birthday today " . date("d-m-Y ");

				$notificationInserIdDetails = ExtendedNotificationsentdetails::addNotification($subject, $birthdayMsg, $institutionId, 1, $date);

				$notificationInserId = $notificationInserIdDetails['id'];
				$this->sendPushNotification($birthdayDevices, $birthdayMsg, "birthday", $institutionId, $notificationInserId, $institutionName, $date, 'B', date("d-m-Y"));
			}
		}
	}
	/**
	 * To sent the event notification
	 */
	public function actionEventNotification()
	{
		$institutions = ExtendedInstitution::getAllInstitutions();
		foreach ($institutions as $institution) {

			$institutionId   = $institution['id'];
			$timeZone 	     = trim($institution['timezone']);
			$institutionName = trim($institution['name']);
			date_default_timezone_set($timeZone);
			$date =  date("Y-m-d");

			if (!(date("H") >= 7 && date("H") < 10)) {
				continue;
			}
			$this->processEvents($institutionId, $institutionName, $date);
		}
	}

	/**
	 * To sent the news notification
	 */
	public function actionNewsNotification()
	{
		$institutions = ExtendedInstitution::getAllInstitutions();
		foreach ($institutions as $institution) {

			$institutionId   = $institution['id'];
			$timeZone 	     = trim($institution['timezone']);
			$institutionName = trim($institution['name']);
			date_default_timezone_set($timeZone);
			$date =  date("Y-m-d");

			if (!(date("H") >= 7 && date("H") < 10)) {
				continue;
			}
			$this->processNews($institutionId, $institutionName, $date);
		}
	}

	public function actionAnniversaryNotification()
	{
		$institutions = ExtendedInstitution::getAllInstitutions();
		if (empty($institutions))
			return;

		foreach ($institutions as $key => $institution) {
			date_default_timezone_set(trim($institution['timezone']));
			$currentDate =  date("Y-m-d");
			if (!(date("H") >= 7 && date("H") < 10)) {
				continue;
			}
			$devicesToPush = BaseModel::getNotificationDevices($institution['id'], $currentDate, "w");
			if (empty($devicesToPush)) {
				continue;
			}
			
			// Get member/spouse anniversaries
			$notifications = BaseModel::getNotifications($institution['id'], $currentDate);
			$weddingAnniversaires = [];
			if (!empty($notifications)) {
				$weddingAnniversaires = array_filter($notifications, array($this, 'anniversary'));
			}
			
			// Get dependants with wedding anniversaries today (independent of member/spouse)
			$dependantAnniversaries = $this->getDependantAnniversaries($institution['id'], $currentDate);
			
			// Send notification if there are ANY anniversaries (members/spouses or dependants)
			if (count($weddingAnniversaires) > 0 || count($dependantAnniversaries) > 0) {
				$message = "Wedding Anniv. today -  ";
				foreach ($weddingAnniversaires as $anniversary) {
					$message .= $anniversary['membertitle'] . ' ' . $anniversary['firstname'] . ' ' . $anniversary['lastname'] . ' ' .
						$anniversary['spousetitle'] . ' ' . $anniversary['spouse_firstname'] . ' ' . $anniversary['spouse_lastname'] . ', ';
				}
				
				foreach ($dependantAnniversaries as $dependant) {
					$memberFullName = trim(($dependant['membertitle'] ?? '') . ' ' . $dependant['firstname'] . ' ' . ($dependant['middlename'] ?? '') . ' ' . $dependant['lastname']);
					// Show dependant and their spouse together, like member anniversaries
					$message .= $dependant['titlename'] . ' ' . $dependant['dependantname'] . ' ' . 
					            ($dependant['spousetitle'] ?? '') . ' ' . ($dependant['spousename'] ?? '') . 
					            ' (' . $dependant['relation'] . ' of ' . $memberFullName . '), ';
				}
				
				$message = rtrim($message, ', ');
				$subject = $institution['name'] . " members Wedding Anniversary today " . date("d-m-Y ");
				$anniversaryDetails = ExtendedNotificationsentdetails::addNotification(
					$subject,
					$message,
					$institution['id'],
					2,
					$currentDate
				);

				$this->sendPushNotification(
					$devicesToPush,
					$message,
					"anniversary",
					$institution['id'],
					$anniversaryDetails['id'],
					$institution['name'],
					$currentDate,
					'w',
					date("d-m-Y")
				);
			}
		}
	}

	/**
	 * Initate a push notification to clear the expired notifications in user devices
	 */
	public function actionRemoveExpiredNotification()
	{
		$devices = ExtendedDevicedetails::getUserDevicesForExpiredNotificationRemoval();

		if (count($devices)) {
			foreach ($devices as $device) {
				$deviceType = strtolower($device["devicetype"]);
				$notificationPayload = Yii::$app->PushNotificationHandler->getSilentPushNotificationBasicRequest("clean-expired", $deviceType);
				$response = Yii::$app->PushNotificationHandler->sendNotification($deviceType, $device["deviceid"], $notificationPayload);
			}
		}
	}



	protected function sendPushNotification($devicesDetails, $message, $notificationType, $institutionId, $notificationInserId, $institutionName, $date, $type, $notificationExpiryDate = null, $batch = '')
	{
		$registrationid = null;
		$pushNotificationSender = Yii::$app->PushNotificationHandler;

		$fields = ['sentto', 'notificationid', 'userid', 'notificationsenton', 'notificationtype', 'institutionid', 'type'];
		$successfullSend = [];
		$bulkInsert  = [];
		$count = 0;
		$i = 0;

		$batches = [];

		if ($batch) {

			$batches = explode(',', $batch);
		}
		$baseModel = new BaseModel();
		$batchArrays = $baseModel->getMemberBatchArray($institutionId);

		foreach ($batchArrays as $batch) {
			$batchArray[$batch['userid']] =  $batch['batch'];
		}

		foreach ($devicesDetails as $devices) {
			//$memberbatch = BaseModel  :: getMemberInstitutionBatch($devices['userid'],$institutionId);

			if (count($batches) > 0) {
				if (!empty($batchArray[$devices['userid']])) {
					if (!in_array($batchArray[$devices['userid']], $batches)) {
						continue;
					}
				}
			}

			$registrationid = null;

			if ($notificationType == 'birthday') {
				if (strtolower($devices["usertype"]) == "m" && $devices["birthday"] == 1) {
					$registrationid =  $devices["deviceid"];
				}
				if (strtolower($devices["usertype"]) == "s" && $devices["spousebirthday"] == 1) {
					$registrationid =  $devices["deviceid"];
				}
			} elseif ($notificationType == 'anniversary') {

				if (strtolower($devices["usertype"]) == "m" && $devices["anniversary"] == 1) {
					$registrationid =  $devices["deviceid"];
				}
				if (strtolower($devices["usertype"]) == "s" && $devices["spouseanniversary"] == 1) {
					$registrationid =  $devices["deviceid"];
				}
			} elseif ($notificationType == 'event') {

				if (strtolower($devices["usertype"]) == "m" && $devices["membernotification"] == 1) {
					$registrationid =  $devices["deviceid"];
				}
				if (strtolower($devices["usertype"]) == "s" && $devices["spousenotification"] == 1) {
					$registrationid =  $devices["deviceid"];
				}
			} elseif ($notificationType == 'announcements') {

				if (strtolower($devices["usertype"]) == "m" && $devices["membernotification"] == 1) {
					$registrationid =  $devices["deviceid"];
				}
				if (strtolower($devices["usertype"]) == "s" && $devices["spousenotification"] == 1) {
					$registrationid =  $devices["deviceid"];
				}
			}
			if ($registrationid) {

				//$requestData  = $pushNotificationSender->setPushNotificationRequest($registrationid,$message,$notificationType,$institutionId,$notificationInserId,$institutionName,strtolower($devices['devicetype']),$devices['userid']);

				$requestData  = $pushNotificationSender->getPushNotificationRequestUsingInfo([
					PushNotificationRequestParamKeys::MESSAGE => $message,
					PushNotificationRequestParamKeys::NOTIFICATION_TYPE => $notificationType,
					PushNotificationRequestParamKeys::INSTITUTION_ID => $institutionId,
					PushNotificationRequestParamKeys::INSTITUTION_NAME => $institutionName,
					PushNotificationRequestParamKeys::NOTIFICATION_ID => $notificationInserId,
					PushNotificationRequestParamKeys::DEVICE_TYPE => strtolower($devices['devicetype']),
					PushNotificationRequestParamKeys::USER_ID => $devices['userid'],
					PushNotificationRequestParamKeys::EXPIRY_ON => $notificationExpiryDate
				]);


				$response     = $pushNotificationSender->sendNotification(strtolower($devices['devicetype']), $registrationid, $requestData);

				if ($response) {
					$successfullSend[] = [$registrationid, $notificationInserId, $devices['userid'], $date, $type, $institutionId, 'device'];
					$i++;
				}
				$count++;
				$bulkInsert[] = [$registrationid, $notificationInserId, $devices['userid'], $date, $type, $institutionId, 'device'];


				if ($count == 1000) {

					Yii::$app->db->createCommand()->batchInsert('eventsentdetails', $fields, $bulkInsert)->execute();

					$bulkInsert = [];
					$count = 0;
				}

				if ($i == 1000) {

					Yii::$app->db->createCommand()->batchInsert('successfulleventsent', $fields, $successfullSend)->execute();

					$successfullSend = [];
					$i = 0;
				}
			}
		}
		if (!empty($bulkInsert)) {

			Yii::$app->db->createCommand()->batchInsert('eventsentdetails', $fields, $bulkInsert)->execute();
		}
		if (!empty($successfullSend)) {
			Yii::$app->db->createCommand()->batchInsert('successfulleventsent', $fields, $successfullSend)->execute();
		}
	}

	// to do
	protected function sendIosNotification($registrationid, $message, $notificationType, $institutionId, $notificationInserId, $institutionName, $date) {}
	/**
	 * To get spouse bithday details
	 * @param unknown $data
	 * @return unknown
	 */
	public function  spouseBirthday($data)
	{

		if ($data['spousebirthday']) {
			return $data;
		}
	}
	/**
	 * To get member bithday details
	 * @param unknown $data
	 * @return unknown
	 */
	public function  memberBirthday($data)
	{

		if ($data['birthday']) {
			return $data;
		}
	}

	/**
	 * To get the member anniversary
	 */
	public function  anniversary($data)
	{

		if ($data['anniversary']) {
			return $data;
		}
	}

	protected function processEvents($institutionId, $institutionName, $date)
	{

		$eventDetails = ExtendedEvent::getNotificationEventdetails($institutionId, $date);

		$notificationId = null;
		foreach ($eventDetails as $events) {

			$notificationId = $events['id'];
			//'batches'
			$familyUnitId = null;
			if (!empty($events['familyunitid'])) {
				$familyUnitId = $events['familyunitid'];
			}

			$deviceDetails = BaseModel::getAllEventDevices($institutionId, $notificationId, $familyUnitId);

			if (!empty($deviceDetails)) {
				$eventDate = date("d/m/Y H:i:s", strtotimeNew($events["activitydate"]));
				$message =  $events["notehead"] . " " . $events["venue"] . " on " . $eventDate;
				$expiryDate = date("d-m-Y", strtotimeNew($events["expirydate"]));

				//foreach ($eventDetails as $events ){
				//foreach ($eventDetails as $events ){
				$this->sendPushNotification($deviceDetails, $message, 'event', $institutionId, $notificationId, $institutionName, $date, 'E', $expiryDate, $events['batch']);
				//}

				//}

			}
		}
	}

	protected function processNews($institutionid, $institutionName, $date)
	{

		$eventDetails = ExtendedEvent::getNotificationNewsdetails($institutionid, $date);
		foreach ($eventDetails as $events) {

			$eventId = $events['id'];
			$familyUnitId = null;
			if (!empty($events['familyunitid'])) {
				$familyUnitId = $events['familyunitid'];
			}
			$noteHead = $events['notehead'];
			$activityDate = $events['activitydate'];
			$expiryDate = $events['expirydate'];

			$deviceDetails = BaseModel::getAnnouncementDevices($eventId, $institutionid, $familyUnitId);
			if (!empty($deviceDetails)) {
				if (!empty($activityDate)) {

					$activitydate = date('d/m/Y', strtotimeNew($activityDate));
					$message = $noteHead . " on " . $activitydate;
				} else {
					$message = $noteHead;
				}

				$this->sendPushNotification($deviceDetails, $message, 'announcements', $institutionid, $eventId, $institutionName, $date, 'A', $expiryDate, $events['batch']);
			}
		}
	}

	/**
	 * Get dependants with birthdays today
	 * @param int $institutionId
	 * @param string $date
	 * @return array
	 */
	protected function getDependantBirthdays($institutionId, $date)
	{
		try {
			$todayMonthDay = date('m-d', strtotime($date));
			
			$query = "SELECT 
						d.id, 
						d.dependantname, 
						CASE 
							WHEN d.relation IS NOT NULL AND d.relation != '' THEN d.relation
							WHEN spouse.relation = 'Son' THEN 'Daughter-in-law'
							WHEN spouse.relation = 'Daughter' THEN 'Son-in-law'
							WHEN spouse.relation = 'Father' THEN 'Mother'
							WHEN spouse.relation = 'Mother' THEN 'Father'
							WHEN spouse.relation = 'Brother' THEN 'Sister-in-law'
							WHEN spouse.relation = 'Sister' THEN 'Brother-in-law'
							WHEN spouse.relation = 'Grandfather' THEN 'Grandmother'
							WHEN spouse.relation = 'Grandmother' THEN 'Grandfather'
							WHEN spouse.relation = 'Grandson' THEN 'Granddaughter-in-law'
							WHEN spouse.relation = 'Granddaughter' THEN 'Grandson-in-law'
							ELSE 'Spouse'
						END as relation,
						d.dob, 
						t.Description as titlename, 
						m.memberid,
						mt.description as membertitle,
						m.firstname,
						m.middlename,
						m.lastname
					  FROM dependant d
					  INNER JOIN member m ON d.memberid = m.memberid
					  LEFT JOIN title t ON d.titleid = t.TitleId
					  LEFT JOIN title mt ON mt.TitleId = m.membertitle
					  LEFT JOIN dependant spouse ON spouse.dependantid = d.id
					  WHERE m.institutionid = :institutionId 
					  /* AND m.active = 1
					  AND m.confirmed = 1
					  AND d.active = 1
					  AND d.confirmed = 1 */
					  AND d.dob IS NOT NULL
					  AND d.dependantname IS NOT NULL
					  AND d.dependantname != ''
					  AND DATE_FORMAT(d.dob, '%m-%d') = :todayMonthDay
					  ORDER BY m.firstname, m.lastname, d.dependantname";
			
			$dependants = Yii::$app->db->createCommand($query)
				->bindValue(':institutionId', $institutionId)
				->bindValue(':todayMonthDay', $todayMonthDay)
				->queryAll();
			
			return $dependants;
		} catch (\Exception $e) {
			Yii::error("getDependantBirthdays: " . $e->getMessage());
			return [];
		}
	}

	/**
	 * Get dependants with wedding anniversaries today (only for married dependants)
	 * @param int $institutionId
	 * @param string $date
	 * @return array
	 */
	protected function getDependantAnniversaries($institutionId, $date)
	{
		try {
			$todayMonthDay = date('m-d', strtotime($date));
			
			$query = "SELECT 
						d.id, 
						d.dependantname, 
						CASE 
							WHEN d.relation IS NOT NULL AND d.relation != '' THEN d.relation
							WHEN spouse.relation = 'Son' THEN 'Daughter-in-law'
							WHEN spouse.relation = 'Daughter' THEN 'Son-in-law'
							WHEN spouse.relation = 'Father' THEN 'Mother'
							WHEN spouse.relation = 'Mother' THEN 'Father'
							WHEN spouse.relation = 'Brother' THEN 'Sister-in-law'
							WHEN spouse.relation = 'Sister' THEN 'Brother-in-law'
							WHEN spouse.relation = 'Grandfather' THEN 'Grandmother'
							WHEN spouse.relation = 'Grandmother' THEN 'Grandfather'
							WHEN spouse.relation = 'Grandson' THEN 'Granddaughter-in-law'
							WHEN spouse.relation = 'Granddaughter' THEN 'Grandson-in-law'
							ELSE 'Spouse'
						END as relation,
						d.weddinganniversary, 
						t.Description as titlename,
						spouse.dependantname as spousename,
						st.Description as spousetitle,
						m.memberid,
						mt.description as membertitle,
						m.firstname,
						m.middlename,
						m.lastname
					  FROM dependant d
					  INNER JOIN member m ON d.memberid = m.memberid
					  LEFT JOIN title t ON d.titleid = t.TitleId
					  LEFT JOIN title mt ON mt.TitleId = m.membertitle
					  LEFT JOIN dependant spouse ON spouse.dependantid = d.id
					  LEFT JOIN title st ON st.TitleId = spouse.titleid
					  WHERE m.institutionid = :institutionId 
					  /* AND m.active = 1
					  AND m.confirmed = 1
					  AND d.active = 1
					  AND d.confirmed = 1 */
					  AND d.ismarried = 2
					  AND d.weddinganniversary IS NOT NULL
					  AND d.dependantname IS NOT NULL
					  AND d.dependantname != ''
					  AND DATE_FORMAT(d.weddinganniversary, '%m-%d') = :todayMonthDay
					  ORDER BY m.firstname, m.lastname, d.dependantname";
			
			$dependants = Yii::$app->db->createCommand($query)
				->bindValue(':institutionId', $institutionId)
				->bindValue(':todayMonthDay', $todayMonthDay)
				->queryAll();
			
			return $dependants;
		} catch (\Exception $e) {
			Yii::error("getDependantAnniversaries: " . $e->getMessage());
			return [];
		}
	}
}
