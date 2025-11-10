<?php 

namespace api\modules\v3\controllers;

use Yii;
use api\modules\v3\controllers\BaseController;
use api\modules\v3\models\responses\ApiResponse;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedSettings;
use common\models\extendedmodels\ExtendedCommittee;
use common\models\extendedmodels\ExtendedUserMember;
use common\models\extendedmodels\ExtendedBills;
use common\models\extendedmodels\ExtendedMember;
use common\models\extendedmodels\ExtendedEvent;
use common\models\extendedmodels\ExtendedDependant;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\extendedmodels\ExtendedCommitteegroup;
use common\models\extendedmodels\ExtendedCommitteePeriod;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedAddresstype;
use common\models\basemodels\BaseModel;
use common\models\basemodels\RememberAppConstModel;
use common\models\extendedmodels\ExtendedPrivilege;
use common\models\extendedmodels\ExtendedFamilyunit;
use common\models\extendedmodels\ExtendedInstitutioncontactfilters;
use common\components\FileuploadComponent;
use common\models\extendedmodels\ExtendedContactfilters;
use common\models\extendedmodels\ExtendedInstitutionType;
use common\models\extendedmodels\ExtendedDashboard;


class OfflineController extends BaseController
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
							'get-committee-members-for-sync' => ['GET'],
							'get-bills-for-sync' => ['GET'],
							'get-notifications-for-sync' => ['GET'],
							'get-institution-details-for-sync' => ['GET'],
							'get-member-details-file-for-sync' => ['GET']
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
	 * Gets the list of all 
	 * the events and news
	 */
	public function actionGetNotificationsForSync()
	{
		$requset = Yii::$app->request;
		$events = [];
		$announcementlist = [];

		$user = Yii::$app->user->identity;
		$userId = $user->id; 
		$institutionId = $user->institutionid;
		$userBatch = BaseModel :: getMemberBatches($userId,$institutionId);
		$userBatch = explode(',',$userBatch);
		$data = new \stdClass();
		if ($userId) {
			$activatedOn = gmdate("Y-m-d H:i:s");
			$eventData = ExtendedEvent::getEventData($userId, $activatedOn);
			$responseAnnouncements = ExtendedEvent::getAllAnnouncements($userId, $activatedOn);
			if (!empty($eventData)) {
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

					$date=date_create($data['activitydate']);
					$groupDate = date_format($date,"Y-m-d");
					if (!isset($modifiedArray[$groupDate])) {
						$modifiedArray[$groupDate] = array();
					}
					array_push($modifiedArray[$groupDate], $data);
				}
				foreach ($modifiedArray as $date => $eventData) {
					$event =[];
					$event['date']=$date;
					$eventlist =[];
					foreach ($eventData as $key => $value) {
						$result = [
								'eventId' => (!empty($value['id'])) ? $value['id'] : '',
								'eventTitle' => (!empty($value['notehead'])) ? $value['notehead'] : '',
								'eventTeaser' => '',
								'eventVenue' => (!empty($value['venue'])) ? $value['venue'] : '',
								'eventTime' => date(Yii::$app->params['dateFormat']['time12Hr'], strtotimeNew($value['time'])),
								'eventDate' => (!empty($value['activitydate'])) ? date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($value['activitydate'])) : '',
								'expiryDate' => (!empty($value['expirydate'])) ? date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($value['expirydate'])) : '',
								'eventBody' => (!empty($value['notebody'])) ? $value['notebody'] : '',
								'eventLogo' => '',
								'externalUrl' => (!empty($value['noteurl'])) ? $value['noteurl'] : '',
								'institutionLogo' => (!empty($value['EventLogo'])) ? (string)yii::$app->params['imagePath'].$value['EventLogo'] : '',
								'institutionName' => (!empty($value['name'])) ? $value['name'] : '',
								'isRead' => (!empty($value['viewedstatus'])) ? (bool)$value['viewedstatus'] : false,
								'rsvpAvailable' => (!empty($value['rsvpavailable'])) ? (bool)$value['rsvpavailable'] : false,
								'rsvpType' => (!empty($value['rsvpvalue'])) ? (int)$value['rsvpvalue'] : 0,
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
			}

			if (!empty($responseAnnouncements)) {
				foreach ($responseAnnouncements as $value) {

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

					$newsDateTime = (!empty($value['activitydate'])) ? $value['activitydate'] : '';
					$date = date('d-m-Y', strtotimeNew($newsDateTime));
					$announcementResult = [
						'notificationId' => (!empty($value['id'])) ? $value['id']:'',
						'notificationDate' => $date ? $date : '',
						'notificationHeader' => (!empty($value['notehead'])) ? $value['notehead']:'',
						'notificationBody' => (!empty($value['notebody'])) ? $value['notebody']:'',
						'institutionLogo' => (!empty($value['EventLogo'])) ? (string)yii::$app->params['imagePath'].$value['EventLogo']:'',
						'institutionName' => (!empty($value['name'])) ? $value['name']:'',
						'expiryDate' => (!empty($value['expirydate'])) ? date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($value['expirydate'])) : '',
						'isRead' => (!empty($value['viewedstatus'])) ? (bool)$value['viewedstatus']: false,
						'rsvpAvailable' => (!empty($value['rsvpavailable'])) ? (bool)$value['rsvpavailable']: false,
						'rsvpType' => (!empty($value['rsvpvalue'])) ? (int)$value['rsvpvalue']: 0,
						'rsvpAttendeesCount' =>[
							'memberCount' => (!empty($value['membercount'])) ? (int)$value['membercount'] : 0,
							'childrenCount' => (!empty($value['childrencount'])) ? (int)$value['childrencount'] : 0,
							'guestCount' => (!empty($value['guestcount'])) ? (int)$value['guestcount'] : 0,
						],
					];
					array_push($announcementlist,$announcementResult);
				} 
			}
			$data = [
				'events' => $events,
				'announcements' => $announcementlist,
			];
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
	}
	/**
	 * To get the 
	 * user's institution details for offline sync
	 */
	public function actionGetInstitutionDetailsForSync()
	{
		$request = Yii::$app->request;
		$institutionId = $request->get('institutionId');
		$feedbackTypes = [];
		$communicationAddresses = [];
		$institutions = [];
		$stafflist = [];
		$filters = [];
		$filterOptions = [];
		$committeeTypes = [];
		$familyUnit = [];
		if($institutionId) {
			$institutionId = filter_var($institutionId, FILTER_SANITIZE_NUMBER_INT);
			$institution = ExtendedInstitution::getInstitutionDetails($institutionId);
			$institutionType = $institution['institutiontype'];
			$userId = Yii::$app->user->identity->id;
			if($userId) {
				$memberIdDetails = ExtendedUserMember::getMemberIdByUserIdAndInstitutionId($userId,$institutionId);
				$memberId = $memberIdDetails['memberid'];
				$userType = $memberIdDetails['usertype'];
				$memberResponse = ExtendedMember::getMemberNameByMemberId($memberId);
				$response = ExtendedInstitution::getInstitution($institutionId);
				$ob = new \stdClass();
				if(!empty($response)) {
					$addressObj = new \stdClass();
					$addressObj->addressline1 = (!empty($response['address1'])) ? $response['address1'] : '';
					$addressObj->addressline2 = (!empty($response['address2'])) ? $response['address2'] : '';
					$addressObj->city = (!empty($response['district'])) ? $response['district'] : '';
					$addressObj->state = (!empty($response['state'])) ? $response['state'] : '';
					$addressObj->pincode = (!empty($response['pin'])) ? $response['pin'] : '';
					$addressObj->country = (!empty($response['location'])) ? $response['location'] : '';
					
					//institution contact filters
					$filterlist = ExtendedInstitutioncontactfilters::getInstitutionContactFilters($institutionId);
					if($filterlist) {
						$resultFilter = [];
						$filterOption = [];
						foreach ($filterlist as $key=>$value) {
							if($response['institutiontypeid'] == ExtendedInstitutionType::INSTITUTION_TYPE_CHURCH) {
								if($value['contactfilterid'] == ExtendedContactfilters::CONTACT_FILTER_FAMILYUNIT) {
									$familyObject = new ExtendedFamilyunit();
									$filterList = $familyObject->getActiveFamilyUnits($institutionId);
									if($filterList) {
										foreach ($filterList as $values) {
											$filterOption=[
													'optionId' => (isset($values['familyunitid'])) ? (int)$values['familyunitid']:0,
													'optionText' => (!empty($values['description'])) ? $values['description']:'',
											];
											array_push($filterOptions,$filterOption);
										}
									}
								}
							}
								
							$resultFilter=[
									'filterType' => (isset($value['contactfilterid']))? (int)$value['contactfilterid']:0,
									'filterName' => (!empty($value['description']))? $value['description']:'',
									'filterOptionType' => (isset($value['filteroptiontypeid']))? (int)$value['filteroptiontypeid']:'',
									'filterOptions' => $filterOptions,
									'filterMinValue' 	=> 0,
									'filterMaxValue' 	=> 0
							];
							//Tag search
							if($value['contactfilterid'] == ExtendedContactfilters::CONTACT_FILTER_TAGSEARCH) {
								$resultFilter=[
										'filterType' => (isset($value['contactfilterid']))? (int)$value['contactfilterid']:0,
										'filterName' => (isset($value['description']))? $value['description']:'',
										'filterOptionType' => (isset($value['filteroptiontypeid']))? (int)$value['filteroptiontypeid']:0,
										'filterOptions' => [],
										'filterMinValue' 	=> 0,
										'filterMaxValue' 	=> 0
								];
							}
							//Blood group
							if($value['contactfilterid'] == ExtendedContactfilters::CONTACT_FILTER_BLOODGROUP) {
								$bloodGroup = [
										"O -ve"  => "1",
										"O +ve"  => "2",
										"A -ve" => "3" ,
										"A +ve" => "4" ,
										"B -ve" => "5" ,
										"B +ve" => "6" ,
										"AB -ve" => "7" ,
										"AB +ve" => "8"
								];
					

								if($bloodGroup && count($bloodGroup) > 0) {
									foreach ($bloodGroup as $key => $group) {
										$filterOption=[
												'optionId' => (int)$group,
												'optionText' => $key
										];
										array_push($filterOptions,$filterOption);
									}
									
									$resultFilter = [
											'filterType' => (isset($value['contactfilterid']))? (int)$value['contactfilterid']:0,
											'filterName' => (!empty($value['description']))? $value['description']:'',
											'filterOptionType' => (isset($value['filteroptiontypeid']))? (int)$value['filteroptiontypeid']:0,
											'filterOptions' => $filterOptions,
											'filterMinValue' 	=> 0,
											'filterMaxValue' 	=> 0
									];
								}
							}


							$filterMinValue = 0;
							$filterMaxValue = 0;
							if($institutionType == ExtendedInstitution::INSTITUTION_TYPE_EDUCATION && $value['contactfilterid'] == ExtendedInstitutioncontactfilters::CONTACT_FILTER_BATCH) {
								$filterMinValue = BaseModel :: getInstitutionMinBatch($institutionId);
								$filterMaxValue = date("Y");
								$resultFilter = [
									'filterType' => (isset($value['contactfilterid']))? (int)$value['contactfilterid']:0,
									'filterName' => (!empty($value['description']))? $value['description']:'',
									'filterOptionType' => (isset($value['filteroptiontypeid']))? (int)$value['filteroptiontypeid']:0,
									'filterOptions' => $filterOptions,
									'filterMinValue' 	=> $filterMinValue,
									'filterMaxValue' 	=> (int)$filterMaxValue
								];
							}

							array_push($filters,$resultFilter);
						}
							
						
					
					}
					$committeeresponse = BaseModel::getAllCommitteePeriodsAndTypes($institutionId);
					if (! empty($committeeresponse) && $committeeresponse->Status === true) {
					    $grp = $committeeresponse->value->committeeGroupList;
					    $comPrd = $committeeresponse->value->committeePeriodList;
					    foreach ($grp as $eachGroup) {
					        $periods = [];
					        foreach ($comPrd as $eachPeriodList) {
					            if ($eachGroup['committeegroupid'] == $eachPeriodList['committeegroupid']) {
					                $committeperiod = array(
					                    
					                    "periodId" => (int)$eachPeriodList['committee_period_id'],
					                    "startDate" => (string) date(yii::$app->params['dateFormat']['dateOfBrithFormat'], strtotimeNew($eachPeriodList['period_from'])),
					                    "endDate" => (string) date(yii::$app->params['dateFormat']['dateOfBrithFormat'], strtotimeNew($eachPeriodList['period_to']))
					                );
					                array_push($periods, $committeperiod);
					            }
					        }
					        $committeetype = array(
					            "committeeTypeId" => (int)$eachGroup['committeegroupid'],
					            "committeeTypeName" => $eachGroup['description'],
					            "periods" => $periods
					        );
					        array_push($committeeTypes, $committeetype);
					    }
					} else {		    
					    $this->message = "An error occurred while processing the request";
					    $this->statusCode = 500;
					    $this->data = new \stdClass();
					    return new ApiResponse($this->statusCode, $this->data, $this->message);
					}
						$responseInstitution = ExtendedInstitution::getInstitutionFeatures($institutionId);
						$responseInstitutionDetails = BaseModel::getInstitutionFeatureEnabledValue($institutionId);
                        $committee = BaseModel::checkUserAvailableInCommittee($userId, $institutionId);
                        $rolePrivileges = new \stdClass();
						$featuresEnabled = [];
						$responsePrivileges = BaseModel::getAllAppUserPrivileges($memberId,$institutionId, $userType);
						  if (! empty($responseInstitution) && $responseInstitution->Status === true && ! empty($responseInstitution->value) && (!empty($responseInstitutionDetails && $responseInstitution->Status === true))) {
							    foreach ($responseInstitution->value as  $eachValue) {
                                    if(($responseInstitutionDetails->value['prayerrequestenabled'] == 0) && ($eachValue['dashboardid'] == ExtendedDashboard::PRAYERREQUEST)) {   
                                        continue;
                                    } else if(!$committee && $eachValue['dashboardid'] == ExtendedDashboard::CONVERSATION) {
                                        continue;   
                                    } else if(($responseInstitutionDetails->value['feedbackenabled'] == 0 ) && $eachValue['dashboardid'] == ExtendedDashboard::FEEDBACK) { 
                                        continue; 
                                    } else if($responseInstitutionDetails->value['moreenabled'] ==0 && ($eachValue['dashboardid'] == ExtendedDashboard::MORE)) {
                                        continue;
                                    } else {
                                        $_feature = array(
                                            "featureId" => (!empty($eachValue['dashboardid']) ? (int)$eachValue['dashboardid'] : 0)
                                        );
                                        array_push($featuresEnabled, $_feature);
                                    }
								}
									// Near Me
								$_featureNearMe = array(
										"featureId" => 12
								);
								array_push($featuresEnabled, $_featureNearMe);
								// Administaration
								if (!empty($responsePrivileges) && $responsePrivileges->Status === true && $responsePrivileges->value != null && count($responsePrivileges->value) > 0) {
									$rolePrivileges = $responsePrivileges->value;
									if (array_key_exists(ExtendedPrivilege::MANAGE_PRAYER_REQUESTS, $rolePrivileges) || array_key_exists(ExtendedPrivilege::MANAGE_EVENT_RSVP, $rolePrivileges) || array_key_exists(ExtendedPrivilege::APPROVE_PENDING_MEMBER, $rolePrivileges) || array_key_exists(ExtendedPrivilege::MANAGE_FEEDBACKS, $rolePrivileges) || array_key_exists(ExtendedPrivilege::MANAGE_ALBUM, $rolePrivileges) || array_key_exists(ExtendedPrivilege::MANAGE_FOOD_ORDERS, $rolePrivileges)) {
										$_featureSettings = array(
												"featureId" => 14
										);
										array_push($featuresEnabled, $_featureSettings);
									}
								}
						
							$institutionsData = ExtendedInstitution::getUserInstitutions($userId);
							if($institutionsData) {
								foreach ($institutionsData as $value) {
									$result = [
											'institutionId' => (!empty($value['id'])) ? (string)$value['id'] : '',
											'institutionName' => (!empty($value['name'])) ? $value['name']:'',
									];
									array_push($institutions, $result);
								}
							}
							
						}
						
						$institutionFeatureSettings = ExtendedInstitution::getInstitutionFeatureDetails($institutionId);
						if($institutionFeatureSettings) {
							if($institutionFeatureSettings['feedbackenabled'] == 1) {
								$feedbackResponse = BaseModel::getFeedbackTypesByUnderInstitution($institutionId, true);
								if (! empty($feedbackResponse) && $feedbackResponse->Status === true && count($feedbackResponse->value) > 0) {
									foreach ($feedbackResponse->value as $feedbackRes) {
										$_feedbackTypes = array(
												"type" => $feedbackRes['description'],
												"typeId" => $feedbackRes['feedbacktypeid']
									
										);
										array_push($feedbackTypes, $_feedbackTypes);
									}
								} else {
									$_feedbackTypes = array(
											"type" => RememberAppConstModel::DEFAULT_FEEDBACK_TYPE,
											"typeId" => RememberAppConstModel::DEFAULT_FEEDBACK_TYPE_ID
									);
									array_push($feedbackTypes, $_feedbackTypes);
								}
							}
							if($institutionFeatureSettings != '' && $institutionFeatureSettings['feedbackenabled'] !== ''
									&& $institutionFeatureSettings['paymentoptionenabled'] != '') {
								$ob = new \stdClass();
								foreach ($institutionFeatureSettings as $key => $value) {
									$featureData = new \stdClass();
									$featureData->feedbackFeatureAvailable = (!empty($value['feedbackenabled'])) ? (bool)$value['feedbackenabled'] : false;
									$featureData->paymentFeatureAvailable = (!empty($value['paymentoptionenabled'])) ? (bool)$value['paymentoptionenabled'] : false;
									$featureData->isDemoEnabled = false;
								}
							}
						}
						
						$responseAddressType = BaseModel::getAddressType();
						if ((! empty($responseAddressType) && $responseAddressType->Status === true) && ! empty($responseAddressType->value)) {
							foreach ($responseAddressType->value as $addresstype) {
								$_communicationAddresses = [];
								$_communicationAddresses['addressTypeId'] = (string) $addresstype['id'];
								$_communicationAddresses['addressDescription'] = (string) $addresstype['type'];
								array_push($communicationAddresses, $_communicationAddresses);
							}
						}
						
						// privileges
						$accessPrivilege = [];
						if ($responsePrivileges != null && $responsePrivileges->Status === true && $responsePrivileges->value != null && count($responsePrivileges->value) > 0) {
							foreach ($responsePrivileges->value as $eachPrivilege) {
								if(in_array($eachPrivilege->name,yii::$app->params['mobileAccessPrivileges'])){
									$_privilege = array(
										"privilegeId" => $eachPrivilege->name,
										"privilegeName" => $eachPrivilege->description
									);
									array_push($accessPrivilege, $_privilege);
								}
							}
						}
						//Staffs
						$staffResponse = ExtendedMember::getStaffs($institutionId);
						if($staffResponse) {
							foreach ($staffResponse as $value) {
								$staff[] = [
										'staffId' => (!empty($value['memberid'])) ? (int)$value['memberid'] :0,
										'title' => (!empty($value['description'])) ? $value['description'] : '',
										'name' => (!empty($value['firstName'])) ? $value['firstName'] : '',
										'thumbnail' => (!empty($value['memberImageThumbnail'])) ? (string)yii::$app->params['imagePath'].$value['memberImageThumbnail'] : '',
										'designation' => (!empty($value['designation'])) ? $value['designation'] : '',
										'mobileNumber' => (!empty($value['member_mobile1'])) ? $value['member_mobile1'] : '',
										'mobileNumberCountryCode' => (!empty($value['member_mobile1_countrycode'])) ? $value['member_mobile1_countrycode'] : '',
										'email' => (!empty($value['member_email'])) ? $value['member_email'] : '',
								];
								array_push($stafflist,$staff);
							}
						}
						
						$institutionResponse = ExtendedInstitution::getInstitution($institutionId);
						if($institutionResponse) {
							if($institutionResponse['institutiontypeid'] == 2) {
								$familyUnitmodel = new ExtendedFamilyunit();
								$familyUnitList = $familyUnitmodel->getActiveFamilyUnits($institutionId);
								if($familyUnitList && count($familyUnitList) > 0) {
									foreach ($familyUnitList as $key => $value) {
										$familyUnit[] = [
												'familyUnit' => (!empty($value['description'])) ? $value['description'] :'', 
												'familyUnitId' => (!empty($value['familyunitid'])) ? (int)$value['familyunitid'] : 0,
										];
									}
								}
								
							}
							//institution details
							$institutionDetails = ExtendedInstitution::changeInstitution($userId, $institutionId);
							if($institutionDetails) {
								$ob->memberId = (!empty($institutionDetails['memberid'])) ? $institutionDetails['memberid'] : '';
								$ob->userGroup = ($institutionDetails['membertype'] == 0) ? ($institutionDetails['usertype'] == 'M' ? 0 : 1 ): 2;
								$ob->memberName = (!empty($institutionDetails['username'])) ? $institutionDetails['username'] : '';
								$ob->institutionId = (!empty($institutionDetails['institutionid'])) ? (string)$institutionDetails['institutionid'] : '';
								$ob->institutionName = (!empty($institutionResponse['name'])) ? $institutionResponse['name'] : '';
								$ob->institutionLogo = (!empty($institutionResponse['institutionlogo'])) ? yii::$app->params['imagePath'].$institutionResponse['institutionlogo'] : '';
								$ob->institutionType = (!empty($institutionResponse['institutiontypeid'])) ? (int)$institutionResponse['institutiontypeid'] : 0;
								$ob->isAdsEnabled = true;
								//family units
								$ob->familyUnits = $familyUnit;
								$ob->moreInfoUrl = (!empty($institutionResponse['moreurl'])) ? $institutionResponse['moreurl'] : '';
								$ob->isContactFilterEnabled = (!empty($institutionFeatureSettings['advancedsearchenabled'])) ? (bool)$institutionResponse['advancedsearchenabled'] : false;
								$ob->isTagsEnabled = (!empty($institutionFeatureSettings['tagcloud'])) ? (bool)$institutionResponse['tagcloud'] : false;
								$ob->phoneNumber1 = (!empty($institutionResponse['phone1'])) ? $institutionResponse['phone1'] : '';
								$ob->website = (!empty($institutionResponse['url'])) ? $institutionResponse['url'] : '';
								$ob->phoneNumber2 = (!empty($institutionResponse['phone2'])) ? $institutionResponse['phone2'] : '';
								$ob->email = (!empty($institutionResponse['email'])) ? $institutionResponse['email'] : '';
								//address
								$ob->address = $addressObj;
								$ob->communicationAddressId = (!empty($institutionDetails['addresstypeid'])) ? (string)$institutionDetails['addresstypeid'] : '';
								$ob->pushNotificationsEnabled = (!empty($institutionDetails['membernotification'])) ? (bool)$institutionDetails['membernotification'] : false;
								$ob->birthdayNotificationsEnabled = (!empty($institutionDetails['birthday'])) ? (bool)$institutionDetails['birthday'] : false;
								$ob->anniversaryNotificationsEnabled = (!empty($institutionDetails['anniversary'])) ? (bool)$institutionDetails['anniversary'] : false;
								$ob->smsNotificationsEnabled = (!empty($institutionDetails['membersms'])) ? (bool)$institutionDetails['membersms'] : false;
								$ob->emailNotificationsEnabled = (!empty($institutionDetails['memberemail'])) ? (bool)$institutionDetails['memberemail'] : false;
								$ob->institutions = $institutions;
								$ob->committeeTypes = $committeeTypes;
								//institutionFeatures
								$ob->institutionFeatures = $featureData;
								$ob->institutionFeatures->feedbackTypes = $feedbackTypes;
								$ob->institutionFeatures->featuresEnabled = $featuresEnabled;
								$ob->institutionFeatures->communicationAddresses = $communicationAddresses;
								//access privilege
								$ob->accessPrivilege = $accessPrivilege;
								//staff list
								$ob->staffs = $stafflist;
								//filter options
								$ob->filters = $filters;
								
							}
						}
						$this->statusCode = 200;
						$this->message = '';
						$this->data = $ob;
						return new ApiResponse($this->statusCode, $this->data,$this->message);
				}else{

					$this->statusCode = 200;
					$this->message = '';
					$this->data = new \stdClass();
					return new ApiResponse($this->statusCode, $this->data,$this->message);
				}
			}else{
				$this->statusCode = 498;
				$this->message = 'Session invalid';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
				
			}
		}else{
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}

	/**
	 * Retrieve the list of committee members of an institution.
	 * @param $institutionId int
	 * @return $statusCode int
	 * @return $data array
	 * @return $message string
	*/
	public function actionGetCommitteeMembersForSync()
	{
		$request = Yii::$app->request;
    	$userId = Yii::$app->user->identity->id;
    	$institutionId = $request->get('institutionId');
    	$currentDate = gmdate("Y-m-d H:i:s");
    	$type = [];
    	$record = [];
    	$periods = [];
    	$membersList = [];
    	$periodIdList = [];
    	$data = new \stdClass();
    	$committeeTypeIdList = [];
    	
    	try{
    		if($userId){
    			$committeeMembers = ExtendedCommittee::getCommitteeMembers($institutionId, $currentDate);
    			if ($committeeMembers) {
    				$result = $this->_group_by($committeeMembers, 'committeegroupid');
    				//Committee data 
    				foreach($result as $item) {
    					$committeeTypeId = '';
    					$committeeDescription = '';
    					$element = $this->_group_by($item, 'committeeperiodid');
    					foreach ($element as $row) {
    						$periodId = '';
    						$startDate = '';
    						$endDate = '';
    						$membersList = [];
    						foreach ($row as $temp) {
    							if (empty($committeeTypeId)) {
	    							$committeeTypeId = (int)$temp['committeegroupid'];
	    							$committeeDescription = $temp['committeegroupdescription'];
	    						}

	    						if(empty($periodId)) {
	    							$periodId =$temp['committeeperiodid'];
	    							$startDate = $temp['startdate'];
	    							$endDate = $temp['enddate'];
	    						}
	    						$members = [
	    							'memberId' => (!empty($temp['memberid'])) ? (int)$temp['memberid'] : 0 ,
	    							'title' => (!empty($temp['title'])) ? $temp['title'] : '',
	    							'name' => (!empty($temp['membername'])) ? $temp['membername'] : '',
	    							'userThumbnailImage' => (!empty($temp['memberimage'])) ? Yii::$app->params['imagePath'].$temp['memberimage'] : '',
	    							'contactNumber' => (!empty($temp['memberphone'])) ? $temp['memberphone'] : 0,
	    							'email' => (!empty($temp['memberemail'])) ? $temp['memberemail'] : '',
	    							'position' => (!empty($temp['description'])) ? $temp['description'] : '',
	    							'isSpouse' => (!empty($temp['isspouse'])) ? (bool)$temp['isspouse'] : false,
	    						];
	    						$membersList[] = $members;
    						}
    						if (!in_array($periodId, $periodIdList)) {
	    						$periods['periodId'] = (int)$periodId;
		    					$periods['startDate'] = date_format(date_create(
										$startDate),Yii::$app->params['dateFormat']['viewDateFormat']);
		    					$periods['endDate'] = date_format(date_create(
										$endDate),Yii::$app->params['dateFormat']['viewDateFormat']);
		    					$periodIdList[] = $periodId;
    						}
    						$periods['committeeMembers'] = $membersList;
    						$type['periods'][] = $periods;
    					}
    					$type['committeeTypeId'] = $committeeTypeId;
    					$type['committeeTypeName'] = $committeeDescription;
    					if(!in_array($committeeTypeId, $committeeTypeIdList)){
    						$record[] = $type;
    						$committeeTypeIdList[] = $committeeTypeId;
    					}
    				}
				}
				$data->committeeTypes = $record;
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
        } catch(Exception $e) {
        	yii::error($e->getMessage());
        	$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
        }
    }

    /**
     *Return record grouped by specified key.
     *@param $key
     *@param @array
    */
    protected function _group_by($array, $key)
    {
        $return = array();
        foreach($array as $val) {
            $return[$val[$key]][] = $val;
        }
        return $return;
    } 
    /**
     * To retrieve all the bills 
     * of a member in an institution
     */
    public function actionGetBillsForSync()
    {
    	$request = Yii::$app->request;
    	$institutionId = $request->get('institutionId');
    	if($institutionId) {
    		$institutionId = filter_var($institutionId, FILTER_SANITIZE_NUMBER_INT);
    		$obj = new \stdClass();
    		$currentMonth = date('m');
    		$currentYear = date('y');
    		$userType = Yii::$app->user->identity->usertype;
    		$userId = Yii::$app->user->identity->id;
    		$memberData = ExtendedMember::getMemberId($userId,$institutionId,$userType);
    		$memberId = $memberData['memberid'];
    		$currentDate = date('Y-m-d H:i:s');
    		$response = ExtendedBills::getBillsForSync($currentDate, $institutionId, $memberId);
    		$memberEmail = ExtendedUserMember::getMemberEmail($institutionId, $userId, $userType);
    		$memberEmail = $memberEmail['email'];
    		$billListArray = [];
    		$finalData = [];
    		if($response) {
    			ArrayHelper::multisort($response, ['month', 'year'], [SORT_ASC, SORT_ASC]);
    			$normalBillList = [];
	    		$openingBalanceList = [];
	    		$normalBillList = array_filter($response,array($this, 'normalBill'));
	    		$openingBalanceList = array_filter($response,array($this, 'openingBill'));
	    		$openFlag = 1;
	    		$normalFlag = 1;
    			
    			foreach($response as $bill) {

    				if (($currentMonth != $bill['month']) && ($currentYear != $bill['year'])) {
    					$openFlag = 1;
    					$normalFlag = 1;
    				}
    				$currentMonth = $bill['month'];
    				$currentYear = $bill['year'];
    				$openingDebitAmount = 0;
	    			$openingCreditAmount = 0;
	    		    $openingBalance = 0.0;
	    		    $openingBalanceType = "--";

    				$billInfoResponse = ExtendedBills::getBillsInfoForSync($currentMonth, $currentYear, $memberId, $institutionId);
            		
            		$billListArray[$bill['year']][$bill['month']]['month'] = (string)$currentMonth;
            		$billListArray[$bill['year']][$bill['month']]['year'] = (string)$currentYear;
            		
            		if (count($openingBalanceList) > 0 && $bill['type'] == 1 ) {
            				$openFlag++;
	            			$openingDebitAmount = (string)$bill['debit'];
		    				$openingCreditAmount = (string)$bill['credit'];
		    				$openingBalanceType = ($openingDebitAmount == null || $openingDebitAmount == 0.0 || $openingDebitAmount == 0 ||
		    									  $openingDebitAmount == '') ? "Cr" : "Dr";
		    				$openingBalance = ($openingDebitAmount == null || $openingDebitAmount == 0.0 || $openingDebitAmount == 0 ||
		    								  $openingDebitAmount == '') ? (string)$openingCreditAmount : (string)$openingDebitAmount;
	            			$billListArray[$bill['year']][$bill['month']]['openingBalance'] = (!empty($openingBalance))? $openingBalance : 0;
	    					$billListArray[$bill['year']][$bill['month']]['openingBalanceType'] = $openingBalanceType;
	    					$billListArray[$bill['year']][$bill['month']]['closingBalance'] = (!empty($closingBalance))? $closingBalance : 0;
            		} else {
            			if ($openFlag == 1) {
            				$openFlag++;
							$billListArray[$bill['year']][$bill['month']]['openingBalance'] = (!empty($openingBalance))? $openingBalance : 0;
	    					$billListArray[$bill['year']][$bill['month']]['openingBalanceType'] = $openingBalanceType;
	    					$billListArray[$bill['year']][$bill['month']]['closingBalance'] = (!empty($closingBalance))? $closingBalance : 0;
            			}
            		}
    				$billListArray[$bill['year']][$bill['month']]['isPreviousMonthDataAvailable'] = ($billInfoResponse['@previousmonth'] > 0 &&
    						$billInfoResponse['@previousyear'] > 0) ? true : false ;
    				$billListArray[$bill['year']][$bill['month']]['isNextMonthDataAvailable'] = ($billInfoResponse['@nextmonth'] > 0 &&
    						$billInfoResponse['@nextyear'] > 0) ? true : false ;
    				$billListArray[$bill['year']][$bill['month']]['memberEmail'] = $memberEmail;
   					
   					if (count($normalBillList) > 0 && $bill['type'] != 1 ) {
   							$credit = (float)$bill['credit'];
   							$debit = (float)$bill['debit'];
   							$normalFlag++;
							$bill['transactiondate'] = date('Y-m-d', strtotimeNew($bill['transactiondate']));
							$billListArray[$bill['year']][$bill['month']]['bills'][$bill['transactiondate']]['transactionDate'] = $bill['transactiondate'];
	   						$billListArray[$bill['year']][$bill['month']]['bills'][$bill['transactiondate']]['transactions'][] = [
									'transactionId' => 0,
			    					'transactionType' => ($debit) ? "Dr" : "Cr",
			    					'transactionDetails' => (!empty($bill['description'])) ? $bill['description'] : '',
			    					'transactionAmount' => ($credit) ? (string)$credit : (string)$debit ,
							];
							$billListArray[$bill['year']][$bill['month']]['bills'] = array_values($billListArray[$bill['year']][$bill['month']]['bills']);
   					} else {
   						if ($normalFlag == 1) {
   							$normalFlag++;
							$billListArray[$bill['year']][$bill['month']]['bills'] = [];
   						}
   					} 
        		}
    			$obj->institutionId = $institutionId;
    			$bills = array_map('array_values',$billListArray);
    			if (!empty($bills)) {
    				foreach ($bills as $key => $value) {
    					$finalData = $value;
    				}
    			}
    			$obj->billList = $finalData;

    			$this->statusCode = 200;
    			$this->message = '';
    			$this->data = $obj;
    			return new ApiResponse($this->statusCode,$this->data,$this->message);
    		} else {
    			$this->statusCode = 200;
    			$this->message = '';
    			$this->data = $obj;
    			return new ApiResponse($this->statusCode, $this->data,$this->message);
    		}
    	} else {
    		$this->statusCode = 500;
    		$this->message = 'An error occurred while processing the request';
    		$this->data = new \stdClass();
    		return new ApiResponse($this->statusCode,$this->data,$this->message);
    	}
    }

    /**
     * Setting array for normal bill
     */
    public function normalBill($data)
    {
    	if($data['type'] != 1) {
    		return $data;
    	}
    }
    /**
     * setting array for opening bill
     */
    public function openingBill($data)
    {
    	if($data['type'] == 1) {
    		return $data;
    	}
    }

	/**
     * To set the data
     * for response
     */
    protected function setData($currentMonth,$currentYear,$openingBalance,
    		$openingBalanceType,$closingBalance,$billInfoResponse2,$memberEmail,$billDatas)
    {
    	try {
    		$billlist = [
    				'month' => (string)$currentMonth,
    				'year' => (string)$currentYear,
    				'openingBalance' => (!empty($openingBalance))? $openingBalance : 0,
    				'openingBalanceType' => $openingBalanceType,
    				'closingBalance' => (!empty($closingBalance))? $closingBalance : 0,
    				'isPreviousMonthDataAvailable' => ($billInfoResponse2['@previousmonth'] > 0 &&
    						$billInfoResponse2['@previousyear'] > 0) ? true : false ,
    				'isNextMonthDataAvailable' => ($billInfoResponse2['@nextmonth'] > 0 &&
    						$billInfoResponse2['@nextyear'] > 0) ? true : false ,
    				'memberEmail' => $memberEmail,
    				'bills' => $billDatas,
    		];
    		
    		return $billlist;
    
    	} catch (Exception $e) {
    		return false;
    	}
    
    }

    /**
	 * Retrieving the details of a particular member
	 * @param $lastUpdatedOn datetime
	 * @return $statusCode int
	 * @return $data array
	 * @return $message string
	*/
	/*
	public function action_GetMemberDetailsFileForSync()
	{

		date_default_timezone_set('Asia/Kolkata');
		$request = Yii::$app->request;
		$user = Yii::$app->user->identity;
		$institution = $user->institution;
    	$userId = $user->id;
    	$lastUpdatedOn = $request->get('lastUpdatedOn');
    	$institutionId = $user->institutionid;
    	$institutionType = $institution->institutiontype;
    	$tagSearch = $institution->advancedsearchenabled;
		$userType = $user->usertype;
        $memberDetails =  ExtendedMember::getMemberId($userId, $institutionId, $userType);;
        $memberId = $memberDetails['memberid'];
    	$lastUpdatedUTC = ($lastUpdatedOn) ? date('Y-m-d H:i:s', strtotimeNew($lastUpdatedOn)) : "";
    	$currentDate =  date("Y-m-d H:i:s");
  		$dependantCount = 0;
  		$dependantsList = [];
  		$institutionList = [];
  		$removedContacts = [];
  		$data = new \stdClass();
  		$committeDesignation = [];
  		$institutionDetailsList = [];
  		$finalMemberDetailsList = [];
  		$associatedInstitutions = [];
  		$dependentObject = new ExtendedDependant();
    	try{
    		if ($userId) {
    			$memberDetails = ExtendedMember::getMemberDetailsFileForSync($userId, $lastUpdatedOn);
    			if ($memberDetails) {
    				if (is_array($memberDetails)) {
    					$institutionIds = ArrayHelper::getColumn($memberDetails, function ($element) {
						    return $element['institutionid'];
						});
						foreach($institutionIds as $institutionId) {
							if(in_array($institutionId, $institutionList)) {
								continue;
							} else {
								$institutionList[] = $institutionId;
							}
							//Member details
							$result = ArrayHelper::index($memberDetails, null, 'institutionid');
							$result = $result[$institutionId];
							foreach ($result as $item) {
								$membershipDetails = new \stdClass();
								$membershipDetails->membershipId = (!empty($item['memberno']) ? $item['memberno'] : '');
								$membershipDetails->membershipType = (!empty($item['membershiptype']) ? $item['membershiptype'] : '');
								$membershipDetails->memberSince = (!empty($item['membersince']) ? $item['membersince'] : '');

								//Get member settings
								$settingsDetails = ExtendedSettings::getMemberSettings($item['memberid']);
 								if (!$settingsDetails) {
 									yii::error('Settings Not found for member'.$item['memberid']);
 									$this->statusCode = 500;
 									$this->message = 'An error occurred while processing the request';
 									$this->data = new \stdClass();
 									return new ApiResponse($this->statusCode,$this->data,$this->message);
 								}

								$memberMobileCountryCode = (!empty($item['member_mobile1_countrycode']) ? $item['member_mobile1_countrycode'] : '');
                                $memberMobileNumber = (!empty($item['member_mobile1']) ? $item['member_mobile1'] : '');
                                if (!empty(trim($item['spouse_firstName']))) {
                                    $spouseMobileCountryCode = (!empty($item['spouse_mobile1_countrycode'])) ? $item['spouse_mobile1_countrycode'] : '';
                                    $spousemobileNumber = (!empty($item['spouse_mobile1']) ? $item['spouse_mobile1']:'');
                                } else {
                                	$spouseMobileCountryCode = '';
                                	$spousemobileNumber = '';
                                }
                                
                                //GetAssosiatedInstitutions
                                $institutionResult = $this->getAssosiatedInstitutions($memberId, $item['memberid'], ExtendedMember::USER_TYPE_MEMBER);
                                if ($institutionResult) {
                                	if (is_array($institutionResult)) {
	                                	foreach($institutionResult as $result) {
	                                		$associatedInstitutions[] = $result['institutionname'];
	                                	}
	                                } else {
	                                	$institutionResponse = ExtendedInstitution:: getInstitutionData($institutionId);
	                                	if ($institutionResponse) {
	                                		$associatedInstitutions[] = $institutionResponse[0]['name'];
	                                	} else {
	                                		$this->statusCode = 500;
											$this->message = 'An error occurred while processing the request';
											$this->data = new \stdClass();
											return new ApiResponse($this->statusCode,$this->data,$this->message);
	                                	}
	                                }
                                } else {
                                	yii::error('Error while processing institution result');
                                	$this->statusCode = 500;
									$this->message = 'An error occurred while processing the request';
									$this->data = new \stdClass();
									return new ApiResponse($this->statusCode,$this->data,$this->message);
                                }

                                //Get Committee designations
                                $committeeResponse = ExtendedCommittee::getCommitteeDesignations($item['memberid'], ExtendedMember::USER_TYPE_MEMBER, true);
                                if ($committeeResponse) {
                                	if (is_array($committeeResponse)) {
                                		foreach($committeeResponse as $result) {
                                			$committeDesignation[] = [
                                				'committeeName' => $result['committeetype'],
                                				'designation' => $result['designation']

                                			];
                                		}
                                	}
                                } else {
                                	yii::error('Error while processing committeeresponse');
                                	$this->statusCode = 500;
									$this->message = 'An error occurred while processing the request';
									$this->data = new \stdClass();
									return new ApiResponse($this->statusCode,$this->data,$this->message);
                                }

                                //Set member details
                                $memberListDetails = $this->setMemberDetails($item, $associatedInstitutions, $committeDesignation, $memberMobileCountryCode, $memberMobileNumber,
                                	$spouseMobileCountryCode,$spousemobileNumber);

                                //Spouse
                                //GetAssosiatedInstitutions
                                $associatedInstitutions = [];
                                $committeDesignation = [];

                                $institutionResult = $this->getAssosiatedInstitutions($memberId, $item['memberid'], ExtendedMember::USER_TYPE_SPOUSE);
                                if($institutionResult) {
                                	if(is_array($institutionResult)) {
	                                	foreach($institutionResult as $result) {
	                                		$associatedInstitutions[] = $result['institutionname'];
	                                	}
	                                } else {
	                                	$institutionResponse = ExtendedInstitution:: getInstitutionData($institutionId);
	                                	if($institutionResponse){
	                                		$associatedInstitutions[] = $institutionResponse[0]['name'];
	                                	} else {
	                                		yii::error('Error while processing institution response spouse');
	                                		$this->statusCode = 500;
											$this->message = 'An error occurred while processing the request';
											$this->data = new \stdClass();
											return new ApiResponse($this->statusCode,$this->data,$this->message);
	                                	}
	                                }
                                } else {
                                	yii::error('Error while processing institution response spouse');
                                	$this->statusCode = 500;
									$this->message = 'An error occurred while processing the request';
									$this->data = new \stdClass();
									return new ApiResponse($this->statusCode,$this->data,$this->message);
                                }

                                //Get Committee designations
                                $committeeResponse = ExtendedCommittee::getCommitteeDesignations($item['memberid'], ExtendedMember::USER_TYPE_SPOUSE, true);
                                if($committeeResponse) {
                                	if(is_array($committeeResponse)) {
                                		foreach($committeeResponse as $result) {
                                			$committeDesignation[] = [
                                				'committeeName' => $result['committeetype'],
                                				'designation' => $result['designation']

                                			];
                                		}
                                	}
                                } else {
                                	yii::error('Error while processing committee Response  spouse');
                                	$this->statusCode = 500;
									$this->message = 'An error occurred while processing the request';
									$this->data = new \stdClass();
									return new ApiResponse($this->statusCode,$this->data,$this->message);
                                }

                                //Set member details
                                $spouseListDetails = $this->setSpouseDetails($item, $associatedInstitutions, $committeDesignation, $memberMobileCountryCode, $memberMobileNumber,
                                	$spouseMobileCountryCode,$spousemobileNumber);

                                //Residential Address
                                $residentialAddress = [
                                	'addressLine1' => (!empty($item['residence_address1'])? $item['residence_address1'] : ''),
                                    'addressLine2' => (!empty($item['residence_address2'])? $item['residence_address2']:''),
                                    'city' => $item['residence_district'],
                                    'state' => (!empty($item['residence_state']) ? $item['residence_state'] : ''),
                                    'pinCode' => (!empty($item['residence_pincode']) ? $item['residence_pincode'] : ''),

                                ];
                                //print_r($residentialAddress);die;
                                $officePhone = [
                                	'countryCode' => (!empty($item['member_business_phone1_countrycode']) ? $item['member_business_phone1_countrycode'] : ''),
                                    'areaCode' => (!empty($item['member_business_phone1_areacode']) ? $item['member_business_phone1_areacode'] : ''),
                                    'phoneNumber' => (!empty($item['member_musiness_Phone1']) ? $item['member_musiness_Phone1'] : ''),
                               ];

                                $alternativeOfficePhone = [
                                    'countryCode' =>(!empty($item['member_business_phone3_countrycode']) ? $item['member_business_phone3_countrycode'] : ''),
                                    'areaCode' => (!empty($item['member_business_phone3_areacode']) ? $item['member_business_phone3_areacode'] :  ''), 
                                    'phoneNumber' => (!empty($item['member_business_Phone3']) ? $item['member_business_Phone3'] : '') 
                                ];

                                $businessDetails = [
                                	'alternativeOfficePhone' => $alternativeOfficePhone,
                                	'alternativeOfficePhone' => $alternativeOfficePhone,
                                    'officePhone' => $officePhone,
                                    'email' => (!empty($item['businessemail']) ? $item['businessemail'] :  ''),
                                    'addressLine1' => (!empty($item['business_address1']) ? $item['business_address1'] :  ''),
                                    'addressLine2' => (!empty($item['business_address2']) ? $item['business_address2'] :  ''),
                                    'city' => (!empty($item['business_district']) ? $item['business_district'] :  ''),
                                    'state' => (!empty($item['business_state']) ? $item['business_state'] :  ''),
                                    'pinCode' => (!empty($item['business_pincode']) ? $item['business_pincode'] :  ''),
                                ];

                                $dependants = $dependentObject->getDependants($item['memberid'], true);

                                if ($dependants) {
                                	if(is_array($dependants)) {
                                		$dependantCount = count($dependants);
	                                	foreach($dependants as $result) {
	                                		$dependantsResult = $this->setDependantsDetails($result);
	                                		$dependantsList[] = $dependantsResult;   
	                                	}
	                                }
                                } else {
                                	yii::error('Error while processing dependants');
                                	$this->statusCode = 500;
									$this->message = 'An error occurred while processing the request';
									$this->data = new \stdClass();
									return new ApiResponse($this->statusCode,$this->data,$this->message);
                                }
                                
                                $hasBusinessDetails = (!empty($item['member_musiness_Phone1'])) || 
                                	(!empty($item['member_musiness_Phone2'])) ||
                                	(!empty($item['businessemail'])) || 
                                	(!empty($item['business_address1'])) || 
                                	(!empty($item['business_address2'])) || 
                                	(!empty($item['business_district'])) || 
                                	(!empty($item['business_state'])) || 
                                	(!empty($item['business_state'])) ? true : false;

                                $finalMemberDetails = [
                                	'memberId' => $item['memberid'],
                                	'membershipDetails' => $membershipDetails,
                                	'memberDetails' => $memberListDetails,
                                	'spouseDetails' => $spouseListDetails,
                                	'hasResidentialAddress' => (!empty($item['residence_address1']) ? true : false),
                                	'residentialAddress' => $residentialAddress,
                                	'hasBusinessDetails' => $hasBusinessDetails,
                                	'businessDetails' => $businessDetails,
                                	'hasDependants' => $dependantCount > 0 ? true : false,
                                	'dependants' => $dependantsList
                                ];
                                $finalMemberDetailsList[] = $finalMemberDetails; 	
							}

							//Institution
                           	$institutionResult = [
                           		'institutionId' => $institutionId,
                           		'institutionName' => $institutionId,
                           		'members' => $finalMemberDetailsList,
                           	];
                           	$institutionDetailsList[] = $institutionResult;
						}
    				}
    				$deletedContacts = ExtendedMember::getDeletedContactsForSync($userId, $lastUpdatedUTC);
    				if ($deletedContacts) {
    					if (is_array($deletedContacts)) {
	    					foreach($deletedContacts as $contact) {
	    						$removedContacts[] = [
	    							'memberId' => $contact['memberid']
	    						];
	    					}
	    				}
    				} else {
    					yii::error('Error while processing deleted dependants');
    					$this->statusCode = 500;
						$this->message = 'An error occurred while processing the request';
						$this->data = new \stdClass();
						return new ApiResponse($this->statusCode,$this->data,$this->message);
    				}
    				$contacts = [
						'updatedOn' => date( "Y-m-d H:i:s", strtotimeNew($lastUpdatedOn.' -1 minute')),
						'institutions' => $institutionDetailsList,
						'removedContacts' => $removedContacts
	    			];
    				//Write to a file
    				$contactData = new \stdClass();
    				$contactData->contacts = $contacts;
    				$filePath = Yii::getAlias('@service').'/'.Yii::$app->params['contacts']['contactPath'].'/'.$userId.'.txt';
    				$status = FileuploadComponent::uploadContactsFile($contactData, $filePath);
    				if ($status) {
    					$fileName = Yii::$app->params['imagePath'].'/'.Yii::$app->params['contacts']['contactPath'].'/'.$userId.'.txt';
    					$data->fileUrl = (string)$fileName;
    					$this->data = $data;
						$this->statusCode = 200;
						$this->message = '';//$contacts;
						return new ApiResponse($this->statusCode, $this->data,$this->message);
    				} else {
    					yii::error('File upload failed');
    					$this->statusCode = 500;
						$this->message = 'An error occurred while processing the request';
						$this->data = new \stdClass();
						return new ApiResponse($this->statusCode,$this->data,$this->message);
    				}
				} else {
					yii::error('Member details fetching failed');
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
    	} catch(Exception $e){
    		yii::error($e->getMessage());
        	$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
    	}
    }*/
    /**
	 * Get assosiated institutions
	 * @param $lastUpdatedOn datetime
	 * @return $statusCode int
	 * @return $data array
	 * @return $message string
	*/
    protected function getAssosiatedInstitutions($currentMemberId, $memberId, $userType)
    {
    	$user = Yii::$app->user->identity;
    	$currentUserId = $user->id;
    	$userType = $user->usertype;
    	$institutionId = $user->institutionid;
    	$responseInstitution = [];
    	try{
    		$userId = ExtendedUserMember::getUserIdFromUserMember($memberId ,$userType)['userid'];
    		if($currentUserId > 0 && (int)$userId > 0){
    			$responseInstitution = ExtendedInstitution::getAssociatedInstitutionDetails($currentUserId, $userId);
    		} else if($currentUserId > 0 && $userId == 0){
    			$institution = ExtendedInstitution::getInstitutionDetails($institutionId);
    			$responseInstitution[0]['institutionid'] = $institutionId;
    			$responseInstitution[0]['institutionname'] = $institution['name'];
    		} else {
    			return false;
    		}
    		if(!empty($responseInstitution)){
    			return $responseInstitution;
    		} else {
    			return true;
    		}
    	}
    	catch(Exception $e){
    		yii::error($e->getMessage());
        	$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return false;
    	}
    }

    /**
	 * Set Member details
	 * @param $member array
	 * @param $associatedInstitutions array
	 * @param $committeDesignation array
	 * @param $memberMobileCountryCode string
	 * @param $spouseMobileCountryCode string
	 * @param $spouseMobileCountryCode string
	 * @param $spousemobileNumber string
	 * @return boolean value
	*/
    protected function setMemberDetails($member, $associatedInstitutions, $committeDesignation, $memberMobileCountryCode, $memberMobileNumber, $spouseMobileCountryCode, $spousemobileNumber){
    	try{
    		$institutionTypeId = Yii::$app->user->identity->institution->institutiontype;
    		if(!empty($member['middleName'])) {
    			$fullName = trim($member['firstName']). ' '.$member['middleName'].' '. trim($member['lastName']);
    		} else {
    			$fullName = trim($member['firstName']).' '.trim($member['lastName']);
    		}
    		
    		$memberDetails = [
    			'memberTitleId' => !empty($member['membertitle']) ? $member['membertitle'] : 0,
                'memberTitle' => !empty($member['membertitledescription']) ? $member['membertitledescription'] : '',
                'memberName' =>  $fullName,
                'memberFirstName' => trim($member['firstName']),
                'memberMiddleName' => (!empty($member['middleName']) ? $member['middleName'] : ''),
                'memberLastName' => trim($member['firstName']),
                'memberNickName' => (!empty($member['membernickname']) ? $member['membernickname'] : ''),
                'memberImage' => (!empty($member['member_pic']) ? Yii::$app->params['imagePath'].$member['member_pic'] : ''),
                'memberImageThumbnail' => (!empty($member['memberImageThumbnail']) ? Yii::$app->params['imagePath'].$member['memberImageThumbnail'] : ''),
                'memberDob' => (!empty($member['member_dob']) ? date_format(date_create($member['member_dob']),Yii::$app->params['dateFormat']['viewDateFormat']) : ''),
                'mobileCountryCode' => (!empty($memberMobileCountryCode) ? $memberMobileCountryCode : ''),
                'mobileNumber' => (!empty($memberMobileNumber) ? $memberMobileNumber : ''),
                'landLineCountryCode' => (!empty($member['member_residence_phone1_countrycode']) ? $member['member_residence_phone1_countrycode'] : ''),
                'landLineAreaCode' => (!empty($member['member_residence_Phone1_areacode']) ? $member['member_residence_Phone1_areacode'] : ''),
                'landLineNumber' => (!empty($member['member_residence_Phone1']) ? $member['member_residence_Phone1'] : ''),
                'email' => (!empty($member['member_email']) ? $member['member_email'] : ''),
                'profession' => (!empty($member['occupation']) ? $member['occupation']:''),
                'isChurch' => $institutionTypeId == 2 ? true : false,
                'homeChurchName' => (!empty($member['homechurch']) ? $member['homechurch'] : ''),
                'familyUnitId' =>  (!empty($member['familyunitid']) ? $member['familyunitid'] : ''),
                'familyUnit' => (!empty($member['familyunit']) ? $member['familyunit'] : ''),
                'bloodGroup' => (!empty($member['memberbloodgroup']) ? $member['memberbloodgroup'] : ''),
                'tags' => (!empty($tagSearch) ? (!empty($member['membertag']) ?  $member['membertag'] : '') : ''),
                'associatedInstitutions' => $associatedInstitutions,
                'committeeDesignation' => $committeDesignation
            ];
            return $memberDetails;

    	}
    	catch(Exception $e){
    		yii::error($e->getMessage());
    		return false;
    	}
    }

    /**
	 * Set Member details
	 * @param $member array
	 * @param $associatedInstitutions array
	 * @param $committeDesignation array
	 * @param $memberMobileCountryCode string
	 * @param $spouseMobileCountryCode string
	 * @param $spouseMobileCountryCode string
	 * @param $spousemobileNumber string
	 * @return boolean value
	*/
    protected function setSpouseDetails($member, $associatedInstitutions, $committeDesignation, $memberMobileCountryCode, $memberMobileNumber, $spouseMobileCountryCode, $spousemobileNumber){
    	try{
    		$institutionTypeId = Yii::$app->user->identity->institution->institutiontype;
    		if(!empty($member['middleName'])){
    			$fullName = trim($member['spouse_firstName']). ' '.$member['spouse_middleName'].' '. trim($member['spouse_lastName']);
    		}
    		else{
    			$fullName = trim($member['spouse_firstName']).' '.trim($member['spouse_lastName']);
    		}
    		
    		$memberDetails = [
    			'spouseTitleId' => !empty($member['spousetitle']) ? $member['spousetitle'] : 0,
                'spouseTitle' => !empty($member['spousetitledescription']) ? $member['spousetitledescription'] : '',
                'spouseName' =>  $fullName,
                'spouseFirstName' => (!empty($member['spouse_firstName']) ? trim($member['spouse_firstName']) : ''),
                'spouseMiddleName' => (!empty($member['spouse_middleName']) ? trim($member['spouse_middleName']) : ''),
                'spouseLastName' => (!empty($member['spouse_lastName']) ? trim($member['spouse_lastName']) : ''),
                'spouseNickName' => (!empty($member['spousenickname']) ? trim($member['spousenickname']) : ''),
                'spouseImage' => (!empty($member['spouse_pic']) ? Yii::$app->params['imagePath'].$member['spouse_pic'] : ''),
                'spouseImageThumbnail' => (!empty($member['spouseImageThumbnail']) ? Yii::$app->params['imagePath'].$member['spouseImageThumbnail'] : ''),
                'dateOfMarriage' => (!empty($member['dom']) ? 
	  				date_format(date_create($member['dom']),
					Yii::$app->params['dateFormat']['viewDateFormat']) : ''),
                'spouseDob' => (!empty($member['spouse_dob']) ? 
	  				date_format(date_create($member['spouse_dob']),
					Yii::$app->params['dateFormat']['viewDateFormat']) : ''),
                'mobileCountryCode' => (!empty($spouseMobileCountryCode) ? $spouseMobileCountryCode : ''),
                'mobileNumber' => (!empty($spousemobileNumber) ? $spousemobileNumber : ''),
                'email' => (!empty($member['spouse_email']) ? $member['spouse_email'] : ''),
                'profession' => (!empty($member['spouseoccupation']) ? $member['spouseoccupation']:''),
                'bloodGroup' => (!empty($member['memberbloodgroup']) ? $member['memberbloodgroup'] : ''),
                'associatedInstitutions' => $associatedInstitutions,
                'committeeDesignation' => $committeDesignation
            ];
            return $memberDetails;

    	}
    	catch(Exception $e){
    		yii::error($e->getMessage());
    		return false;
    	}
    }

    /**
	 * Set dependant details
	 * @param $member array
	 * @return boolean value
	*/
    protected function setDependantsDetails($dependant){
    	try{
    		$result = [
    			'dependantId' => (!empty($dependant['id']) ? (int)$dependant['id'] : 0), 
                'dependantTitleId' => (!empty($dependant['dependanttitleid']) ? $dependant['dependanttitleid'] : ''),
                'dependantTitle' => (!empty($dependant['dependanttitle']) ? $dependant['dependanttitle'] : ''),
                'dependantName' => (!empty($dependant['dependantname']) ? $dependant['dependantname'] : ''),
                'dependantDob' => (!empty($dependant['dob']) ? date_format(date_create($dependant['dob']),Yii::$app->params['dateFormat']['viewDateFormat']) : ''),
                'dependantRelation' => (!empty($dependant['relation']) ? $dependant['relation'] : ''),
                'dependantMaritalStatus' => (!empty($dependant['ismarried']) ? (int)$dependant['ismarried'] : 0),
                'dependantSpouseId' => (!empty($dependant['dependantspouseid']) ? $dependant['dependantspouseid'] : ''),
                'dependantSpouseTitleId' => (!empty($dependant['spousetitleid']) ? $dependant['spousetitleid'] : ''),
                'dependantSpouseTitle' => (!empty($dependant['spousetitle']) ? $dependant['spousetitle'] : ''),
                'dependantSpouseName' => (!empty($dependant['spousename']) ? $dependant['spousename'] : ''),
                'dependantSpouseDateOfBirth' => (!empty($dependant['spousedob']) ? date_format(date_create($dependant['spousedob']),Yii::$app->params['dateFormat']['viewDateFormat']) : ''),
                'dependantWeddingAnniversary' => (!empty($dependant['weddinganniversary']) ? date_format(date_create($dependant['weddinganniversary']),Yii::$app->params['dateFormat']['viewDateFormat']) : ''),
                'dependantImage' => (!empty($dependant['dependantimage']) ? Yii::$app->params['imagePath'].$dependant['dependantimage'] : ''),
                'dependantImageThumbnail' => (!empty($dependant['dependantthumbnailimage']) ? Yii::$app->params['imagePath'].$dependant['dependantthumbnailimage'] : ''),
                'dependantSpouseImage' => (!empty($dependant['dependantspouseimage']) ? Yii::$app->params['imagePath'].$dependant['dependantspouseimage'] : ''),
                'dependantSpouseImageThumbnail' => (!empty($dependant['dependantspousethumbnailimage']) ? Yii::$app->params['imagePath'].$dependant['dependantspousethumbnailimage'] : ''),
    		];
    		return $result;
    	}
    	catch(Exception $e){
    		yii::error($e->getMessage());
    		return false;
    	}
    }
    /**
	 * Retrieving the details of a particular member
	 * @param $lastUpdatedOn datetime
	 * @return $statusCode int
	 * @return $data array
	 * @return $message string
	 * @author : Amal
	*/
	public function actionGetMemberDetailsFileForSync()
	{

		$request = Yii::$app->request;
		$lastUpdatedOn = $request->get('lastUpdatedOn');
		$user = Yii::$app->user->identity;
		$currentInstitution = $user->institution;
		$headers = $request->headers;
		// returns the timezone header value
		if ($headers->has('timezone')) {
			$timezone = $headers->get('timezone');
			date_default_timezone_set('Asia/Kolkata');
		}

		$updatedOn = date("Y-m-d H:i:s");

  		$response = [];
  		$responseDependants = [];
        $return = [];
        $returnDependant = [];
        $responseRemovedContacts = [];
        $removedContacts = [];
        $finalData = [];
        $returnData = new \stdClass();
        $responseInstitution = [];
        $returnCommitteeDesgMember = [];
        $returnCommitteeDesgSpouse = [];
        $responseInstitutionMember = [];
        $responseInstitutionSpouse = [];
        $returnInstitutionMember = [];
        $returnInstitutionSpouse = [];
        $spousemobilecountrycode = "";
        $spousemobileNumber ="";

		if ($user) {
			$baseModel = new BaseModel();
			$userId = $user->id;
			$institutionId 		= $user->institutionid;
			$institutionType 	= $currentInstitution->institutiontype;
			$userType 			= $user->usertype;
			$tagSearch 			= $currentInstitution->advancedsearchenabled;
			$memberDetails 		=  ExtendedMember::getMemberId($userId, $institutionId, $userType);
			$currentMemberId 	= $memberDetails['memberid'];
			$dtUpdated 			= ($lastUpdatedOn) ? date('Y-m-d H:i:s', strtotimeNew($lastUpdatedOn)) : NULL;
	  	 	$response 						= $baseModel->getMemberDetailsFileForSync($userId, $dtUpdated);
	  	 	$responseDependants 			= $baseModel->getDependantsForSync($userId);
	  	 	$responseRemovedContacts 		= $baseModel->getDeletedContactsForSync($userId, null);
	  	 	$responseCommitteeDesgMember 	= $baseModel->getCommitteeDesignationsForSync($userId,'M');
	  	 	$responseCommitteeDesgSpouse 	= $baseModel->getCommitteeDesignationsForSync($userId,'S');

	  	 	$responseInstitutionMember = $baseModel->getAssosiatedInstitutions($userId,'M', $institutionId);
	  	 	$responseInstitutionSpouse = $baseModel->getAssosiatedInstitutions($userId,'S', $institutionId);

	  	 	if(!empty($responseInstitutionMember) && $responseInstitutionMember->status == true && !empty($responseInstitutionMember->value)) {
	  	 		foreach ($responseInstitutionMember->value as $iKey => $iValue) {
	  	 			$returnInstitutionMember[$iValue['memberid']][] = [
	  	 				'institutionName' => !empty($iValue['institutionname']) ? $iValue['institutionname'] : "" 
	  	 			];
	  	 		}
	  	 	}
	  	 	if(!empty($responseInstitutionSpouse) && $responseInstitutionSpouse->status == true && !empty($responseInstitutionSpouse->value)) {
	  	 		foreach ($responseInstitutionSpouse->value as $isKey => $isValue) {
	  	 			$returnInstitutionSpouse[$isValue['memberid']][] = [
	  	 				'institutionName' => !empty($isValue['institutionname']) ? $iValue['institutionname'] : ""
	  	 			];
	  	 		}

	  	 	}

	  	   if(!empty($responseCommitteeDesgMember) && $responseCommitteeDesgMember->status == true && !empty($responseCommitteeDesgMember->value)) {
	  	 		foreach ($responseCommitteeDesgMember->value as $cKey => $cValue) {
	  	 			$returnCommitteeDesgMember[$cValue['memberid']][] = [
	  	 				'committeeName' => !empty($cValue['committeetype'])? $cValue['committeetype']:"", 
                        'designation' => !empty($cValue['designation'])? $cValue['designation']:""
	  	 			];
	  	 		}

	  	 	}
	  	 	if(!empty($responseCommitteeDesgSpouse) && $responseCommitteeDesgSpouse->status == true && !empty($responseCommitteeDesgSpouse->value)) {
	  	 		foreach ($responseCommitteeDesgSpouse->value as $rsKey => $rsValue) {
	  	 			$returnCommitteeDesgSpouse[$rsValue['memberid']][] = [
	  	 				'committeeName' => !empty($rsValue['committeetype']) ? $rsValue['committeetype'] :"", 
                        'designation' => !empty($rsValue['designation']) ? $rsValue['designation'] :""
	  	 			];
	  	 		}

	  	 	}

			if(!empty($responseDependants) && $responseDependants->status ===true && !empty($responseDependants->value)) {
                foreach ($responseDependants->value as $dKey =>$dValue) {
                    $returnDependant[$dValue['memberid']][] =[
                    	'dependantId' => !empty($dValue['id']) ? $dValue['id'] : "",
						'dependantName' => !empty($dValue['dependantname']) ? $dValue['dependantname'] : "",
						'dependantDob' => ($dValue['dob'])? date('d F Y',strtotimeNew($dValue['dob'])) : "",
						'dependantRelation' =>!empty($dValue['relation']) ? $dValue['relation'] : "",
						'dependantTitleId' =>!empty($dValue['dependanttitleid']) ? $dValue['dependanttitleid'] : "",
						'dependantTitle' =>!empty($dValue['dependanttitle']) ? $dValue['dependanttitle'] : "",
						'dependantMaritalStatus' => (int)$dValue['ismarried'],
						'dependantSpouseId' =>!empty($dValue['spousedependantid']) ? $dValue['spousedependantid'] : "",
						'dependantSpouseTitle' =>!empty($dValue['spousetitle']) ? $dValue['spousetitle'] : "",
						'dependantSpouseTitleId' =>!empty($dValue['spousetitleid']) ? $dValue['spousetitleid'] : "",
						'dependantSpouseName' =>!empty($dValue['spousefullname']) ? $dValue['spousefullname'] : "",
						'dependantSpouseDateOfBirth' => ($dValue['spousedob'])? date('d F Y',strtotimeNew($dValue['spousedob'])) : "",
						'dependantWeddingAnniversary' => ($dValue['weddinganniversary'])? date('d F Y',strtotimeNew($dValue['weddinganniversary'])) : "",
					    'dependantImage' => (!empty($dValue['dependantimage'])) 
            								 ? 
            								 (string)preg_replace('/\s/', "%20",Yii::$app->params['imagePath'].$dValue['dependantimage']) 
            								 :Yii::$app->params['imagePath'].'/Member/default-user.png',
						'dependantImageThumbnail' => (!empty($dValue['dependantthumbnailimage'])) 
            								 ? 
            								 (string)preg_replace('/\s/', "%20",Yii::$app->params['imagePath'].$dValue['dependantthumbnailimage']) 
            								 :Yii::$app->params['imagePath'].'/Member/default-user.png',
					    'dependantSpouseImage' => (!empty($dValue['dependantspouseimage'])) 
            								 ? 
            								 (string)preg_replace('/\s/', "%20",Yii::$app->params['imagePath'].$dValue['dependantspouseimage']) 
            								 :Yii::$app->params['imagePath'].'/Member/default-user.png',
						'dependantSpouseImageThumbnail' => (!empty($dValue['dependantspousethumbnailimage'])) 
            								 ? 
            								 (string)preg_replace('/\s/', "%20",Yii::$app->params['imagePath'].$dValue['dependantspousethumbnailimage']) 
            								 :Yii::$app->params['imagePath'].'/Member/default-user.png',

					];
                }
            }
            if(!empty($responseRemovedContacts) && $responseRemovedContacts == true && !empty($responseRemovedContacts->value)) {
				$removedContacts = array_map('intval',$responseRemovedContacts->value);
            }
	  	 	if (!empty($response) && $response->status == true && !empty($response->value)) {
				foreach($response->value as $val) {
					
					if($val['membermobilePrivacyEnabled'] == 0) {
						$membermobilecountrycode = (empty($val['member_mobile1_countrycode'])) ? "" : $val['member_mobile1_countrycode'];
                    	$membermobileNumber = $val['member_mobile1'];
					} else {
						$membermobilecountrycode = "";
                    	$membermobileNumber = 'Private';
					}
					
					if($val['spousemobilePrivacyEnabled'] == 0) {
	                    if (!empty(trim($val['spouse_firstName'] ?? ''))) {
	                        $spousemobilecountrycode = (empty($val['spouse_mobile1_countrycode'])) ? "" : $val['spouse_mobile1_countrycode'];
	                        $spousemobileNumber = $val['spouse_mobile1'];
	                    }
	                } else {
	                	$spousemobilecountrycode = "";
	                	$spousemobileNumber = "Private";
	                }

					
					
            		$return[$val['institutionid']]['institutionId'] = (int)$val['institutionid'];
            		$return[$val['institutionid']]['institutionName'] = $val['institutionName'];
            		$return[$val['institutionid']]['members'][]= [
            			'memberId' => (string)$val['memberid'],
            			'membershipDetails' => [
            				'membershipId'	 	=> !empty($val['memberno']) ? $val['memberno'] : "" ,
            				'membershipType' 	=> !empty($val['membershiptype']) ? $val['membershiptype'] :"",
							'memberSince' 		=> $institutionType == 4 ? ( !empty($val['batch']) ? $val['batch']  :"" ) : ( ($val['membersince']) ? date('Y',strtotimeNew($val['membersince'])) :"" ),
							'batch' 			=> $institutionType == 4 ? ( !empty($val['batch']) ? $val['batch']  :"" ) : ( ($val['membersince']) ? date('Y',strtotimeNew($val['membersince'])) :"" ),
            			],
            			'memberDetails' => [
            				'memberTitleId' 	=> !empty($val['membertitledescription']) ? $val['membertitle'] : 0,
            				'memberTitle' 		=> !empty($val['membertitledescription']) ? $val['membertitledescription'] : "",
            				'memberName' 		=> !empty($val['memberName']) ? $val['memberName'] : "",
            				'memberFirstName' 	=> !empty($val['firstName']) ? $val['firstName']  :"",
            				'memberMiddleName' 	=> !empty($val['middleName']) ? $val['middleName'] : "",
            				'memberLastName' 	=> !empty($val['lastName']) ? $val['lastName'] : "",
            				'memberNickName' 	=> !empty($val['membernickname']) ? $val['membernickname'] :"",
            				'memberImage' 		=> (!empty($val['member_pic'])) 
            								 ? 
            								 (string)preg_replace('/\s/', "%20",Yii::$app->params['imagePath'].$val['member_pic']) 
            								 :"",
            				'memberImageThumbnail' => (!empty($val['memberImageThumbnail'])) 
            								 ? 
            								 (string)preg_replace('/\s/', "%20",Yii::$app->params['imagePath'].$val['memberImageThumbnail']) 
            								 :"",
            				'memberDob' 		=> ($val['member_dob']) ? date('d F Y',strtotimeNew($val['member_dob'])) : "",
							'email' 			=> !empty($val['member_email']) ? $val['member_email'] :"",
							'profession' 		=> !empty($val['occupation']) ? $val['occupation'] : "",
							'isChurch' 			=> ($institutionType == 2) ? true : false,
							'homeChurchName' 	=> !empty($val['homechurch']) ? $val['homechurch'] : "",
							'familyUnit' 		=> !empty($val['familyunit']) ? $val['familyunit'] : "",
                            'familyUnitId' 		=> !empty($val['familyunitid']) ? $val['familyunitid'] : "",
							'bloodGroup'		=> !empty($val['memberbloodgroup']) ? $val['memberbloodgroup'] : "",
                  			'tags' 				=> ($tagSearch) ? (!empty($val['membertag']) ? $val['membertag'] : "" ):"",
                  			'mobileCountryCode' =>  empty($membermobileNumber) ? "" : $membermobilecountrycode,
            				'mobileNumber' 		=> !empty($membermobileNumber) ? $membermobileNumber  : "",
            				'landLineCountryCode' 		=> !empty($val['member_residence_phone1_countrycode']) ? $val['member_residence_phone1_countrycode'] : "",	
							'landLineAreaCode' 			=> !empty($val['member_residence_Phone1_areacode']) ? $val['member_residence_Phone1_areacode'] : "",
							'landLineNumber'			=> !empty($val['member_residence_Phone1']) ? $val['member_residence_Phone1'] : "",
							'associatedInstitutions' 	=> (isset($returnInstitutionMember[$val['memberid']])) ? 
														$returnInstitutionMember[$val['memberid']] : [array('institutionName' => $currentInstitution->name)],
							'committeeDesignation' 		=> (isset($returnCommitteeDesgMember[$val['memberid']])) ? $returnCommitteeDesgMember[$val['memberid']] :[]
            			],
            			'spouseDetails' => [
            				'spouseTitleId' => !empty($val['spousetitledescription']) ? $val['spousetitle'] : 0,
            				'spouseTitle' => !empty($val['spousetitledescription']) ? $val['spousetitledescription'] : "",
            				'spouseName' => !empty($val['spouseName']) ? $val['spouseName'] : "",
            				'spouseFirstName' => !empty($val['spouse_firstName']) ? $val['spouse_firstName'] : "",
            				'spouseMiddleName' => !empty($val['spouse_middleName']) ? $val['spouse_middleName'] : "",
            				'spouseLastName' => !empty($val['spouse_lastName']) ? $val['spouse_lastName'] : "",
            				'spouseNickName' => !empty($val['spousenickname']) ? $val['spousenickname'] : "",
            				'spouseImage' => (!empty($val['spouse_pic'])) 
            								 ? 
            								 (string)preg_replace('/\s/', "%20",Yii::$app->params['imagePath'].$val['spouse_pic']) 
            								 :"",
            				'spouseImageThumbnail' => (!empty($val['spouseImageThumbnail'])) 
            								 ? 
            								 (string)preg_replace('/\s/', "%20",Yii::$app->params['imagePath'].$val['spouseImageThumbnail']) 
            								 :"",
            				'spouseDob' => ($val['spouse_dob']) ? date('d F Y',strtotimeNew($val['spouse_dob'])) : "",
            				'dateOfMarriage' => ($val['dom']) ? date('d F Y',strtotimeNew($val['dom'])) : "",
            				'mobileCountryCode' =>  empty($spousemobileNumber) ? "" : $spousemobilecountrycode,
            				'mobileNumber' => !empty($spousemobileNumber) ? $spousemobileNumber : "",
							'email' => !empty($val['spouse_email']) ? $val['spouse_email'] : "",
							'profession' => !empty($val['spouseoccupation']) ? $val['spouseoccupation'] : "",
							'bloodGroup' => !empty($val['spousebloodgroup']) ? $val['spousebloodgroup'] :"",
							'associatedInstitutions' => (isset($returnInstitutionSpouse[$val['memberid']])) ? 
														$returnInstitutionSpouse[$val['memberid']] : [array('institutionName' => $currentInstitution->name)],
							'committeeDesignation' => (isset($returnCommitteeDesgSpouse[$val['memberid']])) ? $returnCommitteeDesgSpouse[$val['memberid']] :[]
            			],
            			'hasResidentialAddress' => !empty($val['residence_address1']) ? true : false,
						'residentialAddress' => [
							'addressLine1' => !empty($val['residence_address1']) ?  $val['residence_address1'] : "",
							'addressLine2' => !empty($val['residence_address2']) ? $val['residence_address2'] : "",
							'city' => !empty($val['residence_district']) ? $val['residence_district'] : "",
							'state' => !empty($val['residence_state']) ? $val['residence_state'] : "",
							'pinCode' => !empty($val['residence_pincode']) ? $val['residence_pincode'] : ""
						],
						'hasBusinessDetails' => !empty($val['business_address1']) ? true : false,
						'businessDetails' => [	
							'officePhone' => [
							    'countryCode' => !empty($val['member_business_phone1_countrycode'])? $val['member_business_phone1_countrycode']:"",
							    'areaCode' => !empty($val['member_business_phone1_areacode']) ?$val['member_business_phone1_areacode'] :"",
							    'phoneNumber' => !empty($val['member_business_Phone1'])? $val['member_business_Phone1']:""
							],
							'alternativeOfficePhone' => [
							    'countryCode' => !empty($val['member_business_phone3_countrycode']) ? $val['member_business_phone3_countrycode'] :"",
							    'areaCode' => !empty($val['member_business_phone3_areacode']) ? $val['member_business_phone3_areacode'] :"",
							    'phoneNumber' => !empty($val['member_business_Phone3']) ? $val['member_business_Phone3'] :""
							],
							'email' => !empty($val['businessemail']) ? $val['businessemail'] : "",
							'addressLine1' => !empty($val['business_address1']) ? $val['business_address1'] : "",
							'addressLine2' => !empty($val['business_address2']) ? $val['business_address2'] : "",
							'city' => !empty($val['business_district']) ? $val['business_district'] : "",
							'state' => !empty($val['business_state']) ? $val['business_state'] : "",
							'pinCode' => !empty($val['business_pincode']) ? $val['business_pincode'] : ""
						],
						'hasDependants' => isset($returnDependant[$val['memberid']]) ? (count($returnDependant[$val['memberid']]) > 0 ? true: false ):false,
						'dependants' => isset($returnDependant[$val['memberid']]) ? $returnDependant[$val['memberid']] : [],
            		];
        		}	
        		$finalData = [
        			'contacts' =>[
						'updatedOn' => date( "Y-m-d H:i:s", strtotimeNew($updatedOn.' -1 minute')),
						'institutions' => array_values($return),
						'removedContacts' => $removedContacts
					]
	    		];
	    		$status = $this->processFileForSync($finalData,$userId);
    			if ($status) {
    				$fileName = Yii::$app->params['imagePath'].'/'.Yii::$app->params['contacts']['contactPath'].'/'.$userId.'.txt';
    				$returnData->fileUrl = (string)$fileName;
    				$this->data = $returnData;
					$this->statusCode = 200;
					$this->message = '';
    			} else {
    				yii::error('File upload failed');
    				$this->statusCode = 500;
					$this->message = 'An error occurred while processing the request';
					$this->data = new \stdClass();
    			}
	  	 	} else if(!empty($response) && $response->status == true) {
	  	 		$finalData = [
        			'contacts' =>[
						'updatedOn' => date( "Y-m-d H:i:s", strtotimeNew($updatedOn.' -1 minute')),
						'institutions' => [],
						'removedContacts' => $removedContacts
					]
	    		];
	    		$status = $this->processFileForSync($finalData,$userId);
	    		if ($status) {
    				$fileName = Yii::$app->params['imagePath'].'/'.Yii::$app->params['contacts']['contactPath'].'/'.$userId.'.txt';
    				$returnData->fileUrl = (string)$fileName;
    				$this->data = $returnData;
					$this->statusCode = 200;
					$this->message = '';
    			} else {
    				yii::error('File upload failed');
    				$this->statusCode = 500;
					$this->message = 'An error occurred while processing the request';
					$this->data = new \stdClass();
    			}
	  	 	} else {
	  	 		yii::error('member selection failed');
    			$this->statusCode = 500;
				$this->message = 'An error occurred while processing the request';
				$this->data = new \stdClass();
	  	 	}
		} else {
			$this->statusCode = 498;
			$this->message = 'Session has expired. Please login again';
			$this->data = new \stdClass();
		}
		return new ApiResponse($this->statusCode, $this->data,$this->message);
    }
    protected function processFileForSync($finalData,$userId)
    {	
    	$status = false;
		$fileUpload = new FileuploadComponent();
    	//Write to a file
	    $contactData = json_encode(new ArrayValue($finalData), JSON_PRETTY_PRINT); 
    	$filePath = Yii::getAlias('@service').'/'.Yii::$app->params['contacts']['contactPath'].'/'.$userId.'.txt';
    	$status = $fileUpload->uploadContactsFile($contactData, $filePath);
    	return $status;
    }
}
class ArrayValue implements \JsonSerializable {
    private array $array;

    public function __construct(array $array) {
        $this->array = $array;
    }

    public function jsonSerialize(): mixed {
        return $this->array;
    }
}