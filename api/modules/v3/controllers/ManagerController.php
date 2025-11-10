<?php 

namespace api\modules\v3\controllers;

use Yii;
use api\modules\v3\controllers\BaseController;
use common\models\extendedmodels\ExtendedUserLocation;
use api\modules\v3\models\responses\ApiResponse;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedPrayerrequest;
use common\models\extendedmodels\ExtendedFeedback;
use common\models\extendedmodels\ExtendedUserMember;
use common\models\extendedmodels\ExtendedEvent;
use common\models\extendedmodels\ExtendedRsvpdetails;
use common\models\extendedmodels\ExtendedFeedbackimagedetails;
use common\models\extendedmodels\ExtendedInstitution;
use common\components\EmailHandlerComponent;
use common\models\basemodels\Events;
use common\models\extendedmodels\ExtendedPrayerrequestnotificationsent;
use common\models\extendedmodels\ExtendedFeedbacknotificationsent;
use common\models\extendedmodels\ExtendedRsvpnotificationsent;
use yii\web\UnauthorizedHttpException;
use yii\base\ActionEvent;


class ManagerController extends BaseController 
{
	
	public $statusCode;
	public $message = "";
	public $data;
	public $code;
	
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
						'get-prayer-request-details' => ['GET'],
						'get-prayer-requests' => ['GET'],
						'get-feedbacks' => ['GET'],
						'get-rsvp-details-of-member' => ['GET'],
						'get-feedback-details' => ['GET'],
						'acknowledge-prayer-request' => ['POST'],
						'acknowledge-feedback' => ['POST'],
						'acknowledge-rsvp' => ['POST'],
						'get-rsvp-details' => ['GET']
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
           if (in_array($event->action->id,['get-prayer-requests', 'get-feedbacks',
           	'get-feedback-details','acknowledge-rsvp','acknowledge-prayer-request','acknowledge-feedback'])) {
               switch ($event->action->id) {
               	case 'get-prayer-requests':
               		$permissionName = "ca4ac940-ec4a-11e6-b48e-000c2990e707";
               		break;
               	case 'get-feedbacks':
               		$permissionName = "0f74458a-ec49-11e6-b48e-000c2990e707";
               		break;
               	case 'get-feedback-details':
               		$permissionName = "0f74458a-ec49-11e6-b48e-000c2990e707";
               		break;
               	case 'acknowledge-rsvp':
               		$permissionName = "d4b64d8a-ec48-11e6-b48e-000c2990e707";
               		break;
               	case 'acknowledge-prayer-request':
               		$permissionName = "ca4ac940-ec4a-11e6-b48e-000c2990e707";
               		break;
               	case 'acknowledge-feedback':
               		$permissionName = "0f74458a-ec49-11e6-b48e-000c2990e707";
               		break;
            
               }
           	$userMemberId = $user->getUserMember();
               if(!$auth->checkAccess ($userMemberId, $permissionName)){
					throw new UnauthorizedHttpException;
               }       
           }
        });
        return parent::beforeAction($action);
    }
	
	/**
	 * Index action.
	 * @return $statusCode int
	 */
	public function actionIndex()
	{
		$this->statusCode = 404;
		throw new \yii\web\HttpException($this->statusCode);
	}
	/**
	 * To get the details of the 
	 * prayer request
	 * @param $requestId int
	 * @return $statusCode 
	 */
	public function actionGetPrayerRequestDetails()
	{
		$request = Yii::$app->request;
		$requestId = $request->get('requestId');
		$userId = Yii::$app->user->identity->id;

		if ($userId) {
			if ($requestId) {
				$requestId = filter_var($requestId, FILTER_SANITIZE_NUMBER_INT);
				$requestData = ExtendedPrayerrequest::getRequestData($requestId, true);
				$data = new \stdClass();
				if ($requestData) {
					if (is_array($requestData)){
						if (!$requestData['isresponded']) {
							$createdDate = (!empty($requestData['createdtime'])) ? $requestData['createdtime'] : '';
							if ($createdDate) {
								$date = date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($createdDate));
								$time = date(Yii::$app->params['dateFormat']['time12Hr'], strtotimeNew($createdDate));
							} else {
								$date ='';
								$time ='';
							}
							$data->requestId = (!empty($requestData['prayerrequestid'])) ? (int)$requestData['prayerrequestid'] : 0;
							$data->requestTitle = (!empty($requestData['subject'])) ? $requestData['subject'] : '';
							$data->requestDetails = (!empty($requestData['description'])) ? $requestData['description'] : '';
							$data->requestedDate = $date;
							$data->requestedTime = $time;
							$data->memberId = (!empty($requestData['memberid'])) ? $requestData['memberid'] : '';
							$data->memberTitle = (!empty($requestData['title'])) ? $requestData['title'] : '';
							$data->memberName = (!empty($requestData['username'])) ? $requestData['username'] : '';
							$data->memberImage = (!empty($requestData['image'])) ? yii::$app->params['imagePath'].$requestData['image'] : '';
							$data->memberPhone = (!empty($requestData['phone'])) ? $requestData['phone'] : '';
							$data->memberEmail = (!empty($requestData['email'])) ? $requestData['email'] : '';
							$data->isResponded = (!empty($requestData['isresponded'])) ? (bool)$requestData['isresponded'] : false;
							$data->userGroup = (!empty($requestData['usergroup'])) ? (int)$requestData['usergroup'] : 0;

							$this->statusCode = 200;
							$this->message = '';
							$this->data = $data;
							return new ApiResponse($this->statusCode, $this->data,$this->message);
						} else {
							$this->statusCode = 603;
							$this->message = 'This prayerrequest has been already acknowledged by the admin.';
							$this->data = new \stdClass();
							return new ApiResponse($this->statusCode,$this->data,$this->message);
						}
					} else {
						$data->requestId = 0;
						$data->requestTitle = '';
						$data->requestDetails = '';
						$data->requestedDate = '';
						$data->requestedTime = '';
						$data->memberId = '';
						$data->memberTitle = '';
						$data->memberName = '';
						$data->memberImage = '';
						$data->memberPhone = '';
						$data->memberEmail = '';
						$data->isResponded = false;
						$data->userGroup = '';

						$this->statusCode = 200;
						$this->message = '';
						$this->data = $data;
						return new ApiResponse($this->statusCode, $this->data,$this->message);
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
		} else {
			$this->statusCode = 498;
			$this->message = 'Session invalid';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}

	/**
	 * To get prayer requests
	 * @param $userId int
	 * @param $currentDate DateTime
	 */
	public function actionGetPrayerRequests()
	{
		$userId = Yii::$app->user->identity->id;
		date_default_timezone_set('Asia/Kolkata'); //Convert it to user timezone,from devicee details table
  		$currentDate = date('Y-m-d H:i:s');
		if ($userId) {
			$prayerResponse = ExtendedPrayerrequest::getAllPrayerRequest($currentDate, $userId);
			if ($prayerResponse) {
				$prayerList = [];
				if(is_array($prayerResponse)) {
					foreach ($prayerResponse as $key => $value) {
						
						$date = date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($value['createdtime']));
						$time = date(Yii::$app->params['dateFormat']['time12Hr'], strtotimeNew($value['createdtime']));
						$result = [
								'requestId' => (!empty($value['prayerrequestid'])) ? (int)$value['prayerrequestid'] : 0,
								'requestTitle' => (!empty($value['subject'])) ? $value['subject'] : '',
								'memberId' => (!empty($value['memberid'])) ? $value['memberid'] : '',
								'memberTitle' => (!empty($value['title'])) ? $value['title'] : '',
								'memberName' => (!empty($value['username'])) ? $value['username'] : '',
								'memberImage' => (!empty($value['image'])) ? yii::$app->params['imagePath'].$value['image'] : '',
								'requestedDate' => $date ? $date : '',
								'requestedTime' => $time ? $time : '',
								'isResponded' => (!empty($value['isresponded'])) ? (bool)$value['isresponded'] : false,
								'institutionId' => (!empty($value['institutionid'])) ? (int)$value['institutionid'] : 0,
								'institutionName' => (!empty($value['institution'])) ? $value['institution'] : '',
						];
						array_push($prayerList, $result);
					}
				}
				$data['requests'] = $prayerList;
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
		} else {
			$this->statusCode = 498;
			$this->message = 'Session invalid';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}

	/**
	 * To get feedbacks
	 */
	public function actionGetFeedbacks()
	{
		
		$userId = Yii::$app->user->identity->id;
		date_default_timezone_set('Asia/Kolkata'); //Convert it to user timezone,from device details table
  		$currentDate = date('Y-m-d H:i:s');
  		
		if ($userId) {
			$prayerResponse = ExtendedFeedback::getAllFeedback($userId, $currentDate);
			if ($prayerResponse) {
				$feedbackList = [];
				if (is_array($prayerResponse)) {
					foreach ($prayerResponse as $key => $value) {
						$date = (!empty($value['createddatetime'])) ? date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($value['createddatetime'])) : '';

						$time = (!empty($value['createddatetime'])) ? date(Yii::$app->params['dateFormat']['time12Hr'], strtotimeNew($value['createddatetime'])) : '';
						$result = [
								'feedbackId' => (!empty($value['feedbackid'])) ? (int)$value['feedbackid'] : 0,
								'feedbackType' => (!empty($value['feedbacktype'])) ? $value['feedbacktype'] : '',
								'feedbackDate' => $date,
								'feedbackTime' => $time,
								'rating' => (!empty($value['feedbackrating'])) ? (int)$value['feedbackrating'] : 0,
								'memberId' => (!empty($value['memberid'])) ? $value['memberid'] : '',
								'memberTitle' => (!empty($value['title'])) ? $value['title'] : '',
								'memberName' => (!empty($value['username'])) ? $value['username'] : '',
								'memberImage' => (!empty($value['image'])) ? yii::$app->params['imagePath'].$value['image'] : '',
								'isResponded' => (!empty($value['isresponded'])) ? (bool)$value['isresponded'] : false,
								'institutionId' => (!empty($value['institutionid'])) ? (int)$value['institutionid'] : 0,
								'institutionName' => (!empty($value['institution'])) ? $value['institution'] : '',
						];
						array_push($feedbackList,$result);
					}
				}
				$data['feedbacks'] = $feedbackList;
				$this->statusCode = 200;
				$this->message = '';
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data,$this->message);
			}else{
				$this->statusCode = 500;
				$this->message = 'An error occurred while processing the request';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			}
			
		}else{
			$this->statusCode = 498;
			$this->message = 'Session invalid';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	/**
	 * To get rsvp details
	 * of a member
	 */
	public function actionGetRsvpDetailsOfMember()
	{
		$request = Yii::$app->request;
		$eventId = $request->get('eventId');
		$memberId = $request->get('memberId');
		if ($eventId && $memberId) {
			
			$eventId = filter_var($eventId, FILTER_SANITIZE_NUMBER_INT);
			$memberId = filter_var($memberId, FILTER_SANITIZE_NUMBER_INT);
			$userType = ExtendedUserMember::getUserType($memberId);
			$userId = ExtendedUserMember::getUserId($memberId, $userType['usertype']);
			$userId = $userId['userid'];
			$institutionId = ExtendedUserMember::getInstitutionId($memberId);
			$institutionId = $institutionId['institutionid'];
			$totalMemberCount = 0;
			$totalChildrenCount = 0;
			$totalGuestCount = 0;
			$totalAttendees = 0;
			if ($userId) {
				$eventResponse = ExtendedEvent::getRsvpEventDetails($eventId);
				$memberEvent = [];
				if(!empty($eventResponse)) {
					$data = new \stdClass();
					if(is_array($eventResponse)){
						$date = date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($eventResponse['activitydate']));
						$time = date(Yii::$app->params['dateFormat']['time12Hr'], strtotimeNew($eventResponse['activitydate']));
							
						$data->eventId = (!empty($eventResponse['eventid'])) ? (int)$eventResponse['eventid'] : 0;
						$data->eventTitle = (!empty($eventResponse['notehead'])) ? $eventResponse['notehead']: '';
						$data->eventVenue = (!empty($eventResponse['venue'])) ? $eventResponse['venue']:'';
						$data->eventTime = $time ? $time:'';
						$data->eventDate = $date ? $date:'';
						$data->institutionId = (!empty($eventResponse['institutionid'])) ? (int)$eventResponse['institutionid']: 0;
						$data->institutionName = (!empty($eventResponse['institution'])) ? $eventResponse['institution']:'';
						$rsvpValue = true;
						$response = ExtendedRsvpdetails::getRsvpService($eventId, $rsvpValue);
						if ($response) {
							foreach ($response as $key => $value) {
								$totalMemberCount += $value['membercount'];
								$totalChildrenCount += $value['childrencount'];
								$totalGuestCount += $value['guestcount'];
								$totalAttendees += $value['membercount'] + $value['childrencount'] + $value['guestcount'];
								if($value['memberid'] == $memberId) {
									array_push($memberEvent, $value);
								}
							}

							foreach ($memberEvent as $key => $row) {
								$data->totalMemberCount = $totalMemberCount;
								$data->totalGuestCount = $totalGuestCount;
								$data->totalChildrenCount = $totalChildrenCount;
								$data->totalAttendeeCount = $totalAttendees;
								$data->memberId = (!empty($row['memberid'])) ? (int)$row['memberid'] : 0;
								$data->memberTitle = (!empty($row['membertitle'])) ? $row['membertitle'] :'';
								$data->memberName = (!empty($row['name'])) ? $row['name'] :'';
								$data->memberImage = (!empty($row['memberpic'])) ? yii::$app->params['imagePath'].$row['memberpic'] :'';
								$data->memberPhone = (!empty($row['phone'])) ? $row['phone'] :'';
								$data->memberEmail = (!empty($row['memberemail'])) ? $row['memberemail'] :'';
								$data->memberCount = (!empty($row['membercount'])) ? (int)$row['membercount'] : 0;
								$data->guestCount = (!empty($row['guestcount'])) ? (int)$row['guestcount'] : 0;
								$data->childrenCount = (!empty($row['childrencount'])) ? (int)$row['childrencount'] : 0;
								$data->isResponded = (!empty($row['acksentdatetime'])) ? true : false;
								$data->rsvpSubmittedDate = (!empty($row['createddatetime'])) ? date(Yii::$app->params['dateFormat']['dateOfBrithFormat'],strtotimeNew($row['createddatetime'])) :'';
								$data->userGroup = (!empty($row['usergroup'])) ? (int)$row['usergroup'] : 0;
							}
							$this->statusCode = 200;
							$this->message = '';
							$this->data = $data;
							return new ApiResponse($this->statusCode, $this->data,$this->message); 	
						} else {
							$data->totalMemberCount = 0;
							$data->totalGuestCount = 0;
							$data->totalChildrenCount = 0;
							$data->totalAttendeeCount = 0;
							$data->memberId = 0;
							$data->memberTitle = '';
							$data->memberName = '';
							$data->memberImage = '';
							$data->memberPhone = '';
							$data->memberEmail = '';
							$data->memberCount = 0;
							$data->guestCount = 0;
							$data->childrencount = 0;
							$data->isResponded =false;
							$data->rsvpSubmittedDate = '';
							$data->userGroup = 0;
							$this->statusCode = 200;
							$this->message = '';
							$this->data = $data;
							return new ApiResponse($this->statusCode, $this->data,$this->message); 
						} 
					} else {
						$data = new \stdClass();
						$data->eventId =  0;
						$data->eventTitle = '';
						$data->eventVenue = '';
						$data->eventTime = '';
						$data->eventDate = '';
						$data->institutionId =  0;
						$data->institutionName = '';
						$data->totalMemberCount = 0;
						$data->totalGuestCount = 0;
						$data->totalChildrenCount = 0;
						$data->totalAttendeeCount = 0;
						$data->memberId = 0;
						$data->memberTitle = '';
						$data->memberName = '';
						$data->memberImage = '';
						$data->memberPhone = '';
						$data->memberEmail = '';
						$data->memberCount = 0;
						$data->guestCount = 0;
						$data->childrencount = 0;
						$data->isResponded =false;
						$data->rsvpSubmittedDate = '';
						$data->userGroup = 0;
						
						$this->statusCode = 200;
						$this->message = '';
						$this->data = $data;
						return new ApiResponse($this->statusCode, $this->data,$this->message); 
					}
				} else {
					$this->statusCode = 500;
					$this->message = 'An error occurred while processing the request111';
					$this->data = new \stdClass();
					return new ApiResponse($this->statusCode,$this->data,$this->message);
				}
			} else {
				$this->statusCode = 498;
				$this->message = 'Session invalid';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			}
		}
	}
	/**
	 * To get the feedback
	 * details
	 * @param $feedbackId int 
	 */
	public function actionGetFeedbackDetails()
	{
		$request = Yii::$app->request;
		$feedbackId = $request->get('feedbackId');
		// $userId = 1550; 
		$userId = Yii::$app->user->identity->id;

		if($userId)
		{
			$userId = filter_var($userId, FILTER_SANITIZE_NUMBER_INT);
			if($userId != 0)
			{
				$response = ExtendedFeedback::getRequestData($feedbackId, true);
				$imageResponse = ExtendedFeedbackimagedetails::getFeedbackImages($feedbackId);

				if($response)
				{
					$images = [];
					$data = new \stdClass();
					$data->imageUrls = $images;
					if(is_array($response)){
						foreach ($imageResponse as $row)
						{
							$imageUrl = (!empty($row['feedbackimage'])) ? $row['feedbackimage'] : '';
							if ($imageUrl != ''){
							    $imageUrl = yii::$app->params['imagePath'].$imageUrl; 
							}
							array_push($images, $imageUrl);
						}
						if(!$response[0]['isresponded']){
							foreach ($response as $key => $value)
							{
								$date = date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($value['createddatetime']));
								$time = date(Yii::$app->params['dateFormat']['time12Hr'], strtotimeNew($value['createddatetime']));
								$data->feedbackId = (!empty($value['feedbackid'])) ? (int)$value['feedbackid']: 0;
								$data->feedbackType = (!empty($value['feedbacktype'])) ? $value['feedbacktype']:'';
								$data->feedbackText = (!empty($value['description'])) ? $value['description']:'';
								$data->feedbackDate = $date ? $date :'';
								$data->feedbackTime = $time ? $time :'';
								$data->imageUrls = $images;
								$data->rating = (!empty($value['feedbackrating'])) ? (int)$value['feedbackrating']: 0;
								$data->memberId = (!empty($value['memberid'])) ? $value['memberid']:'';
								$data->memberTitle = (!empty($value['title'])) ? $value['title']:'';
								$data->memberName = (!empty($value['username'])) ? $value['username']:'';
								$data->memberImage = (!empty($value['image'])) ? yii::$app->params['imagePath'].$value['image']:'';
								$data->memberPhone = (!empty($value['phone'])) ? $value['phone']:'';
								$data->memberEmail = (!empty($value['email'])) ? $value['email']:'';
								$data->isResponded = (!empty($value['isresponded'])) ? (bool)$value['isresponded']: false;
								$data->userGroup = (!empty($value['usergroup'])) ? (int)$value['usergroup']: 0;
							}
						}
						else{
							$data = new \stdClass();
							$this->statusCode = 603;
							$this->message = 'This feedback has been already acknowledged by the admin.';
							$this->data = new \stdClass();
							return new ApiResponse($this->statusCode, $this->data,$this->message);
						}
					}
					else{
						$data->feedbackId = 0;
						$data->feedbackType = '';
						$data->feedbackText = '';
						$data->feedbackDate = '';
						$data->feedbackTime = '';
						$data->imageUrls = $images;
						$data->rating = 0;
						$data->memberId = '';
						$data->memberTitle = '';
						$data->memberName = '';
						$data->memberImage = '';
						$data->memberPhone = '';
						$data->memberEmail = '';
						$data->isResponded = false;
						$data->userGroup = 0;
					}
					$this->statusCode = 200;
					$this->message = '';
					$this->data = $data;
					return new ApiResponse($this->statusCode, $this->data,$this->message);
				}else{
					$this->statusCode = 500;
					$this->message = 'An error occurred while processing the request';
					$this->data = new \stdClass();
					return new ApiResponse($this->statusCode,$this->data,$this->message);
				}
			}else{
				$this->statusCode = 498;
				$this->message = 'Session invalid';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			}
		}
	}
	/**
	 * To acknowledge prayer request
	 * @param $requestId string
	 * @param $replyMessage string
	 * @param $replyToEmail string
	 */
	public function actionAcknowledgePrayerRequest()
	{
		$request = Yii::$app->request;
		$requestId = $request->getBodyParam('requestId');
		$replyMessage = $request->getBodyParam('replyMessage');
		$replyToEmail = $request->getBodyParam('replyToEmail');
		$userId = Yii::$app->user->identity->id;

		if ($userId && $replyToEmail && $replyMessage) {
			$requestId = filter_var($requestId, FILTER_SANITIZE_NUMBER_INT);
			$prayerResponse = ExtendedPrayerrequest::getRequestData($requestId);
			if($prayerResponse) {
				if(is_array($prayerResponse)) {
					$adminEmail = Yii::$app->params['tempEmail'];
					$sendAcknowledgement = $this->sendMail($userId,$adminEmail,$replyMessage,$replyToEmail,$prayerResponse);
					$sendAcknowledgement = true;
					if($sendAcknowledgement) {
						$updateIsRespondedBit = ExtendedPrayerrequest::setIsRespondedBit($requestId);
						$prayerrequestPrivilegeId = 'ca4ac940-ec4a-11e6-b48e-000c2990e707';
						$saveNotificationSent = ExtendedPrayerrequestnotificationsent::setPrayerRequestNotificationSent($prayerResponse['institutionid'], $prayerrequestPrivilegeId,$requestId);
						$this->statusCode = 200;
						$this->message = 'Your reply has been sent successfully';
						$this->data = new \stdClass();
						return new ApiResponse($this->statusCode, $this->data,$this->message);
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
			} else {
				$this->statusCode = 500;
				$this->message = 'An error occurred while processing the request';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			}
		} else {
			$this->statusCode = 200;
			$this->message = 'Your reply has been sent successfully';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	
	/**
	 * To acknowledge feebback
	 * @param $feedbackId string
	 * @param $replyMessage string
	 * @param $replyToEmail string
	 */
	public function actionAcknowledgeFeedback()
	{
		$request = Yii::$app->request;
		$feedbackId = $request->getBodyParam('feedbackId');
		$replyMessage = $request->getBodyParam('replyMessage');
		$replyToEmail = $request->getBodyParam('replyToEmail');
		$userId = Yii::$app->user->identity->id;
		if ($userId ) {
			$feedbackId = filter_var($feedbackId, FILTER_SANITIZE_NUMBER_INT);
			$feedbackResponse = ExtendedFeedback::getRequestData($feedbackId, true);
			if (!empty($feedbackResponse)) {
				foreach ($feedbackResponse as $key => $row) {
					if($row['isresponded'] == 0) {
						$institutionId = $row['institutionid'];
						$institutionName = $row['institution'];
						$institutionLogo = $row['institutionlogo'];
						$adminEmail = Yii::$app->params['tempEmail'];
						$sendAcknowledgement = $this->sendFeedbackReply($userId,$adminEmail,$feedbackId,$replyMessage,$replyToEmail,$institutionId,$institutionName, $institutionLogo);
						$updateRespondedBit = ExtendedFeedback::updateRespondedBit($feedbackId);
						$manageFeedbacksPrivilegeId = '0f74458a-ec49-11e6-b48e-000c2990e707';
						$saveFeedbackNotification = ExtendedFeedbacknotificationsent::setFeedbackNotification($institutionId, $manageFeedbacksPrivilegeId, $feedbackId);
						if($saveFeedbackNotification == true) {
							$this->statusCode = 200;
							$this->message = 'Your reply has been sent successfully';
							$this->data = new \stdClass();
							return new ApiResponse($this->statusCode, $this->data,$this->message);
						} else {
							$this->statusCode = 500;
							$this->message = 'An error occurred while processing the request';
							$this->data = new \stdClass();
							return new ApiResponse($this->statusCode,$this->data,$this->message);
						}
					} else {
						$this->statusCode = 603;
						$this->message = 'This feedback has been already acknowledged by the admin.';
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
		} else {
			$this->statusCode = 498;
			$this->message = 'Session invalid';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	/**
	 * To send rsvp
	 * acknowledgement to the user
	 */
	public function actionAcknowledgeRsvp()
	{
		$request = Yii::$app->request;
		$memberId = $request->getBodyParam('memberId');
		$eventId = $request->getBodyParam('eventId');
		$replyMessage = $request->getBodyParam('replyMessage');
		$replyToEmail = $request->getBodyParam('replyToEmail');
		$userId = Yii::$app->user->identity->id;
		$logo = Yii::$app->user->identity->institution->institutionlogo;
		if(!$replyToEmail) {
			$this->statusCode = 500;
			$this->message = 'The member have not provided an email address to send the RSVP acknowledgement';
			$this->data = new \stdClass();
		} else {
		if ($userId) {
			$institutionId = ExtendedEvent::getEventInstitution($eventId);
			$institutionId = $institutionId['institutionid'];
			$institutionResponse = ExtendedInstitution::getInstitutionDetails($institutionId);
			$isAcknowledgedResponse = ExtendedRsvpdetails::getAcknowledgedDate($memberId, $eventId);
			$institutionName = $institutionResponse['name'];
			if(!empty($isAcknowledgedResponse)) {
				if($isAcknowledgedResponse['acksentdatetime'] == '' || $isAcknowledgedResponse['acksentdatetime'] == null ) {
					$eventHeading = Events::find()->select('notehead')->where('id = :id', [':id' => $eventId])->scalar();
					$sendRsvpMail = $this->sendRsvpAcknowledgement($replyMessage,$institutionName,
										$userId,$institutionId,$eventId,$replyToEmail,$memberId,$logo, $eventHeading);
					//Notification
					$manageEventRsvp = 'd4b64d8a-ec48-11e6-b48e-000c2990e707';
					$saveRsvpNotification = ExtendedRsvpnotificationsent::setRsvpNotification($institutionId, $manageEventRsvp, $isAcknowledgedResponse['id']);
					if ($sendRsvpMail) {
						$eventModel = new ExtendedRsvpdetails();
	            		$eventdetails = $eventModel::find()->where(['id' => $isAcknowledgedResponse['id']])->one();
	            		$eventdetails->acksentdatetime = date('Y-m-d H:i:s');  
	            		if($eventdetails->update(false)) {
	                 		$this->statusCode = 200;
							$this->message = 'Your reply has been sent successfully';
							$this->data = new \stdClass();        
	            		} else {
	            			$this->statusCode = 500;
							$this->message = 'An error occurred while processing the request';
							$this->data = new \stdClass();
	            		} 
					} else {
						$this->statusCode = 500;
						$this->message = 'An error occurred while processing the request';
						$this->data = new \stdClass();
					} 	
				} else { 	
					$this->statusCode = 200;
					$this->message = 'Already acknowledged';
					$this->data = new \stdClass();
				}
			} else {
				$this->statusCode = 500;
				$this->message = 'An error occurred while processing the request';
				$this->data = new \stdClass();
			}
		} else {
			$this->statusCode = 498;
			$this->message = 'Session invalid';
			$this->data = new \stdClass();
		}
		}
		return new ApiResponse($this->statusCode, $this->data,$this->message);
	}
	/**
	 * To get RSVP details
	 * @param $eventId string
	 * @param $filterBy integer
	 */
	public function actionGetRsvpDetails()
	{
		$request = Yii::$app->request;
		$eventId = $request->get('eventId');
		$filterBy = $request->get('filterBy');
		if($eventId) {
			$eventId = filter_var($eventId, FILTER_SANITIZE_NUMBER_INT);
			$filterBy = filter_var($filterBy, FILTER_SANITIZE_NUMBER_INT);
			$userId = Yii::$app->user->identity->id;
			$institutionId = Yii::$app->user->identity->institutionid;
			$totalMemberCount = 0;
			$totalGuestCount = 0;
			$totalChildrenCount = 0;
			$totalAttendeesCount = 0;
			$eventResponse = ExtendedEvent::getRsvpEventDetails($eventId);
			if($userId != 0) {
				if($filterBy == 1) {
					if($eventResponse) {
						$rsvpValue = true;
						$response = ExtendedRsvpdetails::getRsvpServiceDetails($eventId, $rsvpValue);
						if($response) {
							$data = $this->setEventResponse($response,$eventResponse,$totalMemberCount,$totalGuestCount,$totalChildrenCount,$totalAttendeesCount);
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
					} else {
						$this->statusCode = 500;
						$this->message = 'An error occurred while processing the request';
						$this->data = new \stdClass();
						return new ApiResponse($this->statusCode,$this->data,$this->message);
					}
				} else {
					if ($eventResponse) {
						$rsvpValue = true;
						$responseRsvp = ExtendedRsvpdetails::getRsvpService($eventId, $rsvpValue);
						if ($responseRsvp) {
							$data = $this->setEventResponse($responseRsvp, $eventResponse, $totalMemberCount, $totalGuestCount, $totalChildrenCount, $totalAttendeesCount);
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
					} else {
						$this->statusCode = 500;
						$this->message = 'An error occurred while processing the request';
						$this->data = new \stdClass();
						return new ApiResponse($this->statusCode,$this->data,$this->message);
					}
				}
			} else {
				$this->statusCode = 498;
				$this->message = 'Session invalid';
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
	 * To send prayer request
	 * acknowledgement mail
	 * to user
	 */
	protected function sendMail($userId, $adminEmail, $replyMessage, $replyToEmail, $prayerResponse)
	{	

		$institutionId = $prayerResponse['institutionid'];
		$institutionName = $prayerResponse['institution'];
		if($prayerResponse['institutionlogo']){
            $institutionLogo = Yii::$app->params['imagePath'].$prayerResponse['institutionlogo'];
        } else {
            $institutionLogo = Yii::$app->params['imagePath'].'/institution/institution-icon-grey.png';
        }

		$title ='';
		$subject ='Prayer Request Acknowledgement';
		$attach = '';
		$mailContent['template'] = 'prayer-request';
		$mailContent['content'] = $replyMessage;
		$mailContent['institutionname'] = $institutionName;
		$mailContent['logo'] = $institutionLogo;
		$mailobj = new EmailHandlerComponent();
		$temp =  $mailobj->sendEmail($adminEmail,$replyToEmail,$title,$subject,$mailContent,$attach);
		if ($temp) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * 
	 */
	protected function setEventResponse($response,$eventResponse,$totalMemberCount,$totalGuestCount,$totalChildrenCount,$totalAttendeesCount)
	{
		$listArray = [];
		if (is_array($eventResponse)) {
			if(is_array($response)){
				foreach ($response as $key => $value) {
					$result = [
							'memberId' => (!empty($value['memberid'])) ? (int)$value['memberid']: 0,
							'memberTitle' => (!empty($value['membertitle'])) ? $value['membertitle']:'',
							'memberName' => (!empty($value['name'])) ? $value['name']:'',
							'memberImage' => (!empty($value['memberpic'])) ? yii::$app->params['imagePath'].$value['memberpic']:'',
							'memberPhone' => (!empty($value['phone'])) ? $value['phone']:'',
							'memberEmail' => (!empty($value['memberemail'])) ? $value['memberemail']:'',
							'memberCount' => (!empty($value['membercount'])) ? (int)$value['membercount'] : 0,
							'guestCount' => (!empty($value['guestcount'])) ? (int)$value['guestcount']: 0,
							'childrenCount' => (!empty($value['childrencount'])) ? (int)$value['childrencount']: 0,
							'isResponded' => (!empty($value['acksentdatetime']) || $value['acksentdatetime'] == '--') ? true : false,
							'rsvpSubmittedDate' => (!empty($value['createddatetime'])) ? (date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($value['createddatetime'])). ' '. date(Yii::$app->params['dateFormat']['time12Hr'], strtotimeNew($value['createddatetime']))):'',
					];

					$totalMemberCount += $result['memberCount'];
                    $totalGuestCount += $result['guestCount'];
                    $totalChildrenCount += $result['childrenCount'];
					array_push($listArray, $result);
				}
				$totalAttendeesCount = $totalMemberCount + $totalGuestCount + $totalChildrenCount;
			}
			$data = new \stdClass();
			$data->eventId = (!empty($eventResponse['eventid'])) ? (int)$eventResponse['eventid']: 0;
			$data->eventTitle = (!empty($eventResponse['notehead'])) ? $eventResponse['notehead']:'';
			$data->eventVenue = (!empty($eventResponse['venue'])) ? $eventResponse['venue']:'';
			$date = date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($eventResponse['activitydate']));
			$time = date(Yii::$app->params['dateFormat']['time12Hr'], strtotimeNew($eventResponse['activitydate']));
			$data->eventTime = $time ? $time : '';
			$data->eventDate = $date ? $date :'';
			$data->institutionId = (!empty($eventResponse['institutionid'])) ? (int)$eventResponse['institutionid']: 0;
			$data->institutionName = (!empty($eventResponse['institution'])) ? $eventResponse['institution']:'';
			$data->totalMemberCount = $totalMemberCount;
			$data->totalGuestCount = $totalGuestCount;
			$data->totalChildrenCount = $totalChildrenCount;
			$data->totalAttendeeCount = $totalAttendeesCount;
			$data->attendees = $listArray;
			return $data;
		} else {
			$data = new \stdClass();
			$data->eventId = '';
			$data->eventTitle = '';
			$data->eventVenue = '';
			$data->eventTime = '';
			$data->eventDate = '';
			$data->institutionId = (!empty($eventResponse['institutionid'])) ? (int)$eventResponse['institutionid']: 0;
			$data->institutionName = (!empty($eventResponse['institution'])) ? $eventResponse['institution']:'';
			$data->totalMemberCount = $totalMemberCount;
			$data->totalGuestCount = $totalGuestCount;
			$data->totalChildrenCount = $totalChildrenCount;
			$data->totalAttendeeCount = $totalAttendeesCount;
			$data->attendees = $listArray;
			return $data;
		}
	}
	/**
	 * To send feedback
	 * acknowledgement mail
	 * to the user
	 */
	protected function sendFeedbackReply($userId,$adminEmail,$feedbackId,$replyMessage,$replyToEmail,$institutionId,$institutionName,$institutionLogo)
	{
		$title = '';
		$subject = 'Feedback Reply';
		$attach = '';
		$mailContent['template'] = 'feedback-reply';
		$mailContent['content'] = $replyMessage;
		$mailContent['institutionname'] = $institutionName;
		$mailContent['logo'] = ($institutionLogo) ? yii::$app->params['imagePath'].$institutionLogo : Yii::$app->params['imagePath'].'/institution/institution-icon-grey.png';
		$mailobj = new EmailHandlerComponent();
		$temp =  $mailobj->sendEmail($adminEmail,$replyToEmail,$title,$subject,$mailContent,$attach);
		if ($temp) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * To send rsvp
	 * acknowledgement mail
	 * to the user
	 */
	protected function sendRsvpAcknowledgement($replyMessage,$institutionName,
										$userId,$institutionId,$eventId,$replyToEmail,$memberId, $logo, $eventHeading)
	{
		$title = '';
		$subject = "RSVP Reply of $eventHeading";
		$attach = '';
		$adminEmail = Yii::$app->params['tempEmail'];
		$mailContent['template'] = 'rsvp-reply';
		$mailContent['content'] = $replyMessage;
		$mailContent['institutionname'] = $institutionName;
		//Institution logo
        if(!empty($logo)){
            $logo = Yii::$app->params['imagePath'].$logo;
        } else {
            $logo = Yii::$app->params['imagePath'].'/institution/institution-icon-grey.png';
        }
        $mailContent['logo'] = $logo;
		$mailobj = new EmailHandlerComponent();
		$temp =  $mailobj->sendEmail($adminEmail,$replyToEmail,$title,$subject,$mailContent,$attach);
		if ($temp) {
			return true;
		} else {
			return false;
		}
	}
}	
