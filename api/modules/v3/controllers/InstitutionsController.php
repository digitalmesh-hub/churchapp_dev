<?php 

namespace api\modules\v3\controllers;

use Yii;
use api\modules\v3\controllers\BaseController;
use api\modules\v3\models\responses\ApiResponse;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\extendedmodels\ExtendedSettings;
use common\models\extendedmodels\ExtendedAffiliatedinstitution;
use common\models\extendedmodels\ExtendedInstitutionPaymentGateways;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedCommittee;
use common\models\extendedmodels\ExtendedAddresstype;
use common\models\extendedmodels\ExtendedFamilyunit;
use common\models\extendedmodels\ExtendedCommitteegroup;
use common\models\extendedmodels\ExtendedCommitteePeriod;
use common\models\basemodels\BaseModel;
use common\models\extendedmodels\ExtendedPrivilege;
use common\models\extendedmodels\ExtendedDashboard;

class InstitutionsController extends BaseController
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
						'get-institution-details' => ['GET'],
						'get-affiliated-institutions' => ['GET'],
						'save-institution-settings' => ['POST'],
						'get-merchant-details' => ['GET'],
						'change-institution' => ['POST']
					]
				],
			]
		);
	}
	
	/**
	 * Index action.
	 * @return $statusCode int
	 */
	public function actionIndex()
	{
		throw new \yii\web\HttpException($this->statusCode);
	}
	/**
	 * To get the details 
	 * about an institution
	 * @param $userId int
	 * @param $institutionId int
	 */
	public function actionGetInstitutionDetails()
	{
		$request = Yii::$app->request;
		$user = $request->get('userId');
		$institution = $request->get('institutionId');
		$userId =  $user ? $user : yii::$app->user->identity->id;
		$institutionId = $institution ? $institution : yii::$app->user->identity->institutionid;	
		if ($userId && $institutionId) {
			$institutionStatus = (new \yii\db\Query())
				->select(['active'])
				->from('institution')
				->where(['id' => $institutionId])
				->one();
				$institutionDetails = ExtendedInstitution::getInstitutionData($institutionId);
				$userSettings = ExtendedSettings::getUserSettings($userId,$institutionId);
				$userInstitutions = ExtendedInstitution::getUserInstitutions($userId);
				$resultSet = [];
				$result = new \stdClass();
				if ($institutionDetails) {
					foreach ($institutionDetails as $key => $value) {
						$social_media = $value['social_media'] ? json_decode($value['social_media']) : [];
						$data = new \stdClass();
						$data->institutionId = (!empty($value['id'])) ? $value['id'] : '';
						$data->institutionName = (!empty($value['name'])) ? $value['name'] : '';
						$data->institutionLogo = (!empty($value['institutionlogo'])) ? (string)preg_replace('/\s/', "%20", Yii::$app->params['imagePath'].$value['institutionlogo']) : '';
						$data->phoneNumber1 = (!empty($value['phone1'])) ? $value['phone1'] : 'Not available';
						$data->phoneNumber2 = (!empty($value['phone2'])) ? $value['phone2'] : 'Not available';
						$data->email = (!empty($value['email'])) ? $value['email'] : '';
						$data->website = (!empty($value['url'])) ? $value['url'] : '';
						$address = new \stdClass();
						$address->addressline1 = (!empty($value['address1'])) ? $value['address1'] : '';
						$address->addressline2 = (!empty($value['address2'])) ? $value['address2'] : '';
						$address->city = (!empty($value['district'])) ? $value['district'] : '';
						$address->state = (!empty($value['state'])) ? $value['state'] : '';
						$address->pincode = (!empty($value['pin'])) ? $value['pin'] : '';
						$address->country = (!empty($value['location'])) ? $value['location'] : '';
						$data->address = $address;
						$data->facebook = $social_media->facebook ?? '';
						$data->youtube = $social_media->youtube ?? '';
						$data->instagram = $social_media->instagram ?? '';
						$data->twitter = $social_media->twitter ?? '';
					}
				} else {
					$data = new \stdClass();
					$data->institutionId = '';
					$data->institutionName =  '';
					$data->institutionLogo =  '';
					$data->phoneNumber1 = '';
					$data->phoneNumber2 = '';
					$data->email = '';
					$data->website = '';
					$address = new \stdClass();
					$address->addressline1 = '';
					$address->addressline2 =  '';
					$address->city = '';
					$address->state = '';
					$address->pincode = '';
					$address->country = '';
					$data->address = $address;
					$data->facebook = '';
					$data->twitter = '';
					$data->youtube = '';
					$data->instagram = '';
				}

				if($userSettings){
					$data->communicationAddressId = (!empty($userSettings['addresstypeid'])) ? (string)$userSettings['addresstypeid'] : '';
					$data->pushNotificationsEnabled = $userSettings['usertype'] == 'M' ? ($userSettings['membernotification'] == '1' ? true :false) : ($userSettings['spousenotification'] == '1' ?
						true : false);
					$data->birthdayNotificationsEnabled = $userSettings['usertype'] == 'M' ? ($userSettings['birthday'] == '1'? true : false) : ($userSettings['spousebirthday'] == '1' ? true : false);
					$data->anniversaryNotificationsEnabled = $userSettings['usertype'] == 'M' ? ($userSettings['anniversary'] == '1' ? true : false)  : ($userSettings['spouseanniversary'] == '1' ? true : false);
					$data->smsNotificationsEnabled = $userSettings['usertype'] == 'M' ? ($userSettings['membersms'] == '1' ? true : false) : ($userSettings['spousesms'] == '1' ?
						 true : false);
					$data->emailNotificationsEnabled = $userSettings['usertype'] == 'M' ? ($userSettings['memberemail'] == '1' ? true : false) : ($userSettings['spouseemail'] == '1' ? true : false);	
				} else {
					$data->communicationAddressId = '';
					$data->pushNotificationsEnabled = false;
					$data->birthdayNotificationsEnabled = false;
					$data->anniversaryNotificationsEnabled = false;
					$data->smsNotificationsEnabled = false;
					$data->emailNotificationsEnabled = false;
				}
				$temp = [];
				if ($userInstitutions) {
					foreach ($userInstitutions as $key => $value) {
						$temp[] = [
								'institutionId' => (!empty($value['id'])) ? (string)$value['id'] : '',
								'institutionName' => (!empty($value['name'])) ? (string)$value['name'] : ''				
						];	
					}
				} else {
					$temp[] = [
						'institutionId' =>  '',
						'institutionName' => ''				
					];
				}
			
			    $data->institutions = $temp;
				$this->statusCode = 200;
				$this->message = '';
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data,$this->message);	
		} else {
			$this->statusCode = 500;
			$this->message = 'Session expired/invalid user';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}

	/***
	 * To get the details of
	 * the affiliated institutions of an institutions
	 * @param $institutionId int
	 * @return $statusCode int
	 */
	public function actionGetAffiliatedInstitutions()
	{
		$request = Yii::$app->request;
		$instId = $request->get('institutionId');
		$institutionId = (empty($instId))? Yii::$app->user->identity->institutionid : $instId;
		if ($institutionId) {
			$institutionId = filter_var($institutionId, FILTER_SANITIZE_NUMBER_INT);
			$institutionDetails = ExtendedInstitution::getInstitutionDetails($institutionId);
			$affiliatedInstitutionDetails = ExtendedAffiliatedinstitution::getAffiliatedInstitutionData($institutionId);
			
			$isRotary = $institutionDetails['isrotary']== 1 ? 1 : 0;
			$data = new \stdClass();
			$data->template = ($isRotary == 1) ? 1 : 0;
			$data->templateName = ($data->template == 1) ? 'rotary' : 'default';
			$institution = [];
			$address = new \stdClass();

			if ($affiliatedInstitutionDetails) {
				foreach ($affiliatedInstitutionDetails as $key => $value){
					 if (strpos($value['institutionlogo'] ?? '', 'service/') !== false) {
				        $path = (string)preg_replace('/\s/', "%20", Yii::$app->params['imagePath'].$value['institutionlogo']);
				    } else {
				        $path = (string)preg_replace('/\s/', "%20",Yii::$app->params['imagePath'].'/institutionlogo/'. $institutionId.'/'.$value['institutionlogo']);
				    }
					$institution[] = [
						'institutionId' => (!empty($value['affiliatedinstitutionid'])) ? $value['affiliatedinstitutionid'] : '',
						'institutionName' => (!empty($value['name'])) ? $value['name'] : '',
						'institutionLogo' => (!empty($value['institutionlogo'])) ? (string) $path : '',
						'phoneNumber1' => (!empty($value['phone1'])) ? $value['phone1'] : 'Not available',
						'phoneNumber2' => (!empty($value['phone2'])) ? $value['phone2'] : 'Not available',
						'email' => (!empty($value['email'])) ? $value['email'] : '',
						'website' => (!empty($value['url'])) ? $value['url'] : '',
						'address' => [
							'addressline1' => (!empty($value['address1'])) ? $value['address1'] : '',
							'addressline2' => (!empty($value['address2'])) ? $value['address2'] : '',
							'city' => (!empty($value['district'])) ? $value['district'] : '',
							'state' => (!empty($value['state'])) ? $value['state'] : '',
							'pincode' => (!empty($value['pin'])) ? $value['pin'] : '',
							'country' => (!empty($value['location'])) ? $value['location'] : '',
							
							],
					 	'defaultTemplate' => [
								'phoneNumber1' => (!empty($value['phone1'])) ? $value['phone1'] : 'Not available',
								'phoneNumber2' => (!empty($value['phone2'])) ? $value['phone2'] : 'Not available',
						],
						'rotaryCochinTemplate' => [
								'presidentName' => (!empty($value['presidentname'])) ? $value['presidentname'] : '',
								'presidentMobile' => (!empty($value['presidentmobile'])) ? (($value['presidentmobile_countrycode']) ? $value['presidentmobile_countrycode'].'-'. $value['presidentmobile'] : $value['presidentmobile'] ): 'Not available', 
								'secretaryMobile' => (!empty($value['secretarymobile'])) ? ( ($value['secretarymobile_countrycode']) ? $value['secretarymobile_countrycode'].'-'. $value['secretarymobile']: $value['secretarymobile'] ):'Not available', 
								'secretaryName' => (!empty($value['secretaryname'])) ? $value['secretaryname'] : '',
								'meetingVenue' => (!empty($value['meetingvenue'])) ? $value['meetingvenue'] : '',
								'meetingDay' => (!empty($value['meetingday'])) ? $value['meetingday'] : '',
								'meetingTime' => (!empty($value['meetingtime'])) ? $value['meetingtime'] : '',
								'remarks' => (!empty($value['remarks'])) ? $value['remarks'] : '',
						],
					];
				}
			} else {
				$institution = [];
			}

			$data->institutions = $institution;
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
	}
	/**
	 * To update the institution settings
	 * @param $userId string
	 * @param $institutionId string
	 * @param $communicationAddressId string
	 * @param $pushNotificationsEnabled boolean
	 * @param $birthdayNotificationsEnabled boolean
	 * @param $anniversaryNotificationsEnabled boolean
	 * @param $smsNotificationsEnabled boolean
	 * @param $emailNotificationsEnabled boolean
	 * @return $statusCode int
	 */
	public function actionSaveInstitutionSettings()
	{
		$request = Yii::$app->request;
		$userId = $request->getBodyParam('userId');
		$institutionId = $request->getBodyParam('institutionId');
		$communicationAddressId = $request->getBodyParam('communicationAddressId');
		$pushNotificationsEnabled = $request->getBodyParam('pushNotificationsEnabled');
		$birthdayNotificationsEnabled = $request->getBodyParam('birthdayNotificationsEnabled');
		$anniversaryNotificationsEnabled = $request->getBodyParam('anniversaryNotificationsEnabled');
		$smsNotificationsEnabled = $request->getBodyParam('smsNotificationsEnabled');
		$emailNotificationsEnabled = $request->getBodyParam('emailNotificationsEnabled');
		if($userId && $institutionId)
		{
			$userId = filter_var($userId, FILTER_SANITIZE_NUMBER_INT);
			$institutionId = filter_var($institutionId, FILTER_SANITIZE_NUMBER_INT);
			$communicationAddressId = filter_var($communicationAddressId, FILTER_SANITIZE_NUMBER_INT);
			$institutionDetails = ExtendedSettings::getUserSettings($userId, $institutionId);
			if($institutionDetails){
				$settingsId = $institutionDetails['id'];
				$memberNotification = ($institutionDetails['usertype'] == 'M') ? $pushNotificationsEnabled : $institutionDetails['membernotification'];
				$anniversaryNotifications = ($institutionDetails['usertype'] == 'M') ? $anniversaryNotificationsEnabled : $institutionDetails['anniversary'];
				$smsNotifications = ($institutionDetails['usertype'] == 'M') ? $smsNotificationsEnabled : $institutionDetails['membersms'];
				$emailNotifications = ($institutionDetails['usertype'] == 'M') ? $emailNotificationsEnabled : $institutionDetails['memberemail'];
				$birthdayNotifications = ($institutionDetails['usertype'] == 'M') ? $birthdayNotificationsEnabled : $institutionDetails['birthday'];
				$addressTypeId = $communicationAddressId;
				$syncContactinterval = 1;
				$spouseNotification = ($institutionDetails['usertype'] == 'S') ? $pushNotificationsEnabled : $institutionDetails['spousenotification'];
				$spouseAnniversaryNotification = ($institutionDetails['usertype'] == 'S') ? $anniversaryNotificationsEnabled : $institutionDetails['spouseanniversary'];
				$spouseSmsNotification = ($institutionDetails['usertype'] == 'S') ? $smsNotificationsEnabled : $institutionDetails['spousesms'];
				$spouseEmailNotification = ($institutionDetails['usertype'] == 'S') ? $emailNotificationsEnabled : $institutionDetails['spouseemail'];
				$spouseBirthdayNotifications = ($institutionDetails['usertype'] == 'M') ? $birthdayNotificationsEnabled : $institutionDetails['birthday'];
				$updateInstitutionSettings = ExtendedSettings::updateUserSettings($settingsId, $addressTypeId, $memberNotification, $birthdayNotifications, $anniversaryNotifications, $emailNotifications, $smsNotifications, $spouseEmailNotification, $spouseSmsNotification, $spouseNotification, $spouseBirthdayNotifications, $spouseAnniversaryNotification, $syncContactinterval);
				$this->statusCode = 200;
				$this->message = 'Saved successfully';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			}
			else{
				$this->statusCode = 500;
				$this->message = 'An error occurred while processing the request';
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
	 * To get the merchant details
	 * @param $institutionId string
	 * @param $q string
	 */
	public function actionGetMerchantDetails()
	{
		$request = Yii::$app->request;
		$institutionId = $request->get('institutionId');
		$q = $request->get('q');

		if($institutionId){
			$type = ($q) ? 'test' : 'live';
			$merchantDetails = ExtendedInstitutionPaymentGateways::getMerchantData($institutionId, $type);
			$data = new \stdClass();
			if ($merchantDetails) {
				$data->merchantUrl = (!empty($merchantDetails['PaymentUrl'])) ? $merchantDetails['PaymentUrl'] : '';
				$data->key = (!empty($merchantDetails['guid'])) ? $merchantDetails['guid'] : '';
				$this->statusCode = 200;
				$this->message = '';
				$this->data = $data;
			} else {
				$this->statusCode = 500;
				$this->message = "Sorry, We couldn't process your request. Please contact administrator";
				$this->data = new \stdClass();
			}
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		} else {
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	/**
	 * To change the institution details
	 * @param $userId string
	 * @param $institutionId string
	 */
	public function actionChangeInstitution()
	{
		$request = Yii::$app->request;
		$userId = $request->getBodyParam('userId');
		$institutionId = $request->getBodyParam('institutionId');
		$resultSet = [];
		$dataSet = [];
		$address = [];
		$privileges = [];
		$grpData = [];
		$familyUnit =[];
		$ob = new \stdClass();

		$result = new \stdClass();
		if ($userId && $institutionId) {
			$userId = filter_var($userId, FILTER_SANITIZE_NUMBER_INT);
			$institutionId = filter_var($institutionId, FILTER_SANITIZE_NUMBER_INT);
			$institutionDetails = ExtendedInstitution::changeInstitution($userId, $institutionId);
			$institutionFeatureSettings = ExtendedInstitution::getInstitutionFeatureDetails($institutionId);
			if ($institutionDetails) {
				$newUsertype = $institutionDetails['usertype'];
				$memberNotification = ($newUsertype == 'M') ? $institutionDetails['membernotification'] : $institutionDetails['spousenotification'];
				$emailMember = ($newUsertype == 'M') ? $institutionDetails['memberemail'] : $institutionDetails['spouseemail'];
				$memberSms = ($newUsertype == 'M') ? $institutionDetails['membersms'] : $institutionDetails['spousesms'];
				$memberBirthdayNotification = ($newUsertype == 'M') ? $institutionDetails['birthday'] : $institutionDetails['spousebirthday'];
				$memberAnniversaryNotification = ($newUsertype == 'M') ? $institutionDetails['anniversary'] : $institutionDetails['spouseanniversary'];
				$updateUserInstitution = ExtendedInstitution::updateUserInstitution($userId, $institutionId, $newUsertype);
				$updateUserInstitutionForDevice = ExtendedInstitution::updateInstitutionForDevice($userId, $institutionId);
				$institutionResponse = ExtendedInstitution::getInstitution($institutionId);
				if($institutionResponse) {
					if($institutionFeatureSettings['feedbackenabled'] == 1) {
						$isActive = true;
						$feedbackresponse = ExtendedInstitution::institutionFeedbackTypes($institutionId,$isActive);
						if($feedbackresponse) {
							foreach ($feedbackresponse as $key => $value) {
								$data = new \stdClass();
								$data->type = (!empty($value['description'])) ? $value['description'] :'General';
								$data->typeId = (!empty($value['feedbacktypeid'])) ? $value['feedbacktypeid'] :'1';
								$resultSet[] = $data;
							}
						}
					}
					// all privileges of the user
					$responsePrivileges = BaseModel::getAllAppUserPrivileges($institutionDetails['memberid'], $institutionId, $newUsertype);
					$featureResponse = ExtendedInstitution::getInstitutionFeatures($institutionId);
					$responseInstitutionDetails = BaseModel::getInstitutionFeatureEnabledValue($institutionId);
					$committee = BaseModel::checkUserAvailableInCommittee($userId, $institutionId);
					if(! empty($featureResponse) && $featureResponse->Status === true && ! empty($featureResponse->value) && (!empty($responseInstitutionDetails && $responseInstitutionDetails->Status === true))) {
						$dashboard = [];
						foreach ($featureResponse->value as $key => $value) {
							if(!$committee && $value['description'] == ExtendedDashboard::CONVERSATION) {
									continue;
							}
							if($responseInstitutionDetails->value == '' || $responseInstitutionDetails->value['feedbackenabled'] == false)
							{
								if($value['description'] == ExtendedDashboard::FEEDBACK) {
									continue;
								}
							}
							if($responseInstitutionDetails->value == '' || $responseInstitutionDetails->value['prayerrequestenabled'] == false)
							{
								if($value['description'] == ExtendedDashboard::PRAYERREQUEST) {
									continue;
								}
							}
							if($responseInstitutionDetails->value == '' || $responseInstitutionDetails->value['moreenabled'] == false)
							{
								if($value['dashboardid'] == ExtendedDashboard::MORE) {
									continue;
								}
							}
							array_push($dashboard, $value['description']);
							$obj = new \stdClass();
							$obj->featureId = (!empty($value['dashboardid'])) ? (int)$value['dashboardid'] :'';
							$dataSet[] = $obj;
							}
						}
							// Near Me
                         	$obj = new \stdClass();
							$obj->featureId = 12;
                            array_push($dataSet, $obj);
                            // Administaration
                                if (!empty($responsePrivileges) && $responsePrivileges->Status === true && $responsePrivileges->value != null && count($responsePrivileges->value) > 0) {
                                            $rolePrivileges = $responsePrivileges->value;
                                            if (array_key_exists(ExtendedPrivilege::MANAGE_PRAYER_REQUESTS, $rolePrivileges) || array_key_exists(ExtendedPrivilege::MANAGE_EVENT_RSVP, $rolePrivileges) || array_key_exists(ExtendedPrivilege::APPROVE_PENDING_MEMBER, $rolePrivileges) || array_key_exists(ExtendedPrivilege::MANAGE_FEEDBACKS, $rolePrivileges) || array_key_exists(ExtendedPrivilege::MANAGE_ALBUM, $rolePrivileges) || array_key_exists(ExtendedPrivilege::MANAGE_FOOD_ORDERS, $rolePrivileges)) {
                                                    // Near Me
						                         	$obj = new \stdClass();
													$obj->featureId = 14;
						                            array_push($dataSet, $obj);     
                                        }
                                }

						$responseAddressType = ExtendedAddresstype::getAddressTypes();
						if($responseAddressType) {
							foreach ($responseAddressType as $key => $value) {
								$adrs = new \stdClass();
								$adrs->addressTypeId = (!empty($value['id'])) ? (string)$value['id'] :'';
								$adrs->addressDescription = (!empty($value['type'])) ? $value['type'] :'';
								$address[] = $adrs;
							}
						}
						$featureData = new \stdClass();
						if(!empty($institutionFeatureSettings)) {
								$featureData->feedbackFeatureAvailable = (isset($institutionFeatureSettings['feedbackenabled'])) ? (bool)$institutionFeatureSettings['feedbackenabled'] : false;
								$featureData->paymentFeatureAvailable = (isset($institutionFeatureSettings['paymentoptionenabled'])) ?  (bool)$institutionFeatureSettings['paymentoptionenabled'] : false;
								$featureData->isDemoEnabled = false;
						} else {
							$featureData->feedbackFeatureAvailable = false;
							$featureData->paymentFeatureAvailable = false;
							$featureData->isDemoEnabled = false;
						}
						$addr = new \stdClass();
						$addr->addressline1 = (!empty($institutionResponse['address1'])) ? $institutionResponse['address1'] :'';
						$addr->addressline2 = (!empty($institutionResponse['address2'])) ? $institutionResponse['address2'] :'';
						$addr->city = (!empty($institutionResponse['district'])) ? $institutionResponse['district'] :'';
						$addr->state = (!empty($institutionResponse['state'])) ? $institutionResponse['state'] :'';
						$addr->pincode = (!empty($institutionResponse['pin'])) ? $institutionResponse['pin'] :'';
						$addr->country = (!empty($institutionResponse['location'])) ? $institutionResponse['location'] :'';
						if($institutionResponse['institutiontypeid'] == 2) {
							$familyUnitmodel = new ExtendedFamilyunit();
							$familyUnitList = $familyUnitmodel->getActiveFamilyUnits($institutionId);
							if($familyUnitList && count($familyUnitList) > 0) {
								foreach ($familyUnitList as $key => $value) {
									$familyUnit[] = [
											'familyUnit' => (!empty($value['description'])) ? $value['description'] :'',
											'familyUnitId' => (!empty($value['familyunitid'])) ? (int)$value['familyunitid'] :0,
											
									];
								}
							}
						}
						$committeeGroupList = ExtendedCommitteegroup::getCommitteeGroup($institutionId);
						$committeePeriodList = ExtendedCommitteePeriod::getCommittePeriod($institutionId);
						
					    if($committeeGroupList) {
							foreach ($committeeGroupList as $key => $value) {
								$periodData = [];
								$grpData[$key]["committeeTypeId"] = (!empty($value['committeegroupid'])) ? (int)$value['committeegroupid'] : 0;
								$grpData[$key]["committeeTypeName"] = (!empty($value['description'])) ? $value['description'] : '';
								$j = 0;
								foreach ($committeePeriodList as $ink => $inv) {
									if ($inv["committeegroupid"] == $value["committeegroupid"]) {
										$periodData[$j] = [
											'periodId' => (!empty($inv['committee_period_id'])) ? (int)$inv['committee_period_id'] : 0,
											'startDate' => (!empty($inv['period_from'])) ? date('d/m/Y',strtotimeNew($inv['period_from'])) : '',
											'endDate' => (!empty($inv['period_to'])) ? date('d/m/Y',strtotimeNew($inv['period_to'])) : '',
										];
										$j++;
									}
								}
								$grpData[$key]['periods'] = $periodData;	
							}
						}
						
						$privilege = [];
						if($responsePrivileges->Status === true && !empty($responsePrivileges->value)) {
							foreach ($responsePrivileges->value as $key => $value) {
								if(in_array($value->name,yii::$app->params['mobileAccessPrivileges'])){
									$privilegeData = new \stdClass();
									$privilegeData->privilegeName = $value->description;
									$privilegeData->privilegeId = $value->name;
									$privileges[] = $privilegeData;
								}
							}
						}
						$ob->memberId = (!empty($institutionDetails['memberid'])) ? (string)$institutionDetails['memberid'] : '';
						$ob->userGroup = ($institutionDetails['membertype'] == 0) ? ($institutionDetails['usertype'] == 'M' ? 0 : 1) : 2;
						$ob->memberName = (!empty($institutionDetails['username'])) ? $institutionDetails['username'] : '';
						$ob->institutionId = (!empty($institutionDetails['institutionid'])) ? (string)$institutionDetails['institutionid'] : '';
						$ob->institutionName = (!empty($institutionResponse['name'])) ? $institutionResponse['name'] : '';
						$ob->institutionLogo = (!empty($institutionResponse['institutionlogo'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$institutionResponse['institutionlogo'] ): '';
						$ob->institutionType = (!empty($institutionResponse['institutiontypeid'])) ? (string)$institutionResponse['institutiontypeid'] : '';
						$ob->isAdsEnabled = true;
						//family units
						$ob->familyUnits = $familyUnit;
						$ob->moreInfoUrl = (!empty($institutionResponse['moreurl'])) ? (bool)$institutionResponse['moreurl'] : false;
						$ob->isContactFilterEnabled = (!empty($institutionFeatureSettings['advancedsearchenabled'])) ? (bool)$institutionResponse['advancedsearchenabled'] : false;
						$ob->isTagsEnabled = (!empty($institutionFeatureSettings['tagcloud'])) ? (bool)$institutionResponse['tagcloud'] : false;
						$ob->phoneNumber1 = (!empty($institutionResponse['phone1'])) ? $institutionResponse['phone1'] : 'Not available';
						$ob->website = (!empty($institutionResponse['url'])) ? $institutionResponse['url'] : '';
						$ob->phoneNumber2 = (!empty($institutionResponse['phone2'])) ? $institutionResponse['phone2'] : 'Not available';
						$ob->email = (!empty($institutionResponse['email'])) ? $institutionResponse['email'] : '';
						//address
						$ob->address = $addr;
						$ob->committeeTypes = $grpData;
						//institutionFeatures
						$ob->institutionFeatures = $featureData;
						$ob->institutionFeatures->feedbackTypes = $resultSet;
						$ob->institutionFeatures->featuresEnabled = $dataSet;
						$ob->institutionFeatures->communicationAddresses = $address;
						//accessPrivilege
						$ob->accessPrivilege = $privileges;
						$ob->communicationAddressId = (!empty($institutionDetails['addresstypeid'])) ? (string)$institutionDetails['addresstypeid'] : '';
						$ob->pushNotificationsEnabled = (!empty($institutionDetails['membernotification'])) ? (bool)$institutionDetails['membernotification'] : false;
						$ob->birthdayNotificationsEnabled = (!empty($institutionDetails['birthday'])) ? (bool)$institutionDetails['birthday'] : false;
						$ob->anniversaryNotificationsEnabled = (!empty($institutionDetails['anniversary'])) ? (bool)$institutionDetails['anniversary'] : false;
						$ob->smsNotificationsEnabled = (!empty($institutionDetails['membersms'])) ? (bool)$institutionDetails['membersms'] : false;
						$ob->emailNotificationsEnabled = (!empty($institutionDetails['memberemail'])) ? (bool)$institutionDetails['memberemail'] : false;

						//used to switch the identity class,don't touch me without proper understandig of identity class iam dirty.
						$updatedUser = ExtendedUserCredentials::findIdentity($userId);
						Yii::$app->user->switchIdentity($updatedUser);	
						
						$this->statusCode = 200;
						$this->message = '';
						$this->data = $ob;
					}
				}
				
			} else {
				$this->statusCode = 500;
				$this->message = 'An error occurred while processing the request';
				$this->data = new \stdClass();
				
			}
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}



