<?php 

namespace api\modules\v3\controllers;

use common\models\extendedmodels\ExtendedInstitution;
use Helper\Extended;
use Yii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use common\models\basemodels\BaseModel;
use common\components\ConversationsComponent;
use api\modules\v3\controllers\BaseController;
use common\components\PushNotificationHandler;
use common\models\extendedmodels\ExtendedEvent;
use api\modules\v3\models\responses\ApiResponse;
use common\models\extendedmodels\ExtendedBills;
use common\models\extendedmodels\ExtendedMember;
use common\models\extendedmodels\ExtendedCommittee;
use common\models\extendedmodels\ExtendedConversation;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedCommitteePeriod;
use common\models\extendedmodels\ExtendedUserConversation;
use common\models\extendedmodels\ExtendedConversationtopic;
use common\models\extendedmodels\ExtendedUserConversationTopic;
use common\models\extendedmodels\ExtendedNotificationsentdetails;
use common\models\extendedmodels\ExtendedDevicedetails;
use common\models\basemodels\Qurbana;
use Exception;


class CommitteeController extends BaseController
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
							'get-committee-members' => ['GET'],
							'mark-notification-type-as-read' => ['POST'],
							'close-conversation' => ['POST'],
							'get-conversation-recipients-by-institution' => ['GET'],
							'get-conversation-topics' => ['GET'],
							'get-conversation-chats' => ['GET'],
							'get-badge-count' => ['GET'],
							'get-participants-in-conversation' => ['GET'],
							'save-conversation-topic' => ['POST'],
							'save-chat' => ['POST'],
						]
				],
			]
		);
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
	 * Get committe members 
	 * @return $statusCode int
	 * @return $data array
	 * @return $message string
	*/
	public function actionGetCommitteeMembers()
	{
		$request = Yii::$app->request;
		$periodId = $request->get('periodId');
		$institutionId = $request->get('institutionId');
		$committeeTypeId = $request->get('committeeTypeId');

		$periodModel = new ExtendedCommitteePeriod();
        $committeeMembers = array();
        $userId = Yii::$app->user->identity->id;

        try{
        	$periodId = filter_var($periodId, FILTER_SANITIZE_NUMBER_INT);
        	$institutionId = filter_var($institutionId, FILTER_SANITIZE_NUMBER_INT);
        	$committeeTypeId = filter_var($committeeTypeId, FILTER_SANITIZE_NUMBER_INT);
	        if ($userId) {
				$committeeMemberDetails = 
					$periodModel->getCommitteeMembers(
					$committeeTypeId, $periodId, $institutionId, null);
				if($committeeMemberDetails){
					foreach($committeeMemberDetails as $model){
						if($model['active']){
							$result = [
								'memberId' => (!empty($model['memberid'])) ? (int)$model['memberid'] : 0,
								'title' => (!empty($model['title'])) ? $model['title'] : '',
								'name' => (!empty($model['membername'])) ? $model['membername'] : '',
								'userThumbnailImage' => (!empty($model['memberimage'])) ? (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$model['memberimage']) : '',
								'contactNumber' => (!empty($model['memberphone'])) ? (int)$model['memberphone'] : 0,
								'email' => (!empty($model['memberemail'])) ? $model['memberemail'] : '',
								'position' => (!empty($model['description'])) ? $model['description'] : '',
								'isSpouse' => $model['isspouse'] == 1 ? true : false
							];
							array_push($committeeMembers,$result);
						}
					}
				}
				$data = [
					'committeeMembers' => $committeeMembers
				];
				$this->statusCode = 200;
				$this->message = '';
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data,$this->message);
			}
			else{
				$this->statusCode = 498;
				$this->message = 'Session invalid';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
				}
		} catch(Exception $e){
			yii::error($e->getMessage());
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}

	/**
	 * Mark notification type as read
	 * @return $statusCode int
	 * @return $data array
	 * @return $message string
	*/
	public function actionMarkNotificationTypeAsRead()
	{
		$request = Yii::$app->request;
		$notificationType = $request->post('notificationType');
		$readCount = $request->post('readCount');
		$itemId = $request->post('itemId');
		$conversationArray = [];

    	$userConversationTopicModel = new ExtendedUserConversationTopic();
    	$conversationModel = new ExtendedConversation();
    	$userConversationModel = new ExtendedUserConversation();

    	$userId = Yii::$app->user->identity->id;

        try{
        	$notificationType = filter_var($notificationType, FILTER_SANITIZE_NUMBER_INT);
        	$readCount = filter_var($readCount, FILTER_SANITIZE_NUMBER_INT);
        	$itemId = filter_var($itemId, FILTER_SANITIZE_NUMBER_INT);

        	if($userId){
        		$status = $userConversationTopicModel->updateConversationTopicStatus($itemId, $userId);
        		$conversations = $conversationModel->getConversationsUnderTopic($itemId);
        		if($conversations){
        			foreach($conversations as $model){
        				$result = [$model['conversationid'], $userId, true, gmdate("Y-m-d H:i:s")];
        				array_push($conversationArray, $result);
        			}
        			$status = $userConversationModel->saveUserConversation($conversationArray);
        		}

        		$this->statusCode = 200;
				$this->message = '';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode, $this->data,$this->message);
        	}
        	else{
        		$this->statusCode = 498;
				$this->message = 'Session invalid';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
        	}
        } catch(Exception $e){
        	yii::error($e->getMessage());
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request'.$e->getMessage().$e->getLine();
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}

	/**
	 * Close conversation 
	 * @return $statusCode int
	 * @return $data array
	 * @return $message string
	*/
	public function actionCloseConversation()
	{
		$request = Yii::$app->request;
		$conversationId = $request->post('conversationId');
		$conversionTopicModel = new ExtendedConversationtopic();
		$userId = Yii::$app->user->identity->id;
		try {
        	if($userId){
        		$status = $conversionTopicModel->closeConversation(
        			$conversationId, $userId);
        		if($status){
        			$this->statusCode = 200;
					$this->message = '';
					$this->data =  new \stdClass();
					return new ApiResponse($this->statusCode, $this->data,$this->message);
        		}
        		else{
        			$this->statusCode = 500;
					$this->message = 'Invalid user';
					$this->data = new \stdClass();
					return new ApiResponse($this->statusCode,$this->data,$this->message);
        		}
        	}
        	else
        	{
        		$this->statusCode = 498;
				$this->message = 'Session invalid';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
        	}
        } catch(Exception $e) {
        	yii::error($e->getMessage());
        	$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
        }
    }

    /**
	 * Get conversation recipients by institution
	 * @return $statusCode int
	 * @return $data array
	 * @return $message string
	*/
	public function actionGetConversationRecipientsByInstitution()
	{
		$request = Yii::$app->request;
		$institutionId = $request->get('institutionId');
		$committeeModel = new ExtendedCommittee();
		$flag = 0 ;
		$positions = [];
		$committeeMembers = [];
		$userId = Yii::$app->user->identity->id;
		try {
        	if ($userId) {
        		if ($institutionId) {
	        		$members = $committeeModel->getCommitteeMemberUnderInstitution(
	        			$institutionId);
	        		if($members){
	        			ArrayHelper::multisort($members, ['userid'], [SORT_ASC]);

	        			$userList = [];
	        			foreach($members as $model){

	        				if($model['userid'] == $userId){
	        					continue;
	        				}
	        				else{
	        					if(in_array($model['userid'], $userList)){
	        						$positionList = [];
	        						$positionList = [
	        							'position' => $model['description'], 
	        							'committee' => $model['committeetype']
	        						];
	                                array_push($positions, $positionList);
	        					}
	        					else{
	        						if($flag != 0){
	        							$memberList['positions'] = $positions;
		        		                array_push($committeeMembers, $memberList);
	        							$positions = [];
	        						}

	        						$memberList = [
	        							'recipientId' => (int)$model['userid'], 
	        							'name' => $model['title'] . ' ' . 
		                            	$model['membername']
		                            ];

		                            $positionList =  [
		        						'position' => $model['description'],
		                                'committee' => $model['committeetype']
		                            ];
	                                array_push($positions, $positionList);
	        						array_push($userList, $model['userid']);
	        						$flag = 1;
	        					}
	        				}
	        			}
		        		$memberList['positions'] = $positions;
		        		array_push($committeeMembers, $memberList);
	        		}
	        		$this->statusCode = 200;
					$this->message = '';
					$this->data = ['committeeMembers' => $committeeMembers];
					return new ApiResponse($this->statusCode, $this->data, $this->message);
				}
				else{
					$this->statusCode = 601;
					$this->message = 'Inactive Institution';
					$this->data = new \stdClass();
					return new ApiResponse($this->statusCode,$this->data,$this->message);
				}
        	}
        	else
        	{
        		$this->statusCode = 498;
				$this->message = 'Session invalid';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
        	}
        }
        catch(ErrorException $e){
        	$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
        }
    }

    /**
	 * Get conversation topics
	 * @return $statusCode int
	 * @return $data array
	 * @return $message string
	*/
	public function actionGetConversationTopics()
	{
		$request = Yii::$app->request;
		$conversations = [];
		$committeeModel = new ExtendedCommittee();
		$conversationTopicModel = new ExtendedConversationtopic();
		$logginUserId = Yii::$app->user->identity->id;
		try {
			if ($logginUserId) {
				$committeeInstittution = $committeeModel->getAllCommitteeInstitutionOfUser($logginUserId);
				if ($committeeInstittution) {
					$institutions = implode(",", $committeeInstittution);
					$conversationList = 
						$conversationTopicModel->getConversationTopics($logginUserId,$institutions);
					
					if($conversationList){
						foreach($conversationList as $model){
							$model['createddatetime'] = !empty($model['createddatetime']) ? BaseModel::convertToUserTimezone($model['createddatetime']) : null;
							$result = [
								'conversationId' => (!empty($model['conversationtopicid'])) ? (int)$model['conversationtopicid'] : 0,
								'title' => (!empty($model['title'])) ? $model['title'] : '',
								'startedBy' => (!empty($model['membername'])) ? $model['membername'] : '',
								'date' => (!empty($model['createddatetime'])) ? 
									date_format(date_create(
										$model['createddatetime']),Yii::$app->params['dateFormat']['viewDateFormat'])
									 : '',
								'time' => (!empty($model['createddatetime'])) ? 
									date_format(date_create(
										$model['createddatetime']),Yii::$app->params['dateFormat']['time12Hr'])
									 : '',
								'unreadMessages' => (!empty($model['unreadMessages'])) ? (int)$model['unreadMessages'] : 0,
								'institution' =>(!empty($model['institution'])) ? 
									$model['institution'] : '',
								'institutionId' =>(!empty($model['institutionid'])) ? 
									(int)$model['institutionid'] : 0,
								'message' => (!empty($model['message'])) ? 
									$model['message'] : '',
								'userThumbnailImage' => (!empty($model['memberimage'])) ? (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$model['memberimage'] ): '',
								'initiator' => $model['initiator'] ? true : false,
								
							];
							array_push($conversations, $result);
						}
					}
				}
				$this->statusCode = 200;
				$this->message = '';
				$this->data = [
					'conversations' => $conversations
				];
				return new ApiResponse($this->statusCode, $this->data, $this->message);
			}
			else{
				$this->statusCode = 498;
				$this->message = 'Session invalid';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			}
		} catch(Exception $e){
			yii::error($e->getMessage());
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}

	/**
	 * Get conversation chats
	 * @return $statusCode int
	 * @return $data array
	 * @return $message string
	*/
	public function actionGetConversationChats()
	{
		$request = Yii::$app->request;
		$conversationId = $request->get('conversationId');

		$chats = [];
		$conversation = new \stdClass();
		$committeeModel = new ExtendedCommittee();
		$conversationModel = new ExtendedConversation();
		$conversationObject = new ConversationsComponent();

		$userId = Yii::$app->user->identity->id; 
		try {
			if ($userId) {
				$committeeInstittution = $committeeModel->getAllCommitteeInstitutionOfUser($userId);
				if($committeeInstittution){
					$conversationDetails = $conversationModel->getConversationsDetails($conversationId); 
					
					if($conversationDetails){
						$conversationsList = $conversationModel->getConversationsUnderTopic($conversationId);
						$conversationTopics = $conversationObject->getConversationSubjectTitleByTopic($conversationId);
						if($conversationsList){
							foreach($conversationsList as $model){
								$model['createddatetime'] = BaseModel::convertToUserTimezone($model['createddatetime']);
								$result = [
									'conversationId' => (!empty($model['conversationid'])) ? $model['conversationid'] : '',
									'chatContent' => (!empty($model['conversation'])) ? $model['conversation'] : '',
									'repliedBy' => (!empty($model['username'])) ? $model['username'] : '',
									'date' => (!empty($model['createddatetime'])) ? 
										date_format(date_create(
											$model['createddatetime']),Yii::$app->params['dateFormat']['viewDateFormat'])
										 : '',
									'time' => (!empty($model['createddatetime'])) ? 
										date_format(date_create(
											$model['createddatetime']),Yii::$app->params['dateFormat']['time12Hr'])
										 : '',
									'userThumbnailImage' => (!empty($model['memberimage'])) ? (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$model['memberimage'] ): '',	
								];
								array_push($chats, $result);
							}
							
						}
						$conversation->conversationId = (!empty($conversationDetails['conversationtopicid'])) ? $conversationDetails['conversationtopicid'] : '';
						$conversation->institutionId = (!empty($conversationDetails['institutionid'])) ? (int)$conversationDetails['institutionid'] : 0;
						$conversation->institution =  (!empty($conversationDetails['institutionname'])) ? $conversationDetails['institutionname'] : '';
						$conversation->title = (!empty($conversationDetails['title'])) ? $conversationDetails['title'] : '';
						$conversation->message = (!empty($conversationDetails['subject'])) ? $conversationDetails['subject'] : '';
						$conversation->startedBy =  (!empty($conversationDetails['username'])) ? $conversationDetails['username'] : '';
						$conversationDetails['createddatetime'] =  (!empty($conversationDetails['createddatetime'])) ? BaseModel::convertToUserTimezone($conversationDetails['createddatetime']) : null;
						$conversation->date = (!empty($conversationDetails['createddatetime'])) ?
						date_format(date_create(
						    $conversationDetails['createddatetime']),Yii::$app->params['dateFormat']['viewDateFormat'])
						    : '';
						$conversation->time = (!empty($conversationDetails['createddatetime'])) ?
						date_format(date_create(
						    $conversationDetails['createddatetime']),Yii::$app->params['dateFormat']['time12Hr'])
						    : '';
						$conversation->userThumbnailImage = (!empty($conversationDetails['memberimage'])) ? (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$conversationDetails['memberimage'] ): '';
						$conversation->initiator = $conversationDetails['createdby'] == $userId ? true : false;
					}
				}
				$this->statusCode = 200;
				$this->message = '';
				$this->data = [
					'chats' => $chats,
					'conversation' => $conversation
				];
				return new ApiResponse($this->statusCode, $this->data, $this->message);
			}
			else{
				$this->statusCode = 498;
				$this->message = 'Session invalid';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			}
		}
		catch(ErrorException $e){
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}

	/**
	 * Get badge count
	 * @return $statusCode int
	 * @return $data array
	 * @return $message string
	*/
	public function actionGetBadgeCount()
	{
		
		$request = Yii::$app->request;
		$userId = $request->get('userId');
		$data = [];
		$user = Yii::$app->user->identity;
        $logginUserId = $user->id;
        $institutionId = $user->institutionid;
        $userType = $user->usertype;
        $memberDetails = ExtendedMember::getMemberId($logginUserId,$institutionId,$userType);
        $memberId = $memberDetails['memberid'];
        $adminCount = 0;
        $unreadbillcount = 0;
        $totalUnreadConersation = 0;
        $conversationCount = 0;

        $eventModel = new ExtendedEvent();
        $billModel = new ExtendedBills();
        $committeeModel = new ExtendedCommittee();
        $conversationTopicModel = new ExtendedConversationtopic();
        $notificationSentModel = new ExtendedNotificationsentdetails();

        try{
	        if ($logginUserId) {
	            $data = [
	                'announcementCount' => 0,
	                'birthdayCount' => 0,
	                'anniversaryCount' => 0,
	                'eventsCount' => 0,
	                'conversationCount' => 0,
	                'billsCount' => 0,
	                'adminOptionCount' => 0,
	                'pendingProfileCount' => 0,
	                'pendingFeedbackCount' => 0,
	                'pendingPrayerCount' => 0,
	                'pendingRsvpCount' => 0,
	                'pendingAlbumCount' => 0,
	                'pendingFoodOrderCount' => 0,
					'pendingQurbanaRequestsCount' => 0,
	            ];
	        	//Notications count
	        	$notificationCount = $eventModel->getEventCount($userId, gmdate("Y-m-d H:i:s"));
	        	if ($notificationCount) {
	        	        $data ['announcementCount'] = (int)$notificationCount['announcementcount'];
	        	        $data ['birthdayCount'] = ((int)$notificationCount['memberbirthday'] +(int)$notificationCount['spousebirthday']);
	        	        $data ['anniversaryCount'] = (int)$notificationCount['weddingannuversery'];
	        	        $data ['eventsCount'] = (int)$notificationCount['eventcount'];
	        	}

	        	//Unread count
	        	$committeeInstittution = $committeeModel->getAllCommitteeInstitutionOfUser($logginUserId);
				if ($committeeInstittution) {
					$institutions = implode(',', $committeeInstittution);
					//ConversationCount
					$conversationCount = ExtendedConversationtopic::getTotalConversationCount($logginUserId, $institutions);
				}

				//Bill count	
				$billCount = $billModel-> getBillSeenCount($institutionId, $memberId, 
						$userType);
				//Management count
				$adminCountList = $notificationSentModel->getManagementCount($userId,date("Y-m-d H:i:s"),yii::$app->params['clubAppPrivileges']['manageFoodOrders'], yii::$app->params['propertyGroup']['restaurant']);

				//Qurbana Count
				$qurbanaModel = new Qurbana();
				$qurbanaCount = $qurbanaModel->getPresentQurbanaCount($memberId, $institutionId);
				if($qurbanaCount){
					$data ['pendingQurbanaRequestsCount'] = (int)$qurbanaCount;
				}

				if($conversationCount){
					$totalUnreadConersation = $conversationCount['unreadtopiccount'];
					$data ['conversationCount'] = (int)$totalUnreadConersation;
					
				}

				if($billCount){
					$unreadbillcount = $billCount['billseencount'];
					$data ['billsCount'] = (int)$unreadbillcount;
				}

				if ($adminCountList) {
				    $adminCount = $adminCountList['prayerRequestCount'] + $adminCountList['rsvpCount'] + $adminCountList['profileApprovalCount'] + $adminCountList['feedbackCount'] + $adminCountList['albumCount'] + $adminCountList['orderCount'];
				    $data ['adminOptionCount'] = (int)$adminCount;
				    $data ['pendingProfileCount'] = (int)$adminCountList['profileApprovalCount'];
				    $data ['pendingFeedbackCount'] = (int)$adminCountList['feedbackCount'];
				    $data ['pendingPrayerCount'] = (int)$adminCountList['prayerRequestCount'];
				    $data ['pendingRsvpCount'] = (int)$adminCountList['rsvpCount'];
				    $data ['pendingAlbumCount'] = (int)$adminCountList['albumCount'];
				    $data ['pendingFoodOrderCount'] = (int)$adminCountList['orderCount'];
				}
                $this->statusCode = 200;
				$this->message = '';
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data,$this->message);		
			} else {
				$this->statusCode = 498;
				$this->message = 'Session invalid';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			}
		} catch(Exception $e){
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}

	/**
	 * Get participants in conversation
	 * @return $statusCode int
	 * @return $data array
	 * @return $message string
	*/
	public function actionGetParticipantsInConversation()
	{
		$request = Yii::$app->request;
		$institutionId = $request->get('institutionId');
		$conversationId = $request->get('conversationId');

		$committeeModel = new ExtendedCommittee();
		$conversationTopicModel = new ExtendedConversationtopic();
		$flag = 0 ;
		$positions = [];
		$committeeMembers = [];
		$userId = Yii::$app->user->identity->id;

		try {
        	if ($userId) {
        		$participantList = $conversationTopicModel->getAllCommitteeMemberUnderTopic($institutionId, $conversationId);

        		if($participantList){
	        		$memberDetails = $committeeModel->getCommitteeMemberUnderInstitution(
	        			$institutionId);
	        		if($memberDetails){
	        			ArrayHelper::multisort($participantList, ['userid','designationorder'], [SORT_ASC,SORT_ASC]);

	        			$userList = [];
	        			foreach($participantList as $model) {
	        				if ($model['userid'] == $userId) {
	        					continue;
	        				} else {
	        					if(in_array($model['userid'], $userList)){
	        						continue;
	        					} else {
	        						if ($memberDetails) {
	        							foreach($memberDetails as $member) {
	        								if($member['userid'] == $userId) {
	        									continue;
	        								} else {
	        									if($model['userid'] == $member['userid']){
	        										$positionList = [];
		        									$positionList = [
					        							'position' => $member['description'], 
					        							'committee' => $member['committeetype']
					        						];
					                                array_push($positions, $positionList);

	        									} else {
	        										continue;
	        									}
	        								}
	        							}	        				
	        						}
	        						$imageThumbnail = (!empty($model['thumbnailimage'])) ?
	                            		(string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].
	                            		$model['thumbnailimage']) : ((!empty($model['image'])) ? (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$model['image']) :'');
	                            	$memberName = ((!empty($model['membername'])) ? $model['membername'] : '');
	        						$memberList = [
	        							'recipientId' => (int)$model['userid'], 
	        							'name' => trim($memberName),
	        						    'title' =>(!empty($model['title'])) ? $model['title'] : '',
		                            	'imageThumbnail' => $imageThumbnail,
		                            	'positions' => $positions
		                            ];
		                            $positions = [];
	                                array_push($committeeMembers, $memberList);
	        						array_push($userList, $model['userid']);
	        					}
	        				}
	        			}
		        		
	        		}
	        		$this->statusCode = 200;
					$this->message = '';
					$this->data = ['committeeMembers' => $committeeMembers];
					return new ApiResponse($this->statusCode, $this->data, $this->message);
				}
				else{
					$this->statusCode = 601;
					$this->message = 'Inactive Institution';
					$this->data = new \stdClass();
					return new ApiResponse($this->statusCode,$this->data,$this->message);
				}
        	}
        	else
        	{
        		$this->statusCode = 498;
				$this->message = 'Session invalid';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
        	}
        }
        catch(ErrorException $e){
        	$this->statusCode = 500;
        	yii::error('An error occurred while processing the request'.$e->getMessage().$e->getLine());
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
        }
    }

    /**
	 * Save conversation
	 * @return $statusCode int
	 * @return $data array
	 * @return $message string
	*/
	public function actionSaveConversationTopic()
	{
		$request = Yii::$app->request;
		$title = $request->post('title');
		$message = $request->post('message');
		$memberId = $request->post('memberId');
		$institutionId = $request->post('institutionId');
		$recipients = $request->post('recipients');


		$recipientArray = [];
		$committeeMembers = [];
		$deviceModel = new ExtendedDevicedetails();
		$committeeModel = new ExtendedCommittee();
		$conversationTopicModel = new ExtendedConversationtopic();
		$userConversionTopicModel = new ExtendedUserConversationTopic();
		$userId = Yii::$app->user->identity->id;
		$userType = Yii::$app->user->identity->usertype;
		$institutionDetails = ExtendedInstitution::find()->where(['id' =>$institutionId])->asArray()->one();
		$institutionId = $institutionId;
		$institutionName = $institutionDetails['name'];
		$memberDetails = ExtendedMember::getMemberId($userId, $institutionId, $userType);
    	$memberId = $memberDetails['memberid'];
        if($userType == 'M'){
    	   $memberResponse = ExtendedMember::getMemberName($memberId);
        }
        else{
            $memberResponse = ExtendedMember::getSpouseName($memberId);
        }
        if(empty($memberResponse['middleName'])){
			$userName = $memberResponse['firstName'].' '.$memberResponse['lastName'];
		}
		else{
			$userName = $memberResponse['firstName'].' '.$memberResponse['middleName'].' '.$memberResponse['lastName'];
		}
		$pushNotificationHandler = Yii::$app->PushNotificationHandler;

		try
		{
        	if($userId != 0){
        		$committeeInstitution = $committeeModel->checkUserAvailableInCurrentCommittee($userId, $institutionId);
        		if($committeeInstitution){

        			$conversationTopic = [
        				'subjectTitle' => $title,
                    	'subject' => $message,
                    	'createdBy' => $userId,
                    	'institutionId' => $institutionId,
                    	'createdDateTime' => gmdate("Y-m-d H:i:s")
                    ];
                    $topicId = $conversationTopicModel->addConversationTopic(
                    	$conversationTopic);

                    if($recipients){
                    	foreach($recipients as $model){
	        				$result = [
	        					$topicId,
	        					$model['recipientId'], 
	        					false
	        				];
	        				array_push($recipientArray, $result);
	        			}
	        			$userResult = [
	        					$topicId,
	        					$userId, 
	        					true
	        			];
	        			array_push($recipientArray, $userResult);

	        			$userConversionTopicModel->addUserConversionTopic($recipientArray);
                    }
                    else{
                    	$committeeMembers = $committeeModel->getCommitteeMemberUnderInstitution($institutionId);
                    	if($committeeMembers){
                    		foreach($committeeMembers as $model){
                    			$result = [
		        					$topicId,
		        					$model['userid'], 
		        					false
	        					];
	        					array_push($recipientArray, $result);
                    		}
                    		$userConversionTopicModel->addUserConversionTopic($recipientArray);
                    	}
                    }


                    //Notification
                    if($recipients){
                    	foreach($recipients as $model){
                    		$deviceList = $deviceModel->getUserDevices($model['recipientId']);
                    		if($deviceList){
                    			$converstionTopicCount = 0;
                    			foreach ($deviceList as $device){
                    				$conversationTopicNotification = ExtendedConversationtopic::getTotalConversationCount($model['recipientId'], $institutionId);
                    				if($conversationTopicNotification){
                    					$converstionTopicCount = 
                    						$conversationTopicNotification['unreadconversationcount'];
                    				}
                    				if($userId != $model['recipientId']){
                    					// if(strtolower($device['devicetype']) == 'android'){
                						$message = 'Message from ' . $userName;
	                                	$notificationData = $pushNotificationHandler->setPushNotificationRequest($device['deviceid'], $message, 'conversation', $institutionId, $topicId, $institutionName, strtolower($device['devicetype']),$model['recipientId']);
	                                	if($notificationData){
	                                		$pushNotificationHandler->sendNotification($device['devicetype'], $device['deviceid'], $notificationData);
	                                	}
                    						//SendGCMNotification(deviceItem.DeviceID, "Message from " + currentUserName, "conversation", topicId, userID.RecipientId, ApplicationContext.Current.UserInfo.InstitutionId, InstitutionName, converstionTopicCount);
                    					// }
                    					// elseif(strtolower($device['devicetype']) == 'ios'){
                    					// 	// SendEventAPNSNotification(deviceItem.DeviceID, "Message from " + currentUserName, "conversation", topicId, userID.RecipientId, ApplicationContext.Current.UserInfo.InstitutionId, InstitutionName, converstionTopicCount);
                    					// }
                    				}
                    			}
                    		}
                    	}
                    }
                    else if($committeeMembers)
                    {
                    	foreach($committeeMembers as $model){
                    		$deviceList = $deviceModel->getUserDevices($model['userid']);
                    		if($deviceList){
                    			$converstionTopicCount = 0;
                    			foreach ($deviceList as $device){
                    				$conversationTopicNotification = ExtendedConversationtopic::getTotalConversationCount($device['recipientId'], $institutionId);
                    				if($conversationTopicNotification){
                    					$converstionTopicCount = 
                    						$conversationTopicNotification['unreadconversationcount'];
                    				}
                    				if($userId != $device['recipientId']){
                    					// if(strtolower($device['devicetype']) == 'android'){

                						$message = 'Message from ' . $userName;
	                                	$notificationData = $pushNotificationHandler->setPushNotificationRequest($device['deviceid'], $message, 'conversation', $institutionId, $topicId, $institutionName, strtolower($device['devicetype']),$model['recipientId']);
	                                	if($notificationData){
	                                		$pushNotificationHandler->sendNotification($device['devicetype'], $device['deviceid'], $notificationData);
	                                	}

                    						//SendGCMNotification(deviceItem.DeviceID, "Message from " + currentUserName, "conversation", topicId, userID.RecipientId, ApplicationContext.Current.UserInfo.InstitutionId, InstitutionName, converstionTopicCount);
                    					// }
                    					// elseif(strtolower($device['devicetype']) == 'ios'){
                    					// 	// SendEventAPNSNotification(deviceItem.DeviceID, "Message from " + currentUserName, "conversation", topicId, userID.RecipientId, ApplicationContext.Current.UserInfo.InstitutionId, InstitutionName, converstionTopicCount);
                    					// }
                    				}
                    			}
                    		}
                    	}
                    }

					$this->statusCode = 200;
					$this->message = '';
					$this->data = ['conversationId' => $topicId];
					return new ApiResponse($this->statusCode, $this->data, $this->message);
				}
				else{
					$this->statusCode = 602;
					$this->message = 'Your authorization has been revoked.';
					$this->data = new \stdClass();
					return new ApiResponse($this->statusCode,$this->data,$this->message);
				}
        	}
        	else
        	{
        		$this->statusCode = 498;
				$this->message = 'Session invalid';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
        	}
        }
        catch(ErrorException $e){
        	yii::error($e->getMessage());
        	$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
        }
    }

    /**
	 * Save conversation chat
	 * @param userId int
	 * @param conversationId int
	 * @param institutionId int
	 * @param chatContent string
	 * @return $statusCode int
	 * @return $data array
	 * @return $message string
	*/
	public function actionSaveChat()
	{
		$request = Yii::$app->request;
		$institutionId = $request->getBodyParam('institutionId');
		$conversationId = $request->getBodyParam('conversationId');
		$chatContent = $request->getBodyParam('chatContent');
		$dateTime = gmdate("Y-m-d H:i:s");
		$userId = Yii::$app->user->identity->id;
		try {
        	if ($userId) {
        		$committeeId = ExtendedCommittee::checkUserAvailableInCurrentCommittee($userId, $institutionId);

        		if ($committeeId) {
        			$this->addConversation($userId, $institutionId, $conversationId, $chatContent, $dateTime);
	        		$this->statusCode = 200;
					$this->message = '';
					$this->data = new \stdClass();
					return new ApiResponse($this->statusCode, $this->data, $this->message);
				} else {
					$this->statusCode = 602;
					$this->message = 'Your authorization has been revoked.';
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
        catch(ErrorException $e){
        	yii::error($e->getMessage());
        	$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
        }
    }

    /* Add conversation
    */
    protected function addConversation($userId, $institutionId, $conversationId, $chatContent, $dateTime)
    {
    	
    	$deviceModel = new ExtendedDevicedetails();
    	$pushNotificationHandler = Yii::$app->PushNotificationHandler;

    	try {
    		$userType = Yii::$app->user->identity->usertype;
    		$memberDetails = ExtendedMember::getMemberId($userId, $institutionId, $userType);
	    	$memberId = $memberDetails['memberid'];
	        if($userType == 'M'){
	    	   $memberResponse = ExtendedMember::getMemberName($memberId);
	        }
	        else{
	            $memberResponse = ExtendedMember::getSpouseName($memberId);
	        }

	        if(empty($memberResponse['middleName'])){
				$userName = $memberResponse['firstName'].' '.$memberResponse['lastName'];
			}
			else{
				$userName = $memberResponse['firstName'].' '.$memberResponse['middleName'].' '.$memberResponse['lastName'];
			}

    		$institutionDetails = Yii::$app->user->identity->institution;
	    	$institutionName = $institutionDetails['name'];

        	//Save conversation
        	$id = ExtendedConversation::createConversation($conversationId, $chatContent, $userId, $dateTime);

        	//Save userconversation
        	$userConversationStatus = ExtendedUserConversation::saveUserConversationChat($id, $userId, false, $dateTime);

        	//Get conversation recepients
        	$userList = ExtendedConversation::getConversationsRecepients($conversationId);

        	//Get conversation topic details
        	$conversationTopicDetails = ExtendedConversation::getConversationsDetails($conversationId);
        	if($conversationTopicDetails){
        		$institutionName = $conversationTopicDetails['institutionname'];
        	}
        	if($userList){
        		foreach($userList as $user){
        			$deviceList = $deviceModel->getUserDevices($user['userid']);

        			if($deviceList){
        				$converstionTopicCount = 0;
        				foreach($deviceList as $device){
        					$conversationCount = ExtendedConversationtopic::getTotalConversationCount($user['userid'], $institutionId);
        					if($conversationCount){
        						$converstionTopicCount = $conversationCount['unreadtopiccount'];
        					}
        					if ($userId != $device['userid']){
                            	$message = 'Message from ' . $userName;
                            	$notificationData = $pushNotificationHandler->setPushNotificationRequest($device['deviceid'], $message, 'conversation-chat', $institutionId, $conversationId, $institutionName, strtolower($device['devicetype']),$device['userid']);
                            	if($notificationData){
                            		$pushNotificationHandler->sendNotification($device['devicetype'], $device['deviceid'], $notificationData);
                            	}

                                    // SendGCMNotification(deviceItem.DeviceID, "Message from " + currentUserName, "conversation-chat", conversation.ConversationTopicId, user.UserID, ApplicationContext.Current.UserInfo.InstitutionId, institutionName, converstionTopicCount);
                                // }
                                // else if(strtolower($device['devicetype']) == "ios")
                                // {
                                //     // SendEventAPNSNotification(deviceItem.DeviceID, "Message from " + currentUserName, "conversation-chat", conversation.ConversationTopicId, user.UserID, ApplicationContext.Current.UserInfo.InstitutionId, institutionName, converstionTopicCount);
                                // }
        					}
        				}
        			}
        		}
        	}
        	return true;
    	}
    	catch(ErrorException $e){
        	yii::error($e->getMessage());
        	$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
        }
    }
}