<?php 

namespace api\modules\v3\controllers;

use Yii;
use api\modules\v3\controllers\BaseController;
use api\modules\v3\models\responses\ApiResponse;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedEvent;
use common\models\extendedmodels\ExtendedRsvpdetails;
use common\models\extendedmodels\ExtendedUserMember;
use common\models\extendedmodels\ExtendedEventseendetails;
use common\models\extendedmodels\ExtendedBirthdayAnniversarySeendetails;
use common\models\extendedmodels\ExtendedCommittee;
use common\models\extendedmodels\ExtendedConversationtopic;
use common\models\extendedmodels\ExtendedBills;
use common\models\extendedmodels\ExtendedNotificationsentdetails;
use common\models\extendedmodels\ExtendedMember;
use common\components\EmailHandlerComponent;
use common\models\extendedmodels\ExtendedRsvpnotification;
use common\models\extendedmodels\ExtendedRsvpnotificationsent;
use common\models\basemodels\BaseModel;
use yii\web\UnauthorizedHttpException;
use yii\base\ActionEvent;
use common\models\extendedmodels\ExtendedUserCredentials;
use yii\web\NotFoundHttpException;

class NotificationsController extends BaseController
{

	public $statusCode;
	public $message = "";
	public $data;
	public $code;
	public $memberFilter = [];
	
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return ArrayHelper::merge(
				parent::behaviors(),
				[
					'verbs' => [
							'class' => \yii\filters\VerbFilter::className(),
							'actions' => [
								'get-events' => ['GET'],
								'get-rsvp-events' => ['GET'],
								'save-rsvp' => ['POST'],
								'get-event-details' => ['GET'],
								'get-news-details' => ['GET'],
								'set-viewed-announcements-and-events' => ['POST'],
								'get-notifications' => ['GET']
							]
					],
				]
				);
	}

	function beforeAction($action)
    {
        $this->on(self::EVENT_BEFORE_ACTION,function(ActionEvent $event)
        {
           $auth = Yii::$app->authManager;
           $user = Yii::$app->user->identity;
           $permissionName = "d4b64d8a-ec48-11e6-b48e-000c2990e707";
           if (in_array($event->action->id,['get-rsvp-events'])) {
           		$userMemberId = $user->getUserMember();
                if(!$auth->checkAccess ($userMemberId, $permissionName)){
					throw new UnauthorizedHttpException;
               }       
           }
        });
        return parent::beforeAction($action);
    }
    
	/**
	 * Index action
	 * @return $statusCode int
	 */
	public function actionIndex()
	{
		$this->statusCode = 404;
		throw new \yii\web\HttpException($this->statusCode);
	}
	/**
	 * To get the details of events
	 * @return $statusCode int
	 */
	public function actionGetEvents()
	{  
		//loggin user
		$user = Yii::$app->user->identity;
		$userId = $user->id;
        $institutionId = $user->institutionid;
        date_default_timezone_set($user->institution->timezone);
		$activatedOn = date('Y-m-d H:i:s');

		$userBatch = BaseModel :: getMemberBatches($userId,$institutionId);
		$userBatch = explode(',',$userBatch);
		
		if($userId) {
			$eventData = ExtendedEvent::getEventData($userId, $activatedOn); 
			//$testeventData = ExtendedEvent::getEventData($userId, $activatedOn); 
			$data = new \stdClass();
			$modifiedArray = [];
			foreach ($eventData as $data) {
				
				$flag = 1;
				if(!empty($data['batch'])) {
					$flag = 0;
					$eventBatches = explode(',',$data['batch']);
					foreach($eventBatches as $eventBatch) {
						if(in_array($eventBatch,$userBatch)) {
							$flag = 1;
						}
					}
				}
				if (!$flag){
					continue;
				}

				// if(empty(array_intersect($userBatch, $eventBatch))) {
				// 	continue;
				// }

				$groupDate = date('d-m-Y',strtotimeNew($data['activitydate']));
				if (!isset($modifiedArray[$groupDate])) {
					$modifiedArray[$groupDate] = array();
				}
				array_push($modifiedArray[$groupDate], $data);
			}
			$events = [];
			foreach ($modifiedArray as $date => $eventData) {
				$event =[];
				$event['date'] = $date;
				$eventlist =[];
				foreach ($eventData as $key => $value) {
					
					if(!empty($value['activitydate'])) {
						/*$formatDate = BaseModel::convertToUserTimezone($value['activitydate'], $user->institution->timezone, true);*/
						$eventDate = date('d F Y',strtotimeNew($value['activitydate']));
						$eventTime = date('g:i a',strtotimeNew($value['activitydate']));
					} else {
						$eventDate = "";
						$eventTime = "";
					}
					
					$result = [
							'eventId' => (!empty($value['id'])) ? (int)$value['id'] : 0,
							'eventTitle' => (!empty($value['notehead'])) ? $value['notehead'] : '',
							'eventTeaser' => '',
							'eventVenue' => (!empty($value['venue'])) ? $value['venue'] : '',
							'eventTime' => $eventTime,
							'eventDate' => $eventDate,
							'eventBody' => (!empty($value['notebody'])) ? $value['notebody'] : '',
							'eventLogo' => '',
							'externalUrl' => (!empty($value['noteurl'])) ? $value['noteurl'] : '',
							'institutionLogo' => (!empty($value['EventLogo'])) ? (string)yii::$app->params['imagePath'].$value['EventLogo'] : '',
							'institutionName' => (!empty($value['name'])) ? $value['name'] : '',
							'isRead' => (!empty($value['viewedstatus'])) ? (bool)$value['viewedstatus'] : false,
							'rsvpAvailable' => (!empty($value['rsvpavailable'])) ? (bool)$value['rsvpavailable'] : false,
							'rsvpType' => (isset($value['rsvpvalue']) && $value['rsvpvalue'] >= 0 ) ? (int)$value['rsvpvalue'] : -1,
							'rsvpAttendeesCount' =>[
									'memberCount' => (!empty($value['membercount'])) ? (int)$value['membercount'] : 0,
									'childrenCount' => (!empty($value['childrencount'])) ? (int)$value['childrencount'] : 0,
									'guestCount' => (!empty($value['guestcount'])) ? (int)$value['guestcount'] : 0,
							],
					];
					array_push($eventlist,$result);
				}
				$event['eventList'] = $eventlist;
				array_push($events, $event);
			}
			$data = [
				'events' => $events
				
			];
			$this->statusCode = 200;
			$this->message = '';
			$this->data = $data;
			return new ApiResponse($this->statusCode, $this->data,$this->message);
		} else {
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	/**
	 * To get the Rsvp events
	 * 
	 */
	public function actionGetRsvpEvents()
	{
		$userId = Yii::$app->user->identity->id;
        $institutionId = Yii::$app->user->identity->institutionid;
		$currentDate = date('Y-m-d H:i:s');
		if($userId) {
			$rsvpEventResponse = ExtendedEvent::getRsvpEvents($userId, $currentDate);
			$data = new \stdClass();
				$eventlist =[]; 
				foreach ($rsvpEventResponse as $key => $value) {
					$result = [		
							'eventId' => (!empty($value['id'])) ? (int)$value['id'] : 0,
							'eventTitle' => (!empty($value['notehead'])) ? $value['notehead'] : '',
							'eventVenue' => (!empty($value['venue'])) ? $value['venue'] : '',
							'eventTime' => (!empty($value['activitydate'])) ? date('h:i a',strtotimeNew($value['activitydate'])) : '',
							'eventDate' => (!empty($value['activitydate'])) ? date('d F Y',strtotimeNew($value['activitydate'])) : '',
							'institutionId' => (!empty($value['institutionid'])) ? (int)$value['institutionid'] : 0, 
							'institutionName' => (!empty($value['institution'])) ? $value['institution'] : '',
							'rsvpPendingAcknowledgement' => (!empty($value['count'])) ? (int)$value['count'] : 0,
							'rsvpAttendeesCount' => $value['membercount'] + $value['spousecount'] + $value['childrencount']
					];
					array_push($eventlist,$result);
				}
			$data = [
				'eventList' => $eventlist
			];
			$this->statusCode = 200;
			$this->message = '';
			$this->data = $data;
			return new ApiResponse($this->statusCode, $this->data,$this->message);
		} else {
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	/**
	 * To save RSVP details
	 * @param $memberId string
	 * @param $itemId string
	 * @param $type int
	 * @param $memberCount int
	 * @param $childrenCount int
	 * @param $guestCount int
	 */
	public function actionSaveRsvp()
	{
		$request = Yii::$app->request;
		$itemId = $request->post('itemId');
		$type = $request->post('type');
		$memberCount = $request->post('memberCount', 0);
		$childrenCount = $request->post('childrenCount', 0);
		$guestCount = $request->post('guestCount', 0);
		$user = yii::$app->user->identity;
		$memberData = ExtendedUserMember::getMemberIdByUserIdAndInstitutionId($user->id, $user->institutionid);
		$logo = $user->institution->institutionlogo;
		if($memberData) {
			$memberId = filter_var($memberData['memberid'], FILTER_SANITIZE_NUMBER_INT);
			$itemId = filter_var($itemId, FILTER_SANITIZE_NUMBER_INT);
			$createddatetime = date('Y-m-d H:i:s');
			$userType = $user->usertype;
			$userId = ExtendedUserMember::getUserIdForRsvp($memberId, $user->institutionid, $userType);
			$institutionDetails = ExtendedEvent::getEvent($itemId);
			$institutionId = $institutionDetails['institutionid'];
			$saveRsvpDetails = ExtendedRsvpdetails::saveRsvp($itemId,$type,$memberCount,
								$childrenCount,$guestCount,$userId,$userType,$institutionId,$createddatetime);
			if ($saveRsvpDetails) {
				$lastRsvpId = reset($saveRsvpDetails);
				if($lastRsvpId != 0 ) {
					$model = $this->findModel($lastRsvpId);
					//sending mail
					$response = $this->sendMail($itemId,$memberId,$childrenCount,$guestCount,$memberCount,$userId,$logo);
					//sending notification
					if ($memberCount+$childrenCount+$guestCount > 0) {
						ExtendedRsvpnotification::rsvpNotification($model,$institutionId,$itemId,$userId,$userType);
					}
					$this->statusCode = 200;
					$this->message = '';
					$this->data = new \stdClass();
					return new ApiResponse($this->statusCode,$this->data,$this->message);
				}
			}
		} else {
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	/**
	 * to get event details
	 * @param $eventId int
	 */
	public function actionGetEventDetails()
	{
		$request = Yii::$app->request;
		$eventId = $request->get('eventId');
		$user = yii::$app->user->identity;
		$userId = $user->id;
		if ($eventId && $userId) {
			$eventDetails = ExtendedEvent::getEventDetails($userId, $eventId);
			if($eventDetails) {
				foreach ($eventDetails as $key => $value) {
					$data = new \stdClass();
					$data->eventId = (!empty($value['id'])) ? (int)$value['id']:'';
					$data->eventTitle = (!empty($value['notehead'])) ? $value['notehead']:'';
					$data->eventTeaser ='';
					$data->eventVenue = (!empty($value['venue'])) ? $value['venue']:'';
					if (!empty($value['activitydate'])) {
						$eventDate = date(yii::$app->params['dateFormat']['viewDateFormat'],strtotimeNew($value['activitydate']));
						$eventTime = date(yii::$app->params['dateFormat']['time12Hr'],strtotimeNew($value['activitydate']));
					} else {
						$eventDate = "";
						$eventTime = "";
					}
					$data->eventTime = $eventTime;
					$data->eventDate = $eventDate;
					$data->eventBody = (!empty($value['notebody'])) ? $value['notebody']:'';
					$data->eventLogo = '';
					$data->externalUrl = (!empty($value['noteurl'])) ? $value['noteurl']:'';
					$data->institutionLogo = (!empty($value['EventLogo'])) ? (string)yii::$app->params['imagePath'].$value['EventLogo']:'';
					$data->institutionName = (!empty($value['name'])) ? $value['name']:'';
					$data->isRead = (!empty($value['viewedstatus'])) ? (bool)$value['viewedstatus']:false;
					$data->rsvpAvailable = (!empty($value['rsvpavailable'])) ? (bool)$value['rsvpavailable']:false;
					$data->rsvpType = (isset($value['rsvpvalue']) && $value['rsvpvalue'] >= 0 ) ? (int)$value['rsvpvalue'] : -1;
					$obj = new \stdClass();
					$obj->memberCount = (!empty($value['membercount'])) ? (int)$value['membercount']:0;
					$obj->childrenCount = (!empty($value['childrencount'])) ? (int)$value['childrencount']:0;
					$obj->guestCount = (!empty($value['guestcount'])) ? (int)$value['guestcount']:0;
					$data->rsvpAttendeesCount = $obj;	
				}
				$this->statusCode = 200;
				$this->message = '';
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data,$this->message);
			} else {
				$data = new \stdClass();
				$events = [];
				$data->events = $events;
				$this->statusCode = 500;
				$this->message = 'No events are available';
				$this->data = $data;
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			}	
		} else {
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	/**
	 * To get theh news details
	 * @param $newsId int
	 * @return statusCode
	 */
	public function actionGetNewsDetails()
	{
		$request = Yii::$app->request;
		$newsId = $request->get('newsId');
		if ($newsId) {
			$user = Yii::$app->user->identity;
			$userId = $user->id;
			if ($userId) {
				$newsDetails = ExtendedEvent::getEventDetails($userId, $newsId);
				if ($newsDetails) {
					foreach ($newsDetails as $key => $value) {
						$data = new \stdClass();
						$data->notificationId = (!empty($value['id'])) ? (int)$value['id']:0;
						
						if (!empty($value['activitydate'])) {
							$eventDate = date(yii::$app->params['dateFormat']['viewDateFormat'],strtotimeNew($value['activitydate']));
						} else {
							$eventDate = "";	
						}
						$data->notificationDate = $eventDate;
						$data->notificationHeader = (!empty($value['notehead'])) ? $value['notehead']:'';
						$data->notificationBody = (!empty($value['notebody'])) ? $value['notebody']:'';
						$data->institutionLogo = (!empty($value['EventLogo'])) ? (string)yii::$app->params['imagePath'].$value['EventLogo']:'';
						$data->institutionName = (!empty($value['name'])) ? $value['name']:'';
						$data->isRead = (!empty($value['viewedstatus'])) ? (bool)$value['viewedstatus']:false;
						$data->rsvpAvailable = (!empty($value['rsvpavailable'])) ? (bool)$value['rsvpavailable']:false;
						$data->rsvpType = (!empty($value['rsvpvalue'])) ? (int)$value['rsvpvalue']:0;
						$obj = new \stdClass();
						$obj->memberCount = (!empty($value['membercount'])) ?(int) $value['membercount']:0;
						$obj->childrenCount = (!empty($value['childrencount'])) ? (int)$value['childrencount']:0;
						$obj->guestCount = (!empty($value['guestcount'])) ? (int)$value['guestcount']:0;
						$data->rsvpAttendeesCount = $obj;
					}
					$this->statusCode = 200;
					$this->message = '';
					$this->data = $data;
					return new ApiResponse($this->statusCode, $this->data,$this->message);	
				} else {
					$data = new \stdClass();
					$events = [];
					$data->events = $events;
					$this->statusCode = 500;
					$this->message = 'No news are available';
					$this->data = $data;
					return new ApiResponse($this->statusCode,$this->data,$this->message);
				}	
			} else {
				$this->statusCode = 500;
				$this->message = 'An error occurred while processing the request';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			}
		} else {
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
		
	}
	/**
	 * To set viewed announcements
	 * and events
	 * @param $announcements array
	 * @param $events array
	 * @param $didBirthdaysRead boolean
	 * @param $didAnniversariesRead boolean
	 */
	public function actionSetViewedAnnouncementsAndEvents()
	{
	    $data = new \stdClass();
	    $data->announcementCount =  0;
	    $data->birthdayCount =  0;
	    $data->anniversaryCount =  0;
	    $data->eventsCount =  0;
	    $data->conversationCount = 0;
	    $data->billsCount = 0;
	    $data->adminOptionCount = 0;
	    
		$request = Yii::$app->request;
		$announcements = $request->getBodyParam('announcements');
		$events = $request->getBodyParam('events');
		$didBirthdaysRead = $request->getBodyParam('didBirthdaysRead');
		$didAnniversariesRead = $request->getBodyParam('didAnniversariesRead');

		$user = Yii::$app->user->identity;
		$userId = $user->id;
        $institutionId = $user->institutionid;
        $userType = $user->usertype;
        $memberDetails = ExtendedMember::getMemberId($userId,$institutionId,$userType);
        $memberId = $memberDetails['memberid'];
        $eventDate = date('Y-m-d H:i:s');
		if ($userId) {
			if (count($announcements)>0) {
				foreach ($announcements as $key => $value) {
					$eventInstitution = ExtendedEvent::getEventInstitution($value);
					if ($eventInstitution) {
						$eventId = $value;
						$userId = $userId;
						$institutionId = $eventInstitution['institutionid'];
						$viewedStatus = 1;
						$viewedDate = date('Y-m-d H:i:s');
						$deviceId = null;
						$type = 'A';
						$responseEventSeen = ExtendedEventseendetails::setViewedEvent($eventId,$userId,$institutionId,
								$viewedStatus,$viewedDate,$deviceId,$type);
					}
					$eventInstitution = 0;
				}
			} 
			if (count($events)>0) {
				foreach ($events as $key => $value) {
					$responseInstitution = ExtendedEvent::getEventInstitution($value);
					if ($responseInstitution) {
						$eventId = $value;
						$userId = $userId;
						$institutionId = $responseInstitution['institutionid'];
						$viewedStatus = 1;
						$viewedDate = date('Y-m-d H:i:s');
						$deviceId = null;
						$type = 'E';
						$responseEventSeen = ExtendedEventseendetails::setViewedEvent($eventId,$userId,$institutionId,
								$viewedStatus,$viewedDate,$deviceId,$type);
					}
					$responseInstitution = 0;
				}
			}  
			$type = 'B';
			$viewedDate = date('Y-m-d H:i:s');
			$birthdayCount = ExtendedBirthdayAnniversarySeendetails::getBirthdayAnniversaryCount($userId, $institutionId, $type, $viewedDate);
			if($birthdayCount['count'] == 0) {
				$eventId = 0;
				$userId = $userId;
				$institutionId = $institutionId;
				$viewedStatus = ( $didBirthdaysRead == 1 ) ? 1 : 0;
				$viewedDate = date('Y-m-d H:i:s');
				$deviceId = null;
				$type = 'B';
				$responseBirthdaySeen = ExtendedBirthdayAnniversarySeendetails::setBirthdayAnniversarySeen($userId, $institutionId, $viewedStatus, $viewedDate, $type);
			
			} 
			$type = 'W';
			$viewedDate = date('Y-m-d H:i:s');
			$anniversaryCount = ExtendedBirthdayAnniversarySeendetails::getBirthdayAnniversaryCount($userId, $institutionId, $type, $viewedDate);
			if($anniversaryCount['count'] == 0) {
				$eventId = 0;
				$userId = $userId;
				$institutionId = $institutionId;
				$viewedStatus = ($didAnniversariesRead == 1 ) ? 1 : 0;
				$viewedDate = date('Y-m-d H:i:s');
				$deviceId = null;
				$type = 'W';
				$responseAnniversarySeen = ExtendedBirthdayAnniversarySeendetails::setBirthdayAnniversarySeen($userId, $institutionId, $viewedStatus, $viewedDate, $type);
			}
			if(count($announcements) >= 0 || count($events) >= 0 && $responseEventSeen != 0 ) {
				$eventsCount = ExtendedEvent::getEventCount($userId, $eventDate);
				$conversationCount = ExtendedConversationtopic::getUnreadConversationCount($userId);
				$billCount = ExtendedBills::getBillSeenCount($institutionId, $memberId, $userType);
				$totalUnreadConversation = 0;
				$unreadBillCount = 0;
				$adminCount = 0;
				if ($conversationCount) {
					$totalUnreadConversation = $conversationCount['unreadtopiccount'];
					$data->conversationCount = (int)$totalUnreadConversation;
				}
				if ($billCount) {
					$unreadBillCount = $billCount['billseencount'];
					$data->billsCount = (int)$unreadBillCount;
				}
				$manageFoodOrders = 'fcb852d5-0005-11e7-b48e-000c2990e707';
				$propertyId = 1;
				$adminCountResponse = ExtendedNotificationsentdetails::getManagementCount($userId,$eventDate,$manageFoodOrders,$propertyId);
				if ($adminCountResponse) {
					$adminCount = $adminCountResponse['prayerRequestCount'] + 
									$adminCountResponse['rsvpCount']+ 
									$adminCountResponse['profileApprovalCount'] + 
									$adminCountResponse['feedbackCount'] +
									$adminCountResponse['albumCount'] +
									$adminCountResponse['orderCount'];
					$data->adminOptionCount = (int)$adminCount;
				}
				if ($eventsCount) {
					$data->announcementCount = (!empty($eventsCount['announcementcount'])) ? (int)$eventsCount['announcementcount'] : 0;
					$memberBirthday = (!empty($eventsCount['memberbirthday'])) ? (int)$eventsCount['memberbirthday'] : 0;
					$spouseBirthday = (!empty($eventsCount['spousebirthday'])) ? (int)$eventsCount['spousebirthday'] : 0;
					$birthdays = $memberBirthday + $spouseBirthday;
					$data->birthdayCount = $birthdays ? (int)$birthdays : 0;
					$data->anniversaryCount = (!empty($eventsCount['weddingannuversery'])) ? (int)$eventsCount['weddingannuversery'] : 0;
					$data->eventsCount = (!empty($eventsCount['eventcount'])) ? (int)$eventsCount['eventcount'] : 0;
				}
					$this->statusCode = 200;
					$this->message = '';
					$this->data = $data;
					return new ApiResponse($this->statusCode,$this->data,$this->message);	
			}
		} else {
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	/**
	 * To get notifications
	 * @param $userId int
	 */
	public function actionGetNotifications()
	{
		
		$request = Yii::$app->request;
		$institutionId = Yii::$app->user->identity->institutionid;
		$vieweddate = date('Y-m-d H:i:s');
		$userId = Yii::$app->user->identity->id;
		$data = [];
		if ($userId) {
			$birthdayCount = ExtendedBirthdayAnniversarySeendetails::getBirthdayAnniversaryCount($userId, $institutionId, 'B', $vieweddate);
			$birthdayCount = $birthdayCount['count'];
			$anniversaryCount = ExtendedBirthdayAnniversarySeendetails::getBirthdayAnniversaryCount($userId, $institutionId, 'W', $vieweddate);
			$anniversaryCount = $anniversaryCount['count'];
			$allNotifications = ExtendedUserMember::getAllNotifications($userId, $institutionId, $vieweddate);
			
			$userBatch = BaseModel :: getMemberBatches($userId,$institutionId);
			$userBatch = explode(',',$userBatch);



			if ($allNotifications) {
			    $data['birthdays'] = $this->getBirthdayDetails($allNotifications,'birthday');
			    $data['anniversary'] = $this->getBirthdayDetails($allNotifications, 'anniversary');
			} else {
				$data = [
					'anniversary' => [],
					'birthdays' => [],
				];
			}
			$responseAnnouncements = ExtendedEvent::getAllAnnouncements($userId, $vieweddate);
			if ($responseAnnouncements){
			    $data['announcements'] = $this->getAnnoncements($responseAnnouncements,$userBatch);
			} else {
			    $data['announcements'] =[];
			}
			$this->statusCode = 200;
			$this->message = '';
			$this->data = $data;
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	protected function getvaluesFromArray($data)
	{
		return (!empty($data)) ? $data :'';
	}
	
	protected function getBirthdayDetails($getAllNotifications, $type)
	{		
		$userGroup = 0;
		$memberPh = '';
		
		if ($type == 'birthday') {
		 	$birthday = array_filter($getAllNotifications,array($this, 'memberBirthday'));
		 	$userGroup = 0;
		} elseif ($type == 'spousebirthday') {
		 	$birthday = array_filter($getAllNotifications,array($this, 'spousebirthday'));
		 	$userGroup = 1;
		} elseif ($type == 'anniversary') {
		 	$birthday = array_filter($getAllNotifications, array($this, 'anniversary'));
		}
		$memberBirthdays = ArrayHelper::index($birthday,null, 'userid');
		$birthdaylist = [];
		foreach ($memberBirthdays as $key => $value) {
			$institutionIds = implode(',', array_column($value, 'id'));
			if(count($value) > 1) {
				foreach ($value as $row) {

					if($type!='anniversary') {

						if ($row['birthday']) {
							$memberFilterMobileNo = $row['member_mobile1'];
							if (!empty($memberFilterMobileNo)){
								if (!in_array($memberFilterMobileNo ,$this->memberFilter)){
									array_push($this->memberFilter,$memberFilterMobileNo);
								} else {
									continue;
								}
							}	
						}
						if ($row['spousebirthday']) {
							$memberFilterMobileNo = $row['spouse_mobile1'];
							if (!empty($memberFilterMobileNo)){
								if (!in_array($memberFilterMobileNo ,$this->memberFilter)){
									array_push($this->memberFilter,$memberFilterMobileNo);
								} else {
									continue;
								}
							} 
							
						}

					}
					
					if (!$userGroup){
						$memberName = $this->getvaluesFromArray($row['memberFullName']);
						$memberEmail = $this->getvaluesFromArray($row['member_email']);
						$memberPic = $this->getvaluesFromArray($row['member_pic'])? (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$row['member_pic']) : '';
						$memberPh =  !$row['membermobilePrivacyEnabled']?$this->getvaluesFromArray($row['member_mobile1']):'';
					} else {
						$memberName = $this->getvaluesFromArray($row['spouseFullName']);
						$memberEmail = $this->getvaluesFromArray($row['spouse_email']);
						$memberPic = $this->getvaluesFromArray($row['spouse_pic'])? (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$row['spouse_pic']) : '';
						$memberPh =  !$row['spousemobilePrivacyEnabled']?$this->getvaluesFromArray($row['spouse_mobile1']):'';
					}
					$result = [
							'notificationId' => '',
							'memberId' => (string)$this->getvaluesFromArray($row['memberid']),
							'memberEmail' => $memberEmail,
							'rawMemberPh' => $row['rawMemberNo'] ? $row['rawMemberNo'] : "",
							'rawSpousePh' => $row['rawSpouseNo'] ? $row['rawSpouseNo'] : "",
							'memberName'=> $memberName,
							'memberImage' => $memberPic,
							'memberPhone' => strlen($memberPh)>=10?$memberPh:'',
							'spouseName' => $this->getvaluesFromArray($row['spouseFullName']),
							'spouseImage' => $this->getvaluesFromArray($row['spouse_pic'])? (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$row['spouse_pic']) : '',
							'institution' => $institutionIds,
							'userGroup' => $userGroup,
					];
					if($type == 'anniversary') {
						unset($result['memberEmail']);
						unset($result['userGroup']);
					}
					array_push($birthdaylist,$result);
					break;
				}
			} elseif (count($value) == 1) {
				foreach ($value as $row) {

					if($type!='anniversary') {
					if ($row['birthday']) {
						$memberFilterMobileNo = $row['member_mobile1'];
						if (!empty($memberFilterMobileNo)){
							if (!in_array($memberFilterMobileNo ,$this->memberFilter)){
								array_push($this->memberFilter,$memberFilterMobileNo);
							}else{
								continue;
							}
						} 
						
					}
					if ($row['spousebirthday']) {
						$memberFilterMobileNo = $row['spouse_mobile1'];
						if (!empty($memberFilterMobileNo)){
							if (!in_array($memberFilterMobileNo ,$this->memberFilter)){
								array_push($this->memberFilter,$memberFilterMobileNo);
							}else{
								continue;
							}
						} 
						
					}
					}
				if (!$userGroup){
						$memberName = $this->getvaluesFromArray($row['memberFullName']);
						$memberEmail = $this->getvaluesFromArray($row['member_email']);
						$memberPic = $this->getvaluesFromArray($row['member_pic'])? (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$row['member_pic']) : '';
						$memberPh =  !$row['membermobilePrivacyEnabled']?$this->getvaluesFromArray($row['member_mobile1']):'';
					} else {
						$memberName = $this->getvaluesFromArray($row['spouseFullName']);
						$memberEmail = $this->getvaluesFromArray($row['spouse_email']);
						$memberPic = $this->getvaluesFromArray($row['spouse_pic'])? (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$row['spouse_pic']) : '';
						$memberPh =  !$row['spousemobilePrivacyEnabled'] ? $this->getvaluesFromArray($row['spouse_mobile1']):'';
					}
					$result = [
							'notificationId' => '',
							'memberId' => (string)$this->getvaluesFromArray($row['memberid']),
							'memberEmail' => $memberEmail,
							'memberName'=> $memberName,
							'memberImage' => $memberPic,
							'rawMemberPh' => $row['rawMemberNo'] ? $row['rawMemberNo'] : "",
							'rawSpousePh' => $row['rawSpouseNo'] ? $row['rawSpouseNo'] : "",
							'memberPhone' => strlen ($memberPh) >= 10 ? $memberPh:'',
							'spouseName' => $this->getvaluesFromArray($row['spouseFullName']),
							'spouseImage' => $this->getvaluesFromArray($row['spouse_pic'])? (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$row['spouse_pic']) : '',
							'institution' => $institutionIds,
							'userGroup' => $userGroup,
					];
					if($type == 'anniversary') {
						unset($result['memberEmail']);
						unset($result['userGroup']);
					}
					array_push($birthdaylist,$result);
				}
			}
		}
		if($type == 'birthday') {
			$spouseBirthday	= $this->getBirthdayDetails($getAllNotifications,'spousebirthday');
			if (!empty($spouseBirthday)) {
				foreach ($spouseBirthday as $data) {
					array_push($birthdaylist,$data);
				}
			}
		}
		if($type == 'anniversary') {
			$tempArr = [];
			foreach ($birthdaylist as $bKey => $bValue) {
				if(in_array($bValue['rawMemberPh'], $tempArr) || in_array($bValue['rawSpousePh'], $tempArr) ) {
					unset($birthdaylist[$bKey]);
					continue;
				}
				$tempArr[] =  $bValue['rawMemberPh'];
				if($bValue['rawSpousePh']) 
            		$tempArr[] =  $bValue['rawSpousePh'];
			}
		}
		return array_values($birthdaylist);
	}
	
	protected function getAnnoncements($responseAnnouncements,$userBatch)
	{

		$announcementlist = [];
		$announcements = [];
		$announcement = [];
		$result = [];
		foreach ($responseAnnouncements as $key => $value) {

			$flag = 1;
			if(!empty($value['batch'])) {
				$flag = 0;
				$eventBatches = explode(',',$value['batch']);
				foreach($eventBatches as $eventBatch) {
					if(in_array($eventBatch,$userBatch)) {
						$flag = 1;
					}
				}
			}
			if (!$flag){
				continue;
			}

			$result = [
					'notificationId' => $this->getvaluesFromArray($value['id']),
					'notificationDate' => $this->getvaluesFromArray(date(Yii::$app->params['dateFormat']['viewDateFormat'],strtotimeNew($value['activitydate']))),
					'notificationHeader' => $this->getvaluesFromArray($value['notehead']),
					'notificationBody' => $this->getvaluesFromArray($value['notebody']),
					'institutionLogo' => !empty($value['EventLogo']) ? (string)yii::$app->params['imagePath'].$value['EventLogo'] : '',
					'institutionName' => $this->getvaluesFromArray($value['name']),
					'isRead' => (bool)$this->getvaluesFromArray($value['viewedstatus']),
					'rsvpAvailable' => (bool)$this->getvaluesFromArray($value['rsvpavailable']),
					'rsvpType' => $this->getvaluesFromArray($value['rsvpvalue'])?(int)$this->getvaluesFromArray($value['rsvpvalue']):0,
					'rsvpAttendeesCount' =>[
							'memberCount' =>$this->getvaluesFromArray($value['membercount'])?(int)$this->getvaluesFromArray($value['membercount']):0,
							'childrenCount' => $this->getvaluesFromArray($value['childrencount'])?(int)$this->getvaluesFromArray($value['childrencount']):0,
							'guestCount' => $this->getvaluesFromArray($value['guestcount'])?(int)$this->getvaluesFromArray($value['guestcount']):0,
					],
			];
			if ($result) {
			    array_push($announcementlist, $result);
			}
		}
		
		return $announcementlist;
	}
	/**
	 * To send email 
	 * to admin with the
	 * details of rsvp
	 */
	protected function sendMail($itemId,$memberId,$childrenCount,$guestCount,$memberCount,$userId, $logo='')
	{
		// $institutionId = Yii::$app->user->identity->institutionid;
		$eventDetails = ExtendedEvent::getEvent($itemId);
		$institutionId = $eventDetails['institutionid'];
		$userId = yii::$app->user->identity->id;
		$memberData = ExtendedMember::getMemberNameByInstitutionId($userId, $institutionId);
		$logo = $memberData['institutionlogo'];
		
		if(!empty($memberData['middleName'])){
			$memberName = $memberData['firstName']." ".$memberData['middleName']." ".$memberData['lastName'];
		} else {
			$memberName = $memberData['firstName']." ".$memberData['lastName'];
		}
		$memberName = ucwords($memberName);
		$title = isset($memberData['title']) ? $memberData['title'] :'';
		// $memberEmail = $memberData['member_email'];
		$extendedUserCredentials = new ExtendedUserCredentials();
		$emailList = $extendedUserCredentials->getUserEmail($eventDetails['institutionid']);
		if($emailList) {
			$total = $childrenCount + $guestCount + $memberCount;
			$eventResponse = ExtendedEvent::getEvent($itemId);
			$notehead = $eventResponse['notehead'];
			$eventdate = date('d-m-Y',strtotimeNew($eventResponse['activitydate']));
			$mailobj = new EmailHandlerComponent();
			$from = Yii::$app->params['clientEmail'];
			$subject ='Event Registration From '.$title.' '.$memberName;
			$mailContent['template'] = 'rsvp-mail';
			$mailContent['name'] = $memberName;
			$mailContent['notehead'] = $notehead;
			$mailContent['transactiondate'] = $eventdate;
			$mailContent['member'] = $memberCount;
			$mailContent['children'] = $childrenCount;
			$mailContent['guest'] = $guestCount;
			$mailContent['total'] = $total;
			$attach = '';
			//Institution logo
	        if(!empty($logo)){
	            $logo = Yii::$app->params['imagePath'].$logo;
	        } else {
	            $logo = Yii::$app->params['imagePath'].'/institution/institution-icon-grey.png';
	        }
	        $mailContent['logo'] = $logo;
	        $to = array_column($emailList,'emailid');
	        $status =  $mailobj->sendEmail($from, $to, '', $subject, $mailContent, $attach);
		} else {
			$status = true;
		}
		return $status;
	}

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
        if ($data['anniversary']){
            return $data;
        }
    }
    protected function findModel($id)
    {
    	if (($model = ExtendedRsvpdetails::findOne($id)) !== null) {
    		return $model;
    	}
    	throw new NotFoundHttpException('The requested page does not exist.');
    }
}


