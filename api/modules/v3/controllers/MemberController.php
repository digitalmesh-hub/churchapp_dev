<?php 

namespace api\modules\v3\controllers;

use Yii;
use api\modules\v3\controllers\BaseController;
use api\modules\v3\models\responses\ApiResponse;
use yii\helpers\ArrayHelper;
use common\components\FileuploadComponent;
use common\models\extendedmodels\ExtendedMember;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\extendedmodels\ExtendedInstitutioncontactfilters;
use common\models\extendedmodels\ExtendedFamilyunit;
use common\models\extendedmodels\ExtendedTempmember;
use common\models\extendedmodels\ExtendedMemberadditionalinfo;
use common\models\extendedmodels\ExtendedSettings;
use common\models\extendedmodels\ExtendedTempdependant;
use common\models\extendedmodels\ExtendedDependant;
use common\models\extendedmodels\ExtendedUserMember;
use common\models\extendedmodels\ExtendedCommittee;
use common\models\extendedmodels\ExtendedDeleteMember;
use common\models\extendedmodels\ExtendedTempmemberadditionalinfo;
use common\models\basemodels\BaseModel;
use common\models\extendedmodels\ExtendedTempdependantmail;
use common\models\extendedmodels\ExtendedTempmemberadditionalinfomail;
use common\models\extendedmodels\ExtendedTempmembermail;
use common\components\EmailHandlerComponent;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedProfileupdatenotificationsent;
use common\models\extendedmodels\ExtendedProfileupdatenotification;
use common\models\extendedmodels\ExtendedMemberConnection;
use common\models\basemodels\Member;
use yii\web\UnauthorizedHttpException;
use yii\base\ActionEvent;
use yii\helpers\Url;

class MemberController extends BaseController
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
							'get-staffs' => ['GET'],
							'get-contact-filters' => ['GET'],
							'get-members-list-for-approval' => ['GET'],
							'get-contact-details-for-profile-edit' => ['GET'],
							'get-contact-details' => ['GET'],
							'get-contact-details-by-type' => ['GET'],
							'check-for-contact-update' => ['POST'],
							'get-contacts' => ['GET'],
							'get-member-details-for-approval' => ['GET'],
							'update-profile-picture' => ['POST'],
							'update-dependant-profile-picture' => ['POST'],
							'modify-my-profile-details' => ['POST'],
							'approve-profile-details' => ['POST'],
							'sync-member-connections' => ['POST'],
							'update-member-location' => ['POST']		
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
           if (in_array($event->action->id,['get-members-list-for-approval', 'get-member-details-for-approval'])) {
                $permissionName = "81423355-ec4a-11e6-b48e-000c2990e707";    
                $userMemberId = $user->getUserMember();
               if(!$auth->checkAccess ($userMemberId, $permissionName)){
                    throw new \yii\web\HttpException(401);
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
	 * To get get the list of 
	 * staffs in an institution.
	 */
	public function actionGetStaffs()
	{
		$request = Yii::$app->request;
		$institutionId = $request->get('institutionId');
		$userId = Yii::$app->user->identity->id;
		if ($userId) {
			if ($institutionId) {
				$institutionId = filter_var($institutionId, FILTER_SANITIZE_NUMBER_INT);
				$staffDetails = ExtendedMember::getStaffs($institutionId);
				$staff = [];
				
				if(!empty($staffDetails)) {
					foreach ($staffDetails as $key => $value) {
						$firstName = $value['firstName'];
						$middleName = (!empty($value['middleName'])) ? $value['middleName'] : '';
						$lastName = $value['lastName'];
						if($middleName == ''){
							$fullName = $firstName . ' ' . $lastName;
						}
						else{
							$fullName = $firstName . ' ' . $middleName . ' ' . $lastName;
						}
						$staff[] = [
								'staffId' => (!empty($value['memberid'])) ? $value['memberid'] : '',
								'title' => (!empty($value['description'])) ? $value['description'] : '',
								'name' => $fullName,
								'thumbnail' => (!empty($value['member_pic'])) ? 
									(string) preg_replace('/\s/', "%20",yii::$app->params['imagePath'] . $value['member_pic']) : '',
								'designation' => (!empty($value['designation'])) ? $value['designation'] : '',
								'mobileNumber' => (!empty($value['member_mobile1'])) ? $value['member_mobile1'] : '',
								'mobileNumberCountryCode' => (!empty($value['member_mobile1_countrycode'])) ? $value['member_mobile1_countrycode'] : '',
								'email' => (!empty($value['member_email'])) ? $value['member_email'] : '',
						];
					}
					$data['staffs'] = $staff;
					$this->statusCode = 200;
					$this->message = '';
					$this->data = $data;
					return new ApiResponse($this->statusCode, $this->data, $this->message);
				} else {
					$data['staffs'] = $staff;
					$this->statusCode = 200;
					$this->message = '';
					$this->data = $data;
					return new ApiResponse($this->statusCode, $this->data, $this->message);
				}
			} else {
				$this->statusCode = 500;
				$this->message = 'An error occurred while processing the request';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode, $this->data, $this->message);
			}
			
		} else {
			$this->statusCode = 498;
			$this->message = 'Session invalid';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode, $this->data, $this->message);
		}
	}
	/**
	 * To get the list of all
	 *  available options to 
	 *  refine contact search 
	 *  against an institution.
	 */
	public function actionGetContactFilters()
	{
		$request = Yii::$app->request;
		$institutionId = $request->get('institutionId');
		$userId = Yii::$app->user->identity->id;
		$data = new \stdClass();
		if ($userId) {
			if ($institutionId) {
				$institutionData = ExtendedInstitution::getInstitutionDetails($institutionId);
				$isAdvanceSearch = $institutionData['advancedsearchenabled'];
				if ($isAdvanceSearch) {
					$contactResponse = ExtendedInstitutioncontactfilters::getInstitutionContactFilters($institutionId);
					$institutionDetails = Yii::$app->user->identity;
					$institutionsId = $institutionDetails['institutionid'];
					if(!empty($contactResponse)) {
						foreach ($contactResponse as $key => $value) {
							$filtersOne = [];
							$filterList = [];
							// Family Unit
							$institution = ExtendedInstitution::getInstitutionDetails($institutionsId);
							$institutionType = $institution['institutiontype'];
								if($institutionType == ExtendedInstitution::INSTITUTION_TYPE_CHURCH && $value['contactfilterid'] == ExtendedInstitutioncontactfilters::CONTACT_FILTER_FAMILYUNIT){
									$familyUnitObj = new ExtendedFamilyunit();
									$familyUnitList = $familyUnitObj->getActiveFamilyUnits($institutionId);
									if($familyUnitList) {
										foreach ($familyUnitList as $key => $data) {
											$result = [
													'optionId' => (!empty($data['familyunitid']))? (int)$data['familyunitid']:'',
													'optionText' => (!empty($data['description']))? $data['description']:'',
											];
											array_push($filterList,$result);
										}
									}
								}
								if($value['contactfilterid'] == ExtendedInstitutioncontactfilters::CONTACT_FILTER_BLOODGROUP) {
									$bloodGroupList = [ 
										"O -ve" => "1", 
                                    	"O +ve" => "2" ,
                                     	"A -ve" => "3" ,
                                    	"A +ve" => "4" ,
                                     	"B -ve" => "5" ,
                                    	"B +ve" => "6" ,
                                    	"AB -ve" => "7" ,
                                     	"AB +ve" => "8" 
                                    ];
                             
                                  foreach ($bloodGroupList as $k => $v) {
                                  	$result = [
												'optionId' => (int)$v,
												'optionText' => $k
											];
									array_push($filterList,$result);
                                  }

								}

								$filterMinValue = 0;
								$filterMaxValue = 0;
								if($institutionType == ExtendedInstitution::INSTITUTION_TYPE_EDUCATION && $value['contactfilterid'] == ExtendedInstitutioncontactfilters::CONTACT_FILTER_BATCH) {
									$filterMinValue = BaseModel :: getInstitutionMinBatch($institutionsId);
									$filterMaxValue = date("Y");
								}

							
								$filterData[] = [
									'filterType' => (isset($value['contactfilterid']))? (int)$value['contactfilterid']:'',
									'filterName' => (isset($value['description']))? (string)$value['description']:'',
									'filterOptionType' => (isset($value['filteroptiontypeid']))? (int)$value['filteroptiontypeid']:'',
									'filterOptions' 	=> $filterList,
									'filterMinValue' 	=> $filterMinValue,
									'filterMaxValue' 	=> (int)$filterMaxValue
								];
							}
							$data = [
								'filters' => $filterData
							];
					}
					$this->statusCode = 200;
					$this->message = '';
					$this->data = $data;
					return new ApiResponse($this->statusCode, $this->data, $this->message);
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
	 * To get the list of pending approval 
	 * request of the members.
	 */
	public function actionGetMembersListForApproval()
	{
		$userId = Yii::$app->user->identity->id;
		$institutionId = Yii::$app->user->identity->institutionid;
		if ($userId) {
			$pendingMembers = ExtendedTempmember::getPendingMembersData($userId);
			if (!empty($pendingMembers)) {
				$pendingList = [];
				foreach ($pendingMembers as $key => $value) {
				    $firstName = (!empty($value['temp_firstName'])) ? $value['temp_firstName'] :'';
				    $middleName = (!empty($value['temp_middleName'])) ? $value['temp_middleName'] :'';
				    $lastName = (!empty($value['temp_lastName'])) ? $value['temp_lastName'] :'';
					$result  = [
							'memberId' => (!empty($value['temp_memberid'])) ? (int)$value['temp_memberid'] :0,
							'memberTitle' => (!empty($value['TempMemberTitleDescription'])) ? $value['TempMemberTitleDescription'] :'',
							'memberName' => $firstName.' '.$middleName.' '.$lastName,
							'memberImage' => (!empty($value['temp_memberImageThumbnail']))? preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$value['temp_memberImageThumbnail']) :'',
							'requestedDate' => (!empty($value['temp_createddate']))? date_format(date_create($value['temp_createddate']),Yii::$app->params['dateFormat']['viewDateFormat']) :'',
							'requestedTime' =>  (!empty($value['temp_createddate']))? date_format(date_create($value['temp_createddate']),Yii::$app->params['dateFormat']['time12Hr']) :'',
							'institutionId' => (!empty($value['temp_institutionid'])) ? $value['temp_institutionid'] :'',
							'institutionName' => (!empty($value['institutionname'])) ? $value['institutionname'] :'',
					];
					array_push($pendingList, $result);
				}
				$data = [
						'members' => $pendingList
				];
				$this->statusCode = 200;
				$this->message = '';
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data,$this->message);
				
			} else {
				$this->statusCode = 200;
				$this->message = '';
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
	 * To retrieve the details 
	 * of a particular member.
	 */
	public function actionGetContactDetailsForProfileEdit()
	{
		$request = Yii::$app->request;
		$memberId = $request->get('memberId');
		$user = Yii::$app->user->identity;
		$userId = $user->id;
		$institutionId = $user->institutionid;
		$tempResponse = [];
		$response = [];
		$currentMember = "select memberid from usermember where userid=:userid and institutionid =:institutionid";
		$params = [
			':userid' => $userId,
			':institutionid' => $institutionId
		];
		$currentMemberId = Yii::$app->db->createCommand ( $currentMember )->bindValues($params)->queryScalar();
		$institutionMemberId = ExtendedMember::getInstitutionMemberId($memberId,$institutionId);
		if ($userId) {
			$institutionType = Yii::$app->user->identity->institution->institutiontype;
			$tempResponse = ExtendedMember::getTempMemberBymemberid($memberId);
			if(!$tempResponse) {
				$response = ExtendedMember::getMemberById($memberId);
				$responseAdditionalInfo = ExtendedMemberadditionalinfo::getAdditionalInfo($memberId);
				$memberTag = $responseAdditionalInfo['tagcloud'] ?? '';
			}else{
                $responseAdditionalInfo = ExtendedTempmemberadditionalinfo::find()->where(['memberid'=>$memberId])->one();
                $memberTag = $responseAdditionalInfo['temptagcloud'] ?? '';
            }
			$responseSettings = ExtendedSettings::getMemberSettings($memberId);

			if (!empty($tempResponse)) {
				$data = new \stdClass();
				$membershipData = new \stdClass();
				$businessData = new \stdClass();
				$alternatePhoneData = new \stdClass();
				$data->memberId = $memberId?$memberId:'';
				$membershipData->membershipId = (!empty($tempResponse['temp_memberno'])) ? $tempResponse['temp_memberno']:'';
				$membershipData->membershipType = (!empty($tempResponse['temp_membershiptype'])) ? $tempResponse['temp_membershiptype']:'';
				$membershipData->memberSince = (!empty($tempResponse['temp_membersince'])) ? $tempResponse['temp_membersince']:'';
				$data->membershipDetails = $membershipData;
				
				$memberCountryCode = '';
				$membermobileNumber = '';
				$spousemobilecountrycode = '';
				$spousemobileNumber = '';
				if($currentMemberId != $memberId) { // check whether it is current user
					$mobileCountryCode = (!empty($tempResponse['temp_member_mobile1_countrycode'])) ? $tempResponse['temp_member_mobile1_countrycode'] : "";
					$mobileNumber = (!empty($tempResponse['temp_member_mobile1'])) ? $tempResponse['temp_member_mobile1'] : "";
					$spousemobilecountrycode = (!empty($tempResponse['temp_spouse_mobile1_countrycode'])) ? $tempResponse['temp_spouse_mobile1_countrycode'] : "";
					$spousemobileNumber = (!empty($tempResponse['temp_spouse_mobile1'])) ?
						$tempResponse['temp_spouse_mobile1'] : "";

				} else {
					$mobileCountryCode = (!empty($tempResponse['temp_member_mobile1_countrycode'])) ? $tempResponse['temp_member_mobile1_countrycode'] : "";
					$mobileNumber = (!empty($tempResponse['temp_member_mobile1'])) ? $tempResponse['temp_member_mobile1'] : "";
					$spousemobilecountrycode = (!empty($tempResponse['temp_spouse_mobile1_countrycode'])) ? $tempResponse['temp_spouse_mobile1_countrycode'] : "";
					$spousemobileNumber = (!empty($tempResponse['temp_spouse_mobile1'])) ?
						$tempResponse['temp_spouse_mobile1'] : "";
				}
				
				//Member Details
				$memberDetails = $this->tempMemberData($tempResponse,$mobileCountryCode,$mobileNumber,$institutionType,$memberTag);

				$data->memberDetails = $memberDetails;

				$data->headOfFamily = isset($tempResponse['head_of_family']) ? $tempResponse['head_of_family'] : 'm';
				
				//Spouse Details
				$spouseDetails = $this->tempSpouseData($tempResponse,$spousemobilecountrycode,$spousemobileNumber);
				$data->spouseDetails = $spouseDetails;
				
				//Residential address
				$data->hasResidentialAddress = (!empty($tempResponse['temp_residence_address1']))? true:false;
				$residentialData = $this->residentialData($tempResponse);
				$data->residentialAddress = $residentialData;
				
				//Office details
				$data->hasBusinessDetails = (!empty($tempResponse['temp_member_business_Phone1']))  ||
											(!empty($tempResponse['temp_member_business_Phone2'])) ||
											(!empty($tempResponse['temp_businessemail'])) ||
											(!empty($tempResponse['temp_business_address1']))||
											(!empty($tempResponse['temp_business_address2']))||
											(!empty($tempResponse['temp_business_district'])) ||
											(!empty($tempResponse['temp_business_state'])) ||
											(!empty($tempResponse['temp_business_pincode'])) ? true : false;
				$officeData = $this->officeData($tempResponse);
				$businessData->officePhone = $officeData;
				$data->businessDetails = $businessData;
				
				//alternative Office Phone details
				/* 	$alternatePhoneData->countryCode = (!empty($tempResponse['temp_member_business_phone3_countrycode']))?$tempResponse['temp_member_business_phone3_countrycode']:'';
				$alternatePhoneData->areaCode = (!empty($tempResponse['temp_member_business_phone3_areacode']))?$tempResponse['temp_member_business_phone3_areacode']:'';
				$alternatePhoneData->phoneNumber = (!empty($tempResponse['temp_member_business_Phone3']))?$tempResponse['temp_member_business_Phone3']:'';
				$businessData->alternativeOfficePhone = $alternatePhoneData;
				$data->businessDetails = $businessData;  */
				
				$alternatePhoneData= $this->alternatePhone($tempResponse);
				$businessData->alternativeOfficePhone = $alternatePhoneData;
				$data->businessDetails = $businessData;

				//business details
				$businessData->email = (!empty($tempResponse['temp_businessemail']))?$tempResponse['temp_businessemail']:'';
				$businessData->addressLine1 = (!empty($tempResponse['temp_business_address1']))?$tempResponse['temp_business_address1']:'';
				$businessData->addressLine2 = (!empty($tempResponse['temp_business_address2']))?$tempResponse['temp_business_address2']:'';
				$businessData->city = (!empty($tempResponse['temp_business_district']))?$tempResponse['temp_business_district']:'';
				$businessData->state = (!empty($tempResponse['temp_business_state']))?$tempResponse['temp_business_state']:'';
				$businessData->pinCode = (!empty($tempResponse['temp_business_pincode']))?$tempResponse['temp_business_pincode']:'';
				
				$responseDependants = ExtendedTempdependant::getEditMemberTempDependants($memberId);
				$dependantArray =[];
				if ($responseDependants) {
					$dependantArray = $this->dependantData($responseDependants, true);
				} else {
					$dependantObj = new ExtendedDependant();
					$responseDependants = $dependantObj->getDependants($memberId);
					if ($responseDependants) {
						foreach ($responseDependants as $model) {
						$result = [
								'dependantId' => (!empty($model['id'])) ? $model['id']:'',
								'dependantName' => (!empty($model['dependantname']))?$model['dependantname']:'',
								'dependantMobileCountryCode' => (!empty($model['dependantmobilecountrycode']))?$model['dependantmobilecountrycode']:'',
								'dependantMobile' => (!empty($model['dependantmobile']))?$model['dependantmobile']:'',
								'dependantDob' => (!empty($model['dob']))? date_format(date_create($model['dob']),Yii::$app->params['dateFormat']['viewDateFormat']):'',
								'dependantRelation' => (!empty($model['relation']))?$model['relation']:'',
								'dependantTitleId' => (!empty($model['dependanttitleid']))?
									(int)$model['dependanttitleid']:0,
								'dependantTitle' => (!empty($model['dependanttitle']))?$model['dependanttitle']:'',
								'dependantMaritalStatus' => (!empty($model['ismarried']))?(int)$model['ismarried']: 0,
								'dependantSpouseId' => (!empty($model['dependantspouseid']))?$model['dependantspouseid']:'',
								'dependantSpouseTitle' => (!empty($model['spousetitle']))?$model['spousetitle']:'',
								'dependantSpouseTitleId' => (!empty($model['spousetitleid']))?(int)$model['spousetitleid']:0,
								'dependantSpouseName' => (!empty($model['spousename']))?$model['spousename']:'',
								'dependantSpouseMobileCountryCode' => (!empty($model['dependantspousemobilecountrycode']))?$model['dependantspousemobilecountrycode']:'',
								'dependantSpouseMobile' => (!empty($model['dependantspousemobile']))?$model['dependantspousemobile']:'',
								'dependantSpouseDateOfBirth' => (!empty($model['spousedob']))?date_format(date_create($model['spousedob']),Yii::$app->params['dateFormat']['viewDateFormat']):'',
								'dependantWeddingAnniversary' => (!empty($model['weddinganniversary']))? date_format(date_create($model['weddinganniversary']),Yii::$app->params['dateFormat']['viewDateFormat']):'',
								'dependantImage' => (!empty($model['tempimage'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$model['tempimage']) : '',
								'dependantImageThumbnail' => (!empty($model['tempimagethumbnail'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$model['tempimagethumbnail']) : '',
								'dependantSpouseImage' => (!empty($model['tempdependantspouseimage'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$model['tempdependantspouseimage']) : '',
								'dependantSpouseImageThumbnail' => (!empty($model['tempdependantspouseimagethumbnail'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$model['tempdependantspouseimagethumbnail']) : '',
						];
						array_push($dependantArray,$result);
						}
					}
				}
				$data->dependants = $dependantArray;
				$data->hasDependants = count($responseDependants) > 0 ? true : false;
			
				$this->statusCode = 200;
				$this->message = '';
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data,$this->message);
			} else if(!empty($response)) {
				$data = new \stdClass();
				$membershipData = new \stdClass();
				$businessData = new \stdClass();
				$alternatePhoneData = new \stdClass();
				$data->memberId 				= $memberId ? $memberId : '';
				$membershipData->membershipId 	= (!empty($response['memberno'])) ? $response['memberno']:'';
				$membershipData->membershipType = (!empty($response['membershiptype'])) ? $response['membershiptype']:'';
				$membershipData->memberSince 	= (!empty($response['membersince'])) ? $response['membersince']:'';
				$membershipData->batch 			= (!empty($response['batch'])) ? $response['batch']:'';
				
				$data->membershipDetails 		= $membershipData;
				
				$memberCountryCode = '';
				$membermobileNumber = '';
				$spousemobilecountrycode = '';
				$spousemobileNumber = '';
				if($currentMemberId != $memberId) { // check whether it is current user
				
					/*$mobileCountryCode = (!empty($responseSettings['membermobilePrivacyEnabled'])) ? "" : $response['member_mobile1_countrycode'];
					$mobileNumber = (!empty($responseSettings['membermobilePrivacyEnabled'])) ? "Private": $response['member_mobile1'];
					$spousemobilecountrycode = (!empty($responseSettings['spousemobilePrivacyEnabled'])) ? "" : $response['spouse_mobile1_countrycode'];
					$spousemobileNumber = (!empty($responseSettings['spousemobilePrivacyEnabled'])) ? "Private": $response['spouse_mobile1'];*/

					$mobileCountryCode = (!empty($response['member_mobile1_countrycode'])) ? $response['member_mobile1_countrycode']: "";
					$mobileNumber = (!empty($response['member_mobile1'])) ? $response['member_mobile1'] : "";
					$spousemobilecountrycode = (!empty($response['spouse_mobile1_countrycode'])) ? $response['spouse_mobile1_countrycode'] : "";
					$spousemobileNumber = (!empty($response['spouse_mobile1']))? $response['spouse_mobile1'] : "";
				} else {
					$mobileCountryCode = (empty($response['member_mobile1_countrycode'])) ? "": $response['member_mobile1_countrycode'];
					$mobileNumber = (!empty($response['member_mobile1'])) ? $response['member_mobile1'] : "";
					$spousemobilecountrycode = (empty($response['spouse_mobile1_countrycode']))? "" : $response['spouse_mobile1_countrycode'];
					$spousemobileNumber = (!empty($response['spouse_mobile1'])) ? $response['spouse_mobile1'] : "";
				}
				
				//Member Details
				$memberDetails = $this->memberData($response,$mobileCountryCode,$mobileNumber,$institutionType,$memberTag);

				$data->memberDetails = $memberDetails;
				$data->headOfFamily = isset($response['head_of_family']) ? $response['head_of_family'] : 'm';
				//Spouse Details
				$spouseDetails = $this->spouseData($response,$spousemobilecountrycode,$spousemobileNumber);
				$data->spouseDetails = $spouseDetails;
				
				//Residential address
				$data->hasResidentialAddress = (!empty($response['residence_address1']))? true:false;
				
				$residentialData = new \stdClass();
				$residentialData->addressLine1 = (!empty($response['residence_address1']))?$response['residence_address1']:'';
				$residentialData->addressLine2 = (!empty($response['residence_address2']))?$response['residence_address2']:'';
				$residentialData->city = (!empty($response['residence_district']))?$response['residence_district']:'';
				$residentialData->state = (!empty($response['residence_state']))?$response['residence_state']:'';
				$residentialData->pinCode = (!empty($response['residence_pincode']))?$response['residence_pincode']:'';
				
				$data->residentialAddress = $residentialData;
				
				//Office details
				$data->hasBusinessDetails = (!empty($response['member_business_Phone1']))  ||
											(!empty($response['member_business_Phone2'])) ||
											(!empty($response['businessemail'])) ||
											(!empty($response['business_address1']))||
											(!empty($response['business_address2']))||
											(!empty($response['business_district'])) ||
											(!empty($response['business_state'])) ||
											(!empty($response['business_pincode'])) ? true : false;

				$officeData = $this->officeData($response);
				
				$officeData = new \stdClass();
				$officeData->countryCode = (!empty($response['member_business_phone1_countrycode']))?$response['member_business_phone1_countrycode']:'';
				$officeData->areaCode = (!empty($response['member_business_phone1_areacode']))?$response['member_business_phone1_areacode']:'';
				$officeData->phoneNumber = (!empty($response['member_musiness_Phone1']))?$response['member_musiness_Phone1']:'';
				
				$businessData->officePhone = $officeData;
				$data->businessDetails = $businessData;
				
				//alternative Office Phone details
				$alternatePhoneData->countryCode = (!empty($response['member_business_phone3_countrycode']))?$response['member_business_phone3_countrycode']:'';
				$alternatePhoneData->areaCode = (!empty($response['member_business_phone3_areacode']))?$response['member_business_phone3_areacode']:'';
				$alternatePhoneData->phoneNumber = (!empty($response['member_business_Phone3']))?$response['member_business_Phone3']:'';
				$businessData->alternativeOfficePhone = $alternatePhoneData;
				$data->businessDetails = $businessData;
				
				//business details
				$businessData->email = (!empty($response['businessemail']))?$response['businessemail']:'';
				$businessData->addressLine1 = (!empty($response['business_address1']))?$response['business_address1']:'';
				$businessData->addressLine2 = (!empty($response['business_address2']))?$response['business_address2']:'';
				$businessData->city = (!empty($response['business_district']))?$response['business_district']:'';
				$businessData->state = (!empty($response['business_state']))?$response['business_state']:'';
				$businessData->pinCode = (!empty($response['business_pincode']))?$response['business_pincode']:'';
				
				$responseDependants = ExtendedTempdependant::getEditMemberTempDependants($memberId);
				$dependantArray = [];

				if ($responseDependants) {
					$dependantArray = $this->dependantData($responseDependants);
				} else {
					$dependantObj = new ExtendedDependant();
					$responseDependants = $dependantObj->getDependants($memberId);
					if ($responseDependants) {
						foreach ($responseDependants as $model) {
							if(empty($model['dependantname'])){
								continue;
							}
							$result = [
									'dependantId' => (!empty($model['id']))?$model['id']:'',
									'dependantName' => (!empty($model['dependantname']))?$model['dependantname']:'',
									'dependantMobileCountryCode' => (!empty($model['dependantmobilecountrycode']))?$model['dependantmobilecountrycode']:'',
									'dependantMobile' => (!empty($model['dependantmobile']))?$model['dependantmobile']:'',
									'dependantDob' => (!empty($model['dob']))? date_format(date_create($model['dob']),Yii::$app->params['dateFormat']['viewDateFormat']):'',
									'dependantRelation' => (!empty($model['relation']))?$model['relation']:'',
									'dependantTitleId' => (!empty($model['dependanttitleid']))?(int)$model['dependanttitleid']:0,
									'dependantTitle' => (!empty($model['dependanttitle']))?$model['dependanttitle']:'',
									'dependantMaritalStatus' => (!empty($model['ismarried']))? (int) $model['ismarried']:0,
									'dependantSpouseId' => (!empty($model['dependantspouseid']))?$model['dependantspouseid']:'',
									'dependantSpouseTitle' => (!empty($model['spousetitle']))?$model['spousetitle']:'',
									'dependantSpouseTitleId' => (!empty($model['spousetitleid']))?(int) $model['spousetitleid']:0,
									'dependantSpouseName' => (!empty($model['spousename']))?$model['spousename']:'',
									'dependantSpouseMobileCountryCode' => (!empty($model['dependantspousemobilecountrycode']))?$model['dependantspousemobilecountrycode']:'',
									'dependantSpouseMobile' => (!empty($model['dependantspousemobile']))?$model['dependantspousemobile']:'',
									'dependantSpouseDateOfBirth' => (!empty($model['spousedob']))?date_format(date_create($model['spousedob']),Yii::$app->params['dateFormat']['viewDateFormat']):'',
									'dependantWeddingAnniversary' => (!empty($model['weddinganniversary']))? date_format(date_create($model['weddinganniversary']),Yii::$app->params['dateFormat']['viewDateFormat']):'',
									'dependantImage' => (!empty($model['dependantimage'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$model['dependantimage']) : '',
									'dependantImageThumbnail' => (!empty($model['dependantthumbnailimage'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$model['dependantthumbnailimage']) : '',
									'dependantSpouseImage' => (!empty($model['dependantspouseimage'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$model['dependantspouseimage']) : '',
									'dependantSpouseImageThumbnail' => (!empty($model['dependantspousethumbnailimage'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$model['dependantspousethumbnailimage']) : '',
							];
							array_push($dependantArray,$result);
						}
					}
				}
				$data->hasDependants = count($responseDependants) > 0 ? true:false;
				$data->dependants = $dependantArray;
				
				$this->statusCode = 200;
				$this->message = '';
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data,$this->message);

			} else {
				$this->statusCode = 200;
				$this->message = '';
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
	 * To retrieve the details 
	 * of a particular member.
	 */
	public function actionGetContactDetails()
	{
		$request = Yii::$app->request;
		$memberId = $request->get('memberId');
		$user = Yii::$app->user->identity;
		$userId = $user->id;
		$institutionId = $user->institutionid;
		$currentMember = "select memberid from usermember where userid=:userid";
		$params = [':userid' => $userId];
		$currentMemberId = Yii::$app->db->createCommand ( $currentMember )->bindValues($params)->queryScalar();
		$institutionMemberId = ExtendedMember::getInstitutionMemberId($memberId,$institutionId);
		$memberId = $institutionMemberId;
		if ($userId) {
			$institutionType = $user->institution->institutiontype;
			$response = ExtendedMember::getMemberById($memberId);
			$responseSettings = ExtendedSettings::getMemberSettings($memberId);
			if ($responseSettings) {
				$settings = [];
				$settings = $responseSettings; 	
			} else {
				$this->statusCode = 500;
				$this->message = 'Member no longer exists';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode, $this->data, $this->message);
			}
			if (!empty($response)) {
				$data = new \stdClass();
				$membershipData = new \stdClass();
				$businessData = new \stdClass();
				$dependantObj = new ExtendedDependant();
				$responseDependants = $dependantObj->getDependants($memberId);
				$data->memberId = $memberId ? $memberId:'';
				
					$membershipData->membershipId = (!empty($response['memberno'])) ? $response['memberno']:'';
					$membershipData->membershipType = (!empty($response['membershiptype'])) ? $response['membershiptype']:'';
					$membersince = (!empty($response['membersince'])) ?  (string)date(yii::$app->params['dateFormat']['viewDateFormat'],strtotimeNew($response['membersince']) ) : '';
					$membershipData->memberSince = ($institutionType == 4) ? $response['batch'] : $membersince;
					$membershipData->batch 		 = !empty($response['batch']) ? $response['batch'] : '';
					$data->membershipDetails = $membershipData;
					
					// Head of family
					$data->headOfFamily = isset($response['head_of_family']) ? $response['head_of_family'] : 'm';
					
					$memberCountryCode = '';
					$membermobileNumber = '';
					$spousemobilecountrycode = '';
					$spousemobileNumber = '';
					
					if ($currentMemberId != $memberId) { // check whether it is current user 
						$mobileCountryCode = ($settings['membermobilePrivacyEnabled']) ? "" : $response['member_mobile1_countrycode'];
						$mobileNumber = ($settings['membermobilePrivacyEnabled']) ? "Private": $response['member_mobile1'];
						$spousemobilecountrycode = ($settings['spousemobilePrivacyEnabled']) ? "" : $response['spouse_mobile1_countrycode'];
						$spousemobileNumber = ($settings['spousemobilePrivacyEnabled']) ? "Private": $response['spouse_mobile1'];
					} else {
						$mobileCountryCode = (empty($response['member_mobile1_countrycode']))? "":$response['member_mobile1_countrycode'];
						$mobileNumber = (!empty($response['member_mobile1'])) ? $response['member_mobile1'] : '';
						$spousemobilecountrycode = (empty($response['spouse_mobile1_countrycode']))? "":$response['spouse_mobile1_countrycode'];
						$spousemobileNumber = (!empty($response['spouse_mobile1'])) ? $response['spouse_mobile1'] : '';
					}
					//Member Details
					$memberDetails = $this->memberDataForContact($response,$mobileCountryCode,$mobileNumber,$institutionType);
					$data->memberDetails = $memberDetails;
					
					//Spouse Details
					$spouseDetails = $this->spouseDataForContact($response,$spousemobilecountrycode,$spousemobileNumber);
					$data->spouseDetails = $spouseDetails;
					
					//Residential address
					$data->hasResidentialAddress = (!empty($response['residence_address1']))? true:false;
					$residentialData = $this->residentialDataForContact($response);
					$data->residentialAddress = $residentialData;
					
					//Office details
					$data->hasBusinessDetails = (!empty($response['member_musiness_Phone1']))  ||
					(!empty($response['member_business_Phone2'])) ||
					(!empty($response['businessemail'])) ||
					(!empty($response['business_address1']))||
					(!empty($response['business_address2']))||
					(!empty($response['business_district'])) ||
					(!empty($response['business_state'])) ||
					(!empty($response['business_pincode'])) ? true : false;
					$officeData = $this->officeDataForContact($response);
					$businessData->officePhone = $officeData;
					$data->businessDetails = $businessData;
					
					//alternative Office Phone details
					$alternatePhoneData= $this->alternatePhoneForEdit($response);
					$businessData->alternativeOfficePhone = $alternatePhoneData;
					$data->businessDetails = $businessData;
					
					//business details
					$businessData->email = (!empty($response['businessemail']))?$response['businessemail']:'';
					$businessData->addressLine1 = (!empty($response['business_address1']))?$response['business_address1']:'';
					$businessData->addressLine2 = (!empty($response['business_address2']))?$response['business_address2']:'';
					$businessData->city = (!empty($response['business_district']))?$response['business_district']:'';
					$businessData->state = (!empty($response['business_state']))?$response['business_state']:'';
					$businessData->pinCode = (!empty($response['business_pincode']))?$response['business_pincode']:'';
					
					$data->hasDependants = count($responseDependants) > 0 ? true:false;
					if ($responseDependants) {
						$dependantsArray = $this->dependantDataForContact($responseDependants);
						$data->dependants = $dependantsArray;
					} else {
						$data->dependants = [];
					}
					
					// Get member connections
					$memberConnections = ExtendedMemberConnection::getMemberConnectionsWithDetails($memberId);
					$connectionsArray = [];
					if ($memberConnections) {
						foreach ($memberConnections as $connection) {
							$connectionsArray[] = [
								'memberId' => (string)$connection['memberId'],
								'membershipNumber' => (!empty($connection['membershipNumber'])) ? $connection['membershipNumber'] : ''
							];
						}
					}
					$data->memberConnections = $connectionsArray;
					
					$this->statusCode = 200;
					$this->message = '';
					$this->data = $data;
					return new ApiResponse($this->statusCode, $this->data,$this->message);
			} else {
					$this->statusCode = 200;
					$this->message = '';
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
	* Member data for contact 
	*/
	protected function memberDataForContact($response,$mobileCountryCode,$mobileNumber,$institutionType)
	{
			$memberDetails = new \stdClass();
			$memberDetails->memberTitle = (!empty($response['membertitledescription']))?  $response['membertitledescription']:'';
			$memberDetails->memberTitleId = (!empty($response['membertitle']))?$response['membertitle']:"0";
			$memberDetails->memberName = $response['firstName'].' '.$response['middleName'].' '.$response['lastName'];
			$memberDetails->memberName = (string)preg_replace('!\s+!', ' ', $memberDetails->memberName);
			$memberDetails->memberFirstName = (!empty($response['firstName']))?$response['firstName']:'';
			$memberDetails->memberMiddleName = (!empty($response['middleName']))?$response['middleName']:'';
			$memberDetails->memberLastName = (!empty($response['lastName']))?$response['lastName']:'';
			$memberDetails->memberNickName = (!empty($response['membernickname']))? $response['membernickname']:'';
			$memberDetails->memberImage = (!empty($response['member_pic']))?(string)preg_replace('/\s/', "%20",  yii::$app->params['imagePath'].$response['member_pic']):'';
			$memberDetails->memberImageThumbnail = (!empty($response['memberImageThumbnail']))?(string)preg_replace('/\s/', "%20",  yii::$app->params['imagePath'].$response['memberImageThumbnail']):'';
			$memberDetails->memberDob = (!empty($response['member_dob']))?date_format(date_create($response['member_dob']),Yii::$app->params['dateFormat']['viewDateFormat']):'';
			$memberDetails->mobileCountryCode = (empty($mobileNumber))?'':$mobileCountryCode;
			$memberDetails->mobileNumber = $mobileNumber;
			$memberDetails->landLineCountryCode = (!empty($response['member_residence_phone1_countrycode']))?$response['member_residence_phone1_countrycode']:'';
			$memberDetails->landLineAreaCode = (!empty($response['member_residence_Phone1_areacode']))?$response['member_residence_Phone1_areacode']:'';
			$memberDetails->landLineNumber = (!empty($response['member_residence_Phone1']))?$response['member_residence_Phone1']:'';
			$memberDetails->email = (!empty($response['member_email']))?$response['member_email']:'';
			$memberDetails->profession = (!empty($response['occupation']))?$response['occupation']:'';
			$memberDetails->isChurch = ($institutionType == 2)?true:false;
			$memberDetails->homeChurchName = (!empty($response['homechurch']))?$response['homechurch']:'';
			$memberDetails->committeeDesignation = (!empty($response['familyunit']))?(string)$response['familyunit']:'';
			$memberDetails->familyUnit = (!empty($response['familyunit']))?(string)$response['familyunit']:'';
			$memberDetails->zone = (!empty($response['zone']))?(string)$response['zone']:'';
			$memberDetails->zoneId = (!empty($response['zone_id']))?(int)$response['zone_id']:'';
			$memberDetails->bloodGroup = (!empty($response['memberbloodgroup']))?$response['memberbloodgroup']:'';
			$locationArray = [];
			if (!empty($response['location'])) {
				$locationArray = json_decode($response['location'], true);
			}
			$location = ['latitude' => '', 'longitude' => ''];
			if (is_array($locationArray)) {
				$location = ['latitude' => $locationArray['latitude'] ?? '', 'longitude' => $locationArray['longitude'] ?? ''];
			}
			$memberDetails->location =  $location;
			$memberDetails->viewMemberResidentialAddressOnMap =  true;
			$memberDetails->tags = '';
			if (!empty($response['membertag'])){
			    $membertag = $response['membertag'];
			    $searchString = ',';
			    if( strpos($membertag, $searchString) !== false ) {
			        $myArray = explode(',', $membertag);
			        $tagMember = "";
			        foreach ($myArray as $tag) {
			            $tagMember =  $tagMember . "" . $tag . ",";
			        }
			        $tagMember = rtrim($tagMember,',');
			        $memberDetails->tags = $tagMember;
			    }
			    else{
			        $memberDetails->tags =  $membertag;
			    }
			    
			}
			
			return $memberDetails;
	}
	/**
	 * Member data
	 */
	protected function tempMemberData($response,$mobileCountryCode,$mobileNumber,$institutionType,$memberTag)
	{   
		$memberDetails = new \stdClass();
		$memberDetails->memberTitle = (!empty($response['membertitle']))?$response['membertitle']:'';
		$memberDetails->memberTitleId = (!empty($response['temp_membertitle']))?(int)$response['temp_membertitle']:"0";
		$memberDetails->memberName = $response['temp_firstName'].' '.$response['temp_middleName'].' '.$response['temp_lastName'];
		$memberDetails->memberName = (string)preg_replace('!\s+!', ' ', $memberDetails->memberName);
		$memberDetails->memberFirstName = (!empty($response['temp_firstName']))?$response['temp_firstName']:'';
		$memberDetails->memberMiddleName = (!empty($response['temp_middleName']))?$response['temp_middleName']:'';
		$memberDetails->memberLastName = (!empty($response['temp_lastName']))?$response['temp_lastName']:'';
		$memberDetails->memberNickName = (!empty($response['temp_membernickname']))?$response['temp_membernickname']:'';
		$memberDetails->memberImage = (!empty($response['temp_member_pic']))?(string)preg_replace('/\s/', "%20",  yii::$app->params['imagePath'].$response['temp_member_pic']):'';
		$memberDetails->memberImageThumbnail = (!empty($response['temp_memberImageThumbnail']))?(string)preg_replace('/\s/', "%20",  yii::$app->params['imagePath'].$response['temp_memberImageThumbnail']):'';
		$memberDetails->memberDob = (!empty($response['temp_member_dob']))?date_format(date_create($response['temp_member_dob']),Yii::$app->params['dateFormat']['viewDateFormat']):'';
		$memberDetails->mobileCountryCode = (empty($mobileCountryCode))?'':$mobileCountryCode;
		$memberDetails->mobileNumber = $mobileNumber;
		$memberDetails->landLineCountryCode = (!empty($response['temp_member_residence_Phone1_countrycode']))?$response['temp_member_residence_Phone1_countrycode']:'';
		$memberDetails->landLineAreaCode = (!empty($response['temp_member_residence_Phone1_areacode']))?$response['temp_member_residence_Phone1_areacode']:'';
		$memberDetails->landLineNumber = (!empty($response['temp_member_residence_Phone1']))?$response['temp_member_residence_Phone1']:'';
		$memberDetails->email = (!empty($response['temp_member_email']))?$response['temp_member_email']:'';
		$memberDetails->profession = (!empty($response['temp_occupation']))?$response['temp_occupation']:'';
		$memberDetails->isChurch = ($institutionType == 2)?true:false;
		$memberDetails->homeChurchName = (!empty($response['temp_homechurch']))?$response['temp_homechurch']:'';
		$memberDetails->committeeDesignation = (!empty($response['committeeDesignation']))?$response['committeeDesignation']:'';
		$memberDetails->familyUnit = (!empty($response['familyunitid']))?(string)$response['familyunitid']:'';
		$memberDetails->bloodGroup = (!empty($response['tempmemberBloodGroup']))?$response['tempmemberBloodGroup']:'';
		$memberDetails->tags = (!empty($memberTag))?$memberTag:'';
		$memberDetails->zone = (!empty($response['zone']))?(string)$response['zone']:'';
		$memberDetails->zoneId = (!empty($response['zone_id']))?(int)$response['zone_id']:'';
		$locationArray = [];
		if (!empty($response['location'])) {
			$locationArray = json_decode($response['location'], true);
		}
		$location = ['latitude' => '', 'longitude' => ''];
		if (is_array($locationArray)) {
			$location = ['latitude' => $locationArray['latitude'] ?? '', 'longitude' => $locationArray['longitude'] ?? ''];
		}
		$memberDetails->viewMemberResidentialAddressOnMap = true;
		$memberDetails->location = $location;
		return $memberDetails;
	} 
	/**
	 * Member data
	 */
	protected function memberData($response,$mobileCountryCode,$mobileNumber,$institutionType,$memberTag)
	{   
		
		$memberDetails = new \stdClass();
		$memberDetails->memberTitle = (!empty($response['membertitledescription']))?$response['membertitledescription']:'';
		$memberDetails->memberTitleId = (!empty($response['membertitle']))?$response['membertitle']:"0";
		$memberDetails->memberName = $response['firstName'].' '.$response['middleName'].' '.$response['lastName'];
		$memberDetails->memberName =(string)preg_replace('!\s+!',' ', $memberDetails->memberName);
		$memberDetails->memberFirstName = (!empty($response['firstName']))?$response['firstName']:'';
		$memberDetails->memberMiddleName = (!empty($response['middleName']))?$response['middleName']:'';
		$memberDetails->memberLastName = (!empty($response['lastName']))?$response['lastName']:'';
		$memberDetails->memberNickName = (!empty($response['membernickname']))?$response['membernickname']:'';
		$memberDetails->memberImage = (!empty($response['member_pic']))?(string)preg_replace('/\s/', "%20",  yii::$app->params['imagePath'].$response['member_pic']):'';
		$memberDetails->memberImageThumbnail = (!empty($response['memberImageThumbnail']))?(string)preg_replace('/\s/', "%20",  yii::$app->params['imagePath'].$response['memberImageThumbnail']):'';
		$memberDetails->memberDob = (!empty($response['member_dob']))?date_format(date_create($response['member_dob']),Yii::$app->params['dateFormat']['viewDateFormat']):'';
		$memberDetails->mobileCountryCode = (empty($mobileCountryCode))?'':$mobileCountryCode;
		$memberDetails->mobileNumber = $mobileNumber;
		$memberDetails->landLineCountryCode = (!empty($response['member_residence_phone1_countrycode'])) ? $response['member_residence_phone1_countrycode']:'';
		$memberDetails->landLineAreaCode = (!empty($response['member_residence_Phone1_areacode']))?$response['member_residence_Phone1_areacode']:'';
		$memberDetails->landLineNumber = (!empty($response['member_residence_Phone1']))?$response['member_residence_Phone1']:'';
		$memberDetails->email = (!empty($response['member_email']))?$response['member_email']:'';
		$memberDetails->profession = (!empty($response['occupation']))?$response['occupation']:'';
		$memberDetails->isChurch = ($institutionType == 2)?true:false;
		$memberDetails->homeChurchName = (!empty($response['homechurch']))?$response['homechurch']:'';
		$memberDetails->committeeDesignation = (!empty($response['committeeDesignation']))?$response['committeeDesignation']:'';
		$memberDetails->familyUnit = (!empty($response['familyunitid']))?(string)$response['familyunitid']:'';
		$memberDetails->bloodGroup = (!empty($response['memberbloodgroup'])) ? $response['memberbloodgroup']:'';
		$memberDetails->zone = (!empty($response['zone']))?(string)$response['zone']:'';
		$memberDetails->zoneId = (!empty($response['zone_id']))?(int)$response['zone_id']:'';
		$memberDetails->tags = (!empty($memberTag))?$memberTag:'';
		$locationArray = [];
		if (!empty($response['location'])) {
			$locationArray = json_decode($response['location'], true);
		}
		$location = ['latitude' => '', 'longitude' => ''];
		if (is_array($locationArray)) {
			$location = ['latitude' => $locationArray['latitude'] ?? '', 'longitude' => $locationArray['longitude'] ?? ''];
		}
		$memberDetails->viewMemberResidentialAddressOnMap =  true;
		$memberDetails->location =  $location;
		return $memberDetails;
	}
	/**
	 * Spouse Data For contact
	 */
	protected function spouseDataForContact($response,$spousemobilecountrycode,$spousemobileNumber)
	{
		$spouseDetails = new \stdClass();
		$spouseDetails->spouseTitle = (!empty($response['spousetitledescription']))? $response['spousetitledescription']:'';
		$spouseDetails->spouseTitleId = (empty($response['spousetitle'])) ? '' : $response['spousetitle'];
		$spouseDetails->spouseName = $response['spouse_firstName'].' '.$response['spouse_middleName'].' '.$response['spouse_lastName'];
		$spouseDetails->spouseName = (string)preg_replace('!\s+!', ' ', $spouseDetails->spouseName);
		$spouseDetails->spouseFirstName = (!empty($response['spouse_firstName']))?$response['spouse_firstName']:'';
		$spouseDetails->spouseMiddleName = (!empty($response['spouse_middleName']))?$response['spouse_middleName']:'';
		$spouseDetails->spouseLastName = (!empty($response['spouse_lastName']))?$response['spouse_lastName']:'';
		$spouseDetails->spouseNickName = (!empty($response['spousenickname']))?$response['spousenickname']:'';
		$spouseDetails->spouseImage = (!empty($response['spouse_pic']))?(string)preg_replace('/\s/', "%20",  yii::$app->params['imagePath'].$response['spouse_pic']):'';
		$spouseDetails->spouseImageThumbnail = (!empty($response['spouseImageThumbnail']))?(string)preg_replace('/\s/', "%20",  yii::$app->params['imagePath'].$response['spouseImageThumbnail']):'';
		$spouseDetails->spouseDob = (!empty($response['spouse_dob']))?date_format(date_create($response['spouse_dob']),Yii::$app->params['dateFormat']['viewDateFormat']):'';
		$spouseDetails->dateOfMarriage = (!empty($response['dom'])) ? date_format(date_create($response['dom']),Yii::$app->params['dateFormat']['viewDateFormat']):'';
		$spouseDetails->mobileCountryCode = (empty($spousemobileNumber))?'':$spousemobilecountrycode;
		$spouseDetails->mobileNumber = (!empty($spousemobileNumber))?$spousemobileNumber:'';
		$spouseDetails->email = (!empty($response['spouse_email']))?$response['spouse_email']:'';
		$spouseDetails->profession = (!empty($response['spouseoccupation']))?$response['spouseoccupation']:'';
		$spouseDetails->committeeDesignation = (!empty($response['familyunit']))?(string)$response['familyunit']:'';
		$spouseDetails->bloodGroup = (!empty($response['spousebloodgroup']))?$response['spousebloodgroup']:'';
		return $spouseDetails;
	}
	/**
	 * Spouse Data
	 */
	protected function tempSpouseData($response,$spousemobilecountrycode,$spousemobileNumber)
	{
		$spouseDetails = new \stdClass();
		$spouseDetails->spouseTitle = (!empty($response['spousetitle'])) ? $response['spousetitle']:'';
		$spouseDetails->spouseTitleId = (!empty($response['spousetitle'])) ? (int)$response['temp_spousetitle'] : '' ;
		$spouseDetails->spouseName = $response['temp_spouse_firstName'].' '.$response['temp_spouse_middleName'].' '.$response['temp_spouse_lastName'];
		$spouseDetails->spouseName = (string)preg_replace('!\s+!', ' ', $spouseDetails->spouseName);
		$spouseDetails->spouseFirstName = (!empty($response['temp_spouse_firstName']))?$response['temp_spouse_firstName']:'';
		$spouseDetails->spouseMiddleName = (!empty($response['temp_spouse_middleName']))?$response['temp_spouse_middleName']:'';
		$spouseDetails->spouseLastName = (!empty($response['temp_spouse_lastName']))?$response['temp_spouse_lastName']:'';
		$spouseDetails->spouseNickName = (!empty($response['temp_spousenickname']))?$response['temp_spousenickname']:'';
		$spouseDetails->spouseImage = (!empty($response['temp_spouse_pic']))?(string)preg_replace('/\s/', "%20",  yii::$app->params['imagePath'].$response['temp_spouse_pic']):'';
		$spouseDetails->spouseImageThumbnail = (!empty($response['temp_spouseImageThumbnail']))?(string)preg_replace('/\s/', "%20",  yii::$app->params['imagePath'].$response['temp_spouseImageThumbnail']):'';
		$spouseDetails->spouseDob = (!empty($response['temp_spouse_dob']))?date_format(date_create($response['temp_spouse_dob']),Yii::$app->params['dateFormat']['viewDateFormat']):'';
		$spouseDetails->mobileCountryCode = (empty($spousemobilecountrycode))?'':$spousemobilecountrycode;
		$spouseDetails->mobileNumber = (!empty($spousemobileNumber))?$spousemobileNumber:'';
		$spouseDetails->email = (!empty($response['temp_spouse_email']))?$response['temp_spouse_email']:'';
		$spouseDetails->profession = (!empty($response['temp_spouseoccupation']))?$response['temp_spouseoccupation']:'';
		$spouseDetails->committeeDesignation = (!empty($response['designation']))?$response['designation']:'';
		$spouseDetails->bloodGroup = (!empty($response['tempspouseBloodGroup']))?$response['tempspouseBloodGroup']:'';
		$spouseDetails->dateOfMarriage = (!empty($response['temp_dom'])) ? date_format(date_create($response['temp_dom']), Yii::$app->params['dateFormat']['viewDateFormat']):'';
		return $spouseDetails;
	}
	/**
	 * Spouse Data
	 */
	protected function spouseData($response,$spousemobilecountrycode,$spousemobileNumber)
	{   
		
		$spouseDetails = new \stdClass();
		$spouseDetails->spouseTitle = (!empty($response['spousetitledescription']))?$response['spousetitledescription']:'';
		$spouseDetails->spouseTitleId = (!empty($response['spousetitle'])) ? $response['spousetitle'] : '';
		$spouseDetails->spouseName = $response['spouse_firstName'].' '.$response['spouse_middleName'].' '.$response['spouse_lastName'];
		$spouseDetails->spouseName = (string)preg_replace('!\s+!', ' ', $spouseDetails->spouseName);
		$spouseDetails->spouseFirstName = (!empty($response['spouse_firstName']))?$response['spouse_firstName']:'';
		$spouseDetails->spouseMiddleName = (!empty($response['spouse_middleName']))?$response['spouse_middleName']:'';
		$spouseDetails->spouseLastName = (!empty($response['spouse_lastName']))?$response['spouse_lastName']:'';
		$spouseDetails->spouseNickName = (!empty($response['spousenickname']))?$response['spousenickname']:'';
		$spouseDetails->spouseImage = (!empty($response['spouse_pic']))?(string)preg_replace('/\s/', "%20",  yii::$app->params['imagePath'].$response['spouse_pic']):'';
		$spouseDetails->spouseImageThumbnail = (!empty($response['spouseImageThumbnail']))?(string)preg_replace('/\s/', "%20",  yii::$app->params['imagePath'].$response['spouseImageThumbnail']):'';
		$spouseDetails->spouseDob = (!empty($response['spouse_dob']))?date_format(date_create($response['spouse_dob']),Yii::$app->params['dateFormat']['viewDateFormat']):'';
		$spouseDetails->mobileCountryCode = (empty($spousemobilecountrycode))?'':$spousemobilecountrycode;
		$spouseDetails->mobileNumber = (!empty($spousemobileNumber))?$spousemobileNumber:'';
		$spouseDetails->email = (!empty($response['spouse_email']))?$response['spouse_email']:'';
		$spouseDetails->profession = (!empty($response['spouseoccupation']))?$response['spouseoccupation']:'';
		$spouseDetails->committeeDesignation = (!empty($response['designation']))?$response['designation']:'';
		$spouseDetails->bloodGroup = (!empty($response['spousebloodgroup']))?$response['spousebloodgroup']:'';
		$spouseDetails->dateOfMarriage = (!empty($response['dom'])) ? date_format(date_create($response['dom']), Yii::$app->params['dateFormat']['viewDateFormat']):'';
		return $spouseDetails;
	}

	/**
	 * residential data for contact
	 * @param unknown $response
	 * @return \stdClass
	 */
	protected function residentialDataForContact($response)
	{
		$residentialData = new \stdClass();
		$residentialData->addressLine1 = (!empty($response['residence_address1']))?$response['residence_address1']:'';
		$residentialData->addressLine2 = (!empty($response['residence_address2']))?$response['residence_address2']:'';
		$residentialData->city = (!empty($response['residence_district']))?$response['residence_district']:'';
		$residentialData->state = (!empty($response['residence_state']))?$response['residence_state']:'';
		$residentialData->pinCode = (!empty($response['residence_pincode']))?$response['residence_pincode']:'';
		return $residentialData;
	}
	/**
	 * Temp Member Residential Address
	 */
	protected function residentialData($response)
	{
		$residentialData = new \stdClass();
		$residentialData->addressLine1 = (!empty($response['temp_residence_address1']))?$response['temp_residence_address1']:'';
		$residentialData->addressLine2 = (!empty($response['temp_residence_address2']))?$response['temp_residence_address2']:'';
		$residentialData->city = (!empty($response['temp_residence_district']))?$response['temp_residence_district']:'';
		$residentialData->state = (!empty($response['temp_residence_state']))?$response['temp_residence_state']:'';
		$residentialData->pinCode = (!empty($response['temp_residence_pincode']))?$response['temp_residence_pincode']:'';
		return $residentialData;
	}
	/**
	 * Temp Member Office data for contact
	 */
	protected function officeDataForContact($response)
	{
		$officeData = new \stdClass();
		$officeData->countryCode = (!empty($response['member_business_phone1_countrycode']))?$response['member_business_phone1_countrycode']:'';
		$officeData->areaCode = (!empty($response['member_business_phone1_areacode']))?$response['member_business_phone1_areacode']:'';
		$officeData->phoneNumber = (!empty($response['member_musiness_Phone1']))?$response['member_musiness_Phone1']:'';
		return $officeData;
	}
	/**
	 * Temp Member Office data
	 */
	protected function officeData($response)
	{
		$officeData = new \stdClass();
		$officeData->countryCode = (!empty($response['temp_member_business_phone1_countrycode']))?$response['temp_member_business_phone1_countrycode']:'';
		$officeData->areaCode = (!empty($response['temp_member_business_phone1_areacode']))?$response['temp_member_business_phone1_areacode']:'';
		$officeData->phoneNumber = (!empty($response['temp_member_business_Phone1']))?$response['temp_member_business_Phone1']:'';
		return $officeData;
	}
	/**
	 * Member Alternate phone for edit
	 */
	protected function alternatePhoneForEdit($response)
	{
		$alternatePhoneData = new \stdClass();
		$alternatePhoneData->countryCode = (!empty($response['member_business_phone3_countrycode']))?$response['member_business_phone3_countrycode']:'';
		$alternatePhoneData->areaCode = (!empty($response['member_business_phone3_areacode']))?$response['member_business_phone3_areacode']:'';
		$alternatePhoneData->phoneNumber = (!empty($response['member_business_Phone3']))?$response['member_business_Phone3']:'';
		return $alternatePhoneData;
	}
	/**
	 * Temp Member Alternate phone details
	 */
	protected function alternatePhone($response)
	{
		$alternatePhoneData = new \stdClass();
		$alternatePhoneData->countryCode = (!empty($response['temp_member_business_phone3_countrycode']))?$response['temp_member_business_phone3_countrycode']:'';
		$alternatePhoneData->areaCode = (!empty($response['temp_member_business_phone3_areacode']))?$response['temp_member_business_phone3_areacode']:'';
		$alternatePhoneData->phoneNumber = (!empty($response['temp_member_business_Phone3']))?$response['temp_member_business_Phone3']:'';
		return $alternatePhoneData;
	}
	/**
	 * Member Dependant details for contact
	 */
	protected function dependantDataForContact($responseDependants)
	{
		$dependantsArray = [];
		foreach ($responseDependants as $value)
		{
			if (empty($value['dependantfullname'])){
				continue;
			}
			$dependantImage =  (!empty($value['dependantimage'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$value['dependantimage']) : '';
			$result = [
					'dependantId' => (!empty($value['id']))?$value['id']:'',
					'dependantName' => (!empty($value['dependantfullname']))?$value['dependantfullname']:'',
					'dependantMobileCountryCode' => (!empty($value['dependantmobilecountrycode']))?$value['dependantmobilecountrycode']:'',
					'dependantMobile' => (!empty($value['dependantmobile']))?$value['dependantmobile']:'',
					'dependantDob' => (!empty($value['dob']))?date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($value['dob'])) : '',
					// date_format(date_create($value['dob']),Yii::$app->params['dateFormat']['viewDateFormat']):'',
					'dependantRelation' => (!empty($value['relation']))?$value['relation']:'',
					'dependantTitleId' => (!empty($value['dependanttitleid']))?$value['dependanttitleid']:'',
					'dependantTitle' => (!empty($value['dependanttitle']))?$value['dependanttitle']:'',
					'dependantMaritalStatus' => (!empty($value['ismarried']))? (int) $value['ismarried']:0,
					'dependantSpouseId' => (!empty($value['spousedependantid']))?$value['spousedependantid']:'',
					'dependantSpouseTitle' => (!empty($value['spousetitle']))?$value['spousetitle']:'',
					'dependantSpouseTitleId' => (!empty($value['spousetitleid']))?$value['spousetitleid']:'',
					'dependantSpouseName' => (!empty($value['spousename']))?$value['spousename']:'',
					'dependantSpouseMobileCountryCode' => (!empty($value['dependantspousemobilecountrycode']))? $value['dependantspousemobilecountrycode']:'',
					'dependantSpouseMobile' => (!empty($value['dependantspousemobile']))? $value['dependantspousemobile']:'',
					'dependantSpouseDateOfBirth' => (!empty($value['spousedob']))?date_format(date_create($value['spousedob']),Yii::$app->params['dateFormat']['viewDateFormat']):'',
					'dependantWeddingAnniversary' => (!empty($value['weddinganniversary']))?date_format(date_create($value['weddinganniversary']),Yii::$app->params['dateFormat']['viewDateFormat']):'',
					'dependantImage' => $dependantImage,
					'dependantImageThumbnail' => (!empty($value['dependantthumbnailimage'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$value['dependantthumbnailimage'] ): $dependantImage,
					'dependantSpouseImage' => (!empty($value['dependantspouseimage'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$value['dependantspouseimage']) : '',
					'dependantSpouseImageThumbnail' => (!empty($value['dependantspousethumbnailimage'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$value['dependantspousethumbnailimage']) : '',
			];
			array_push($dependantsArray,$result);
		}
		return $dependantsArray;
	}
	/**
	 * Temp Member Dependant details
	 */
	protected function dependantData($responseDependants,$isTemp = false)
	{   
		$dependantsArray = [];
		foreach ($responseDependants as $value) {
			if(empty($value['dependantname'])){
				continue;
			}
		    if($isTemp){
				$dependantId = (!empty($value['dependantid']))? (int)$value['dependantid']: 0;
		        $spouseId = (!empty($value['DependantSpouseId']))? (int)$value['DependantSpouseId']: 0;
                $dependantImage = (!empty($value['tempimage'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$value['tempimage']) : '';
                $dependantImageThumbnail = (!empty($value['tempimagethumbnail'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$value['tempimagethumbnail'] ): '';
                $dependantSpouseImage = (!empty($value['tempdependantspouseimage'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$value['tempdependantspouseimage']) : '';
                $dependantSpouseImageThumbnail = (!empty($value['tempdependantspouseimagethumbnail'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$value['tempdependantspouseimagethumbnail']) : '';
		    }else{
		        $dependantId = (!empty($value['id']))? (int)$value['id']: 0;
		        $spouseId = (!empty($value['dependantspouseid']))? (int)$value['dependantspouseid']: 0;
                $dependantImage = (!empty($value['dependantimage'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$value['dependantimage']) : '';
                $dependantImageThumbnail = (!empty($value['dependantthumbnailimage'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$value['dependantthumbnailimage'] ): '';
                $dependantSpouseImage = (!empty($value['dependantspouseimage'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$value['dependantspouseimage']) : '';
                $dependantSpouseImageThumbnail = (!empty($value['dependantspousethumbnailimage'])) ? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$value['dependantspousethumbnailimage']) : '';
		    }
			$result = [
					'dependantId' =>$dependantId ,
					'dependantName' => (!empty($value['dependantname']))?$value['dependantname']:'',
					'dependantMobileCountryCode' => (!empty($value['dependantmobilecountrycode']))?$value['dependantmobilecountrycode']:'',
					'dependantMobile' => (!empty($value['dependantmobile']))?$value['dependantmobile']:'',
					'dependantDob' => (!empty($value['dob']))?date_format(date_create($value['dob']),Yii::$app->params['dateFormat']['viewDateFormat']):'',
					'dependantRelation' => (!empty($value['relation']))?$value['relation']:'',
					'dependantTitleId' => (!empty($value['dependanttitleid']))?(int)$value['dependanttitleid']:0,
					'dependantTitle' => (!empty($value['dependanttitle']))?$value['dependanttitle']:'',
					'dependantMaritalStatus' => (!empty($value['ismarried'])) ? (int)$value['ismarried'] : 0,
					'dependantSpouseId' => $spouseId,
					'dependantSpouseTitle' => (!empty($value['spousetitle']))?$value['spousetitle']:'',
					'dependantSpouseTitleId' => (!empty($value['spousetitleid']))? (int)$value['spousetitleid']:0,
					'dependantSpouseName' => (!empty($value['spousename']))?$value['spousename']:'',
					'dependantSpouseMobileCountryCode' => (!empty($value['dependantspousemobilecountrycode']))?$value['dependantspousemobilecountrycode']:'',
					'dependantSpouseMobile' => (!empty($value['dependantspousemobile']))?$value['dependantspousemobile']:'',
					'dependantSpouseDateOfBirth' => (!empty($value['spousedob']))?date_format(date_create($value['spousedob']),Yii::$app->params['dateFormat']['viewDateFormat']):'',
					'dependantWeddingAnniversary' => (!empty($value['weddinganniversary']))?date_format(date_create($value['weddinganniversary']),Yii::$app->params['dateFormat']['viewDateFormat']):'',
					'dependantImage' => $dependantImage,
					'dependantImageThumbnail' => $dependantImageThumbnail,
					'dependantSpouseImage' => $dependantSpouseImage,
					'dependantSpouseImageThumbnail' => $dependantSpouseImageThumbnail,
			];
			array_push($dependantsArray,$result);
		}
		return $dependantsArray;
	}
	/**
	 * To retrieve the details 
	 * of a particular member.
	 */
	public function actionGetContactDetailsByType()
	{	
		$user = Yii::$app->user->identity;
		$request = Yii::$app->request;
		$userGroup = $request->get('userGroup');
		$contactType = $request->get('contactType');
		
		$memberId = $request->get('memberId');
		$institutionId = $user->institutionid;
		$userId = $user->id;
		$currentUserType = $user->usertype;
		
		$userType = ($userGroup == 1) ? 'S':'M';
		$memberCountryCode = '';
		$membermobileNumber = '';
		$spousemobilecountrycode = '';
		$spousemobileNumber = '';
		
		$currentMember = "select memberid from usermember where userid = :userid and institutionid = :institutionid";
		$params = [
			':userid' => $userId,
			':institutionid' => $institutionId
		];
		$currentMemberId = Yii::$app->db->createCommand ($currentMember)->bindValues($params)->queryScalar();
		$memberId = ExtendedUserMember::getUserIdMemberId($memberId, $institutionId, $userType);
		
		$institutionResponse = ExtendedInstitution::getAssociatedInstitutions($currentMemberId,$memberId,$userType,$institutionId,$currentUserType);
		
		$contactResponse = ExtendedMember::getContactDetailsByType($memberId);
		$responseSettings = ExtendedSettings::getMemberSettings($memberId);
		$data = new \stdClass();
		if (empty($responseSettings)) {
			$this->statusCode = 200;
			$this->message = 'Member no longer exists';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		} else {
			if (!empty($contactResponse)) {
				if ($currentMemberId != $memberId) {
					$mobileCountryCode = ($responseSettings['membermobilePrivacyEnabled'] ==1) ? "" : $contactResponse['member_mobile1_countrycode'];
					$mobileNumber = 
					($responseSettings['membermobilePrivacyEnabled'] == 0) ? ((!empty($contactResponse['member_mobile1'])) ? $contactResponse['member_mobile1'] : "" ):"Private";
					$spousemobilecountrycode = (($responseSettings['spousemobilePrivacyEnabled'] == 0)) ? $contactResponse['spouse_mobile1_countrycode'] : "";
					$spousemobileNumber = ($responseSettings['spousemobilePrivacyEnabled'] == 0)? ((!empty($contactResponse['spouse_mobile1'])) ? $contactResponse['spouse_mobile1'] : "" ): "Private";
				} else {
					$mobileCountryCode = ($responseSettings['membermobilePrivacyEnabled'] == 1) ? "" : $contactResponse['member_mobile1_countrycode'];
					$mobileNumber = 
					($responseSettings['membermobilePrivacyEnabled'] == 0 ) ? ((!empty($contactResponse['member_mobile1'])) ? $contactResponse['member_mobile1'] : "" ):"Private";
					$spousemobilecountrycode = (($responseSettings['spousemobilePrivacyEnabled'] == 0)) ? $contactResponse['spouse_mobile1_countrycode'] : "";
					$spousemobileNumber = ($responseSettings['spousemobilePrivacyEnabled'] == 0)? ((!empty($contactResponse['spouse_mobile1'])) ? $contactResponse['spouse_mobile1'] : "" ): "Private";

				}
				$associatedInstitutions = [];
			 	if(!empty($institutionResponse)) {
			 		foreach ($institutionResponse as $value) {			
						$result =  [
							'institutionName' => (!empty($value['institutionname'])) ? $value['institutionname'] : '',
						];
						array_push($associatedInstitutions,$result);
					}

			 	}
				$data->memberId = trim($memberId);
				//member birthday
				if($userGroup == 0 && $contactType == 0) {
					$memberDetails = $this->setMemberDetails($contactResponse,$mobileCountryCode,$mobileNumber,$contactType);
					$data->memberDetails = $memberDetails;
					$data->committeeDesignation = [];
				}
				//spouse birthday
				elseif($userGroup == 1 && $contactType == 0) {
					$spouseDetails = $this->setSpouseDetails($contactResponse,$spousemobilecountrycode,$spousemobileNumber, $contactType);
					$data->spouseDetails = $spouseDetails;
					$data->committeeDesignation = [];
				}
				//wedding anniversary
				elseif(($userGroup == 0 || $userGroup == 1) && $contactType == 1) {
					$memberDetails = $this->setMemberDetails($contactResponse,$mobileCountryCode,$mobileNumber,$contactType);
					$data->memberDetails = $memberDetails;
					$spouseDetails = $this->setSpouseDetails($contactResponse,$spousemobilecountrycode,$spousemobileNumber,$contactType);
					$data->spouseDetails = $spouseDetails;
				}
				//committee
				elseif (($userGroup == 0 || $userGroup == 1) && $contactType == 2) {
					$committeeResponse = ExtendedCommittee::getCommitteeDesignations($memberId,$userType,$contactType);
					if(count($committeeResponse) > 0) {
						if($userGroup == 0) {
							$memberDetails = $this->setMemberDetails($contactResponse,$mobileCountryCode, $mobileNumber, $contactType);
							$data->memberDetails = $memberDetails;
						} else {
							$spouseDetails = $this->setSpouseDetails($contactResponse,$spousemobilecountrycode,$spousemobileNumber,$contactType);
							$data->spouseDetails = $spouseDetails;
						}
						$committeeArray = [];
						if(is_array($committeeResponse)) {
							foreach ($committeeResponse as $model) {
								$result = [
										'committeeName' => (!empty($model['committeetype'])) ? $model['committeetype'] :'',
										'designation' => (!empty($model['designation'])) ? $model['designation'] : '',
								];
								array_push($committeeArray,$result);
							}
						}
						$data->committeeDesignation = $committeeArray;
					} else {
						$this->statusCode = 500;
						$this->message = 'An error occurred while processing the request';
						$this->data = new \stdClass();
						return new ApiResponse($this->statusCode,$this->data,$this->message);
					}
				}
				//nearby members
				elseif (($userGroup == 0 || $userGroup == 1) && $contactType == 3) {
					if($userGroup == 0) {
						$memberDetails = $this->setMemberDetails($contactResponse,$mobileCountryCode,$mobileNumber,$contactType);
						$data->memberDetails = $memberDetails;
						$data->committeeDesignation = [];
					} else {
						$spouseDetails = $this->setSpouseDetails($contactResponse,$spousemobilecountrycode,$spousemobileNumber,$contactType);
						$data->spouseDetails = $spouseDetails;
						$data->committeeDesignation = [];
					}
				}
				//Details of member
				elseif($userGroup == 0 && $contactType == 4) {
					$memberDetails = $this->setMemberDetails($contactResponse,$mobileCountryCode,$mobileNumber,$contactType);
					$data->memberDetails = $memberDetails;
					$data->committeeDesignation = [];
				}
				//Details of spouse
				elseif($userGroup == 1 && $contactType == 4) {
					$spouseDetails = $this->setSpouseDetails($contactResponse,$spousemobilecountrycode,$spousemobileNumber,$contactType);
					$data->spouseDetails = $spouseDetails;
					$data->committeeDesignation = [];
				}
				//whole details
				elseif (($userGroup == 0 || $userGroup == 1) && $contactType == 5) {
					$memberDetails = $this->setMemberDetails($contactResponse,$mobileCountryCode,$mobileNumber,$contactType);
					$data->memberDetails = $memberDetails;
					$spouseDetails = $this->setSpouseDetails($contactResponse,$spousemobilecountrycode,$spousemobileNumber,$contactType);
					$data->spouseDetails = $spouseDetails;
					$data->committeeDesignation = [];
				}
				//Residential address
				$data->hasResidentialAddress = (!empty($contactResponse['residence_address1']))? true:false;
				$residentialData = $this->memberResidentialData($contactResponse);
				$data->residentialAddress = $residentialData;
				if (isset($data->memberDetails) && $data->memberDetails !== null) {
					$locationResponse = Member::getLocationByMemberId($memberId);
					$data->memberDetails->viewMemberResidentialAddressOnMap = true;
					$data->memberDetails->location = $locationResponse;
				}
				//associated institutions
				$data->associatedInstitutions = $associatedInstitutions;
				
				$this->statusCode = 200;
				$this->message = '';
				$this->data = $data;
			} else {
				$this->statusCode = 500;
				$this->message = 'An error occurred while processing the request';
				$this->data = new \stdClass();
			}
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	
	/**
	 * Member Residential Address
	 */
	protected function memberResidentialData($response)
	{
		$residentialData = new \stdClass();
		$residentialData->addressLine1 = (!empty($response['residence_address1']))?$response['residence_address1']:'';
		$residentialData->addressLine2 = (!empty($response['residence_address2']))?$response['residence_address2']:'';
		$residentialData->city = (!empty($response['residence_district']))?$response['residence_district']:'';
		$residentialData->state = (!empty($response['residence_state']))?$response['residence_state']:'';
		$residentialData->pinCode = (!empty($response['residence_pincode']))?$response['residence_pincode']:'';
		return $residentialData;
	}
	/**
	 * Member details
	 */
	protected function setMemberDetails($response, $mobileCountryCode, $mobileNumber, $contactType)
	{
		$memberData = new \stdClass();
		$memberData->memberTitle = (!empty($response['membertitle']))?$response['membertitle']:'';
		$memberData->memberName = $response['firstName'].' '.$response['middleName'].' '.$response['lastName'];
		$memberData->memberNickName = (!empty($response['membernickname']))?$response['membernickname']:'';
		$memberData->memberImageThumbnail = (!empty($response['memberImageThumbnail']))?(string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$response['memberImageThumbnail']):'';
		$memberData->memberImage = (!empty($response['member_pic']))?(string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$response['member_pic']):'';
		$memberData->mobileCountryCode = (empty($mobileNumber))?'':$mobileCountryCode;
		$memberData->mobileNumber = $mobileNumber;
		$memberData->email = (!empty($response['member_email']))?$response['member_email']:'';
		if($contactType == 4 || $contactType == 5)
		{
			$memberData->memberBloodGroup = (!empty($response['memberbloodgroup']))?$response['memberbloodgroup']:'';
		} 
		return $memberData;
		
	}
	/**
	 * Spouse details
	 */
	protected function setSpouseDetails($response,$spousemobilecountrycode,$spousemobileNumber,$contactType)
	{
		$spouseData = new \stdClass();
		$spouseData->spouseTitle = (!empty($response['spousetitle']))?$response['spousetitle']:'';
		$spouseData->spouseName = $response['spouse_firstName'].' '.$response['spouse_middleName'].' '.$response['spouse_lastName'];
		$spouseData->spouseNickName = (!empty($response['spousenickname']))?$response['spousenickname']:'';
		$spouseData->spouseImageThumbnail = (!empty($response['spouseImageThumbnail']))?(string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$response['spouseImageThumbnail']):'';
		$spouseData->spouseImage = (!empty($response['spouse_pic']))?(string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$response['spouse_pic']):'';
		$spouseData->mobileCountryCode = (empty($spousemobileNumber))?'':$spousemobilecountrycode;
		$spouseData->mobileNumber = $spousemobileNumber;
		$spouseData->email = (!empty($response['spouse_email']))?$response['spouse_email']:'';
		if($contactType == 4 || $contactType == 5)
		{
			$spouseData->spouseBloodGroup = (!empty($response['spousebloodgroup']))?$response['spousebloodgroup']:'';
		}
		return $spouseData;
	}
	
	/**
	 * Check for contact update
	 */
	public function actionCheckForContactUpdate()
	{
		$request = Yii::$app->request;
		$lastUpdatedOn = $request->getBodyParam('lastUpdatedOn');
		$userId = $request->getBodyParam('userId');
		$empid = Yii::$app->user->identity->id;
		$institutionId = Yii::$app->user->identity->institutionid;
		if($empid)
		{
			$response = ExtendedMember::getMemberCount($empid,$lastUpdatedOn);
			if(!empty($response))
			{
				$data = new \stdClass();
				$data->isUpdateAvailable = ($response['memmbercount'] > 0 ) ? true:false;
				$this->statusCode = 200;
				$this->message = '';
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data,$this->message);
				
			}else{
				$this->statusCode = 200;
				$this->message = '';
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
	 * To get the list of 
	 * all members in an institution
	 */
	public function actionGetContacts()
	{
		$request = Yii::$app->request;
		$instId = $request->get('institutionId');
		$filterByUpdatedContacts = strtolower($request->get('filterByUpdatedContacts')) == 'true' ? 1 : 0;
		$lastUpdated = $request->get('lastUpdatedOn');
		$userId = Yii::$app->user->identity->id;
		$institutionId = (empty($instId))? Yii::$app->user->identity->institutionid : $instId;
		$defaultUpdatedOn = date('Y-m-d H:i:s');
		$lastUpdatedOn = (!empty($lastUpdated)) ? $lastUpdated : $defaultUpdatedOn;
        $data = new \stdClass();
		if ($userId) {
			$response = ExtendedMember::getContacts($institutionId,$filterByUpdatedContacts,$lastUpdatedOn);
			if ($response !== false) {
				$contactArray = [];
				$data = new \stdClass();
				foreach ($response as $value) {
					$membertag = $value['memberTag'];
					$searchString = ',';
					if( strpos($membertag ?? '', $searchString) !== false ) {
						$myArray = explode(',', $membertag);
						$tagMember = "";
						foreach ($myArray as $tag) {
							$tagMember =  $tagMember . "" . $tag . ",";
						}
						$tagMember = rtrim($tagMember,',');
					} else {
						$tagMember = ''.$membertag;
					}
					$memberName = ucfirst($value['firstName']?? '').' '.ucfirst($value['middleName'] ?? '').' '.ucfirst($value['lastName'] ?? '');;
          $locationArray = [];
					if (!empty($value['location'])) {
						$locationArray = json_decode($value['location'], true);
					}
					$location = ['latitude' => '', 'longitude' => ''];
					if (is_array($locationArray)) {
						$location = ['latitude' => $locationArray['latitude'] ?? '', 'longitude' => $locationArray['longitude'] ?? ''];
					}
          $result = [
							'memberId' => (!empty($value['memberid'])) ? trim($value['memberid']) :'',
							'memberName' => (string)preg_replace('!\s+!', ' ', $memberName),
					    	'memberTitle' => (!empty($value['membertitledescription'])) ? $value['membertitledescription']:'',
							'memberTitleId' => (!empty($value['membertitle'])) ? $value['membertitle']:'',
							'memberNickName' => (!empty($value['membernickname'])) ? $value['membernickname']:'',
							'memberImageThumbnail' => (!empty($value['memberImageThumbnail']))?(string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$value['memberImageThumbnail']):'',
							'memberImage' => (!empty($value['member_pic']))? (string) preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$value['member_pic']):'',
							'memberPhone' => (!empty($value['member_mobile1'])) ? $value['member_mobile1']:'',
							'memberProfession' => (!empty($value['occupation'])) ? $value['occupation']:'',
							'memberTag' => $tagMember,
							'spouseTitle' => (!empty($value['spousetitledescription'])) ? $value['spousetitledescription']:'',
							'spouseTitleId' => (!empty($value['spousetitle'])) ? $value['spousetitle']:'',
							'spouseName' => $value['spouse_firstName'].' '.$value['spouse_middleName'].' '.$value['spouse_lastName'],
							'spouseNickName' => (!empty($value['spousenickname'])) ? $value['spousenickname']:'',
							'spouseImageThumbnail' => (!empty($value['spouseImageThumbnail']))?(string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$value['spouseImageThumbnail']):'',
							'spouseImage' => (!empty($value['spouse_pic']))?(string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$value['spouse_pic']):'',
							'spousePhone' => (!empty($value['spouse_mobile1'])) ? $value['spouse_mobile1']:'',
							'spouseProfession' => (!empty($value['spouseoccupation'])) ? $value['spouseoccupation']:'',
							'familyUnitId' => (!empty($value['familyunitid'])) ? $value['familyunitid']:'',
							'familyUnit' => (!empty($value['familyunit'])) ? $value['familyunit']:'',
							'memberBloodGroup' => (!empty($value['memberbloodgroup'])) ? $value['memberbloodgroup']:'',
							'spouseBloodGroup' => (!empty($value['spousebloodgroup'])) ? $value['spousebloodgroup']:'',
							'batch' => (!empty($value['batch'])) ? $value['batch']:'',
							'location' => $location,
							'headOfFamily' => isset($response['head_of_family']) ? $response['head_of_family'] : 'm',
							'zone' => (!empty($value['zone'])) ? $value['zone']:'',
							'zoneId' => (!empty($value['zone_id'])) ? $value['zone_id']:'',
							'viewMemberResidentialAddressOnMap' => true
					];
					array_push($contactArray,$result);
				}
			} else {
				$data->contacts = [];
				$this->statusCode = 500;
				$this->message = 'No contacts are available';
				$this->data = $data;
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			}
			$deletedIds = [];
			if ($filterByUpdatedContacts) {
				$deleteResponse = ExtendedDeleteMember::getDeletedContacts($institutionId, $lastUpdatedOn);
				if (count($deleteResponse) > 0) {
					foreach ($deleteResponse as $model) {
					
								$deletedIds[] = (!empty($model['memberid'])) ? (int)$model['memberid']:0;
						
						//array_push($deletedIds,$deleted);
					}	
				}
			}
			$data->updatedOn = date("Y-m-d H:i:s");
			$data->contacts = $contactArray;
			$data->removedContacts = $deletedIds;
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
	 * To get the member details
	 * for approval
	 */
	public function actionGetMemberDetailsForApproval()
	{
		$request = Yii::$app->request;
		$memberId = $request->get('memberId');
		if(!$memberId) {
			$this->statusCode = 500;
			$this->message = 'Member not found';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
		$userId = Yii::$app->user->identity->id;
		$isApprovedMember = false;
		$data = new \stdClass();
		if($userId) {
			$memberId = filter_var($memberId, FILTER_SANITIZE_NUMBER_INT);
			$institutionId = Yii::$app->user->identity->institutionid;
			$isApprovedMember = ExtendedTempmember::isTempMemberApproved($memberId);
			if (!$isApprovedMember) {
				$this->statusCode = 500;
				$this->message = 'Member data not available';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode, $this->data, $this->message);
			}
			$isApprovedMember = $isApprovedMember['temp_approved'];
			if(!$isApprovedMember) {
				$responsePendingMembers = ExtendedTempmember::getPendingMembersForResponse($memberId);
				$extendedDependant = new ExtendedDependant();
				$responseDependant = $extendedDependant->getDependants($memberId);
				$responseTempDependant = ExtendedTempdependant::getEditMemberTempDependants($memberId);
				// if(empty($responseTempDependant)){
				// 	$responseTempDependant = $responseDependant;
				// }
				$responsememberadditionalinfo = ExtendedMemberadditionalinfo::getAdditionalInfo($memberId);
				$responsetempmemberadditionalinfo = ExtendedTempmemberadditionalinfo::getTagCloud($memberId);
				if(!empty($responsePendingMembers) && count($responsePendingMembers) > 0) {
					$data = $this->memberDataEdited($responsePendingMembers,$responsememberadditionalinfo,$responsetempmemberadditionalinfo);
					$dependantsData = $this->dependantData($responseDependant);
					$editedDependantsData = $this->dependantData($responseTempDependant,true);
					$data->dependants = $dependantsData;
					$data->dependantsEdited = $editedDependantsData;	
				} else {
			        $data->memberTitleId = 0;
			        $data->memberTitleIdEdited=0;
			  		$data->memberTitle="";
			        $data->memberTitleEdited="";
			        $data->memberFirstName="";
			        $data->memberFirstNameEdited="";
			        $data->memberMiddleName="";
			        $data->memberMiddleNameEdited="";
			        $data->memberLastName="";
			        $data->memberLastNameEdited="";
			        $data->memberNickName="";
			        $data->memberNickNameEdited="";
			        $data->memberImage="";
			        $data->memberImageEdited="";
			        $data->memberImageThumbnail="";
			        $data->memberImageThumbnailEdited="";
			        $data->memberDob="";
			        $data->memberDobEdited="";
			        $data->memberEmail="";
			        $data->memberEmailEdited="";
			        $data->memberProfession="";
			        $data->memberProfessionEdited="";
			        $data->memberBloodGroup="";
			        $data->memberBloodGroupEdited="";
			        $data->landLineCountryCode ="";
			        $data->landLineCountryCodeEdited ="";
			        $data->landLineAreaCode ="";
			        $data->landLineAreaCodeEdited ="";
			        $data->landLineNumber="";
			        $data->landLineNumberEdited="";
			        $data->homeChurchName="";
			        $data->homeChurchNameEdited="";
			        $data->tags="";
			        $data->tagsEdited="";
			        $data->spouseTitleId= 0;
			        $data->spouseTitleIdEdited= 0;
			  		$data->spouseTitle="";
			        $data->spouseTitleEdited="";
			        $data->spouseFirstName="";
			        $data->spouseFirstNameEdited="";
			        $data->spouseMiddleName="";
			        $data->spouseMiddleNameEdited="";
			        $data->spouseLastName="";
			        $data->spouseLastNameEdited="";
			        $data->spouseNickName="";
			        $data->spouseNickNameEdited="";
			        $data->spouseImage="";
			        $data->spouseImageEdited="";
			        $data->spouseImageThumbnail="";
			        $data->spouseImageThumbnailEdited="";
			        $data->spouseDob="";
			        $data->spouseDobEdited="";
			        $data->spouseMobileCountryCode ="";
			        $data->spouseMobileCountryCodeEdited ="";
			        $data->spouseMobileNumber="";
			        $data->spouseMobileNumberEdited="";
			        $data->spouseEmail="";
			        $data->spouseEmailEdited="";
			        $data->spouseProfession="";
			        $data->spouseProfessionEdited="";
			        $data->spouseBloodGroup="";
			        $data->spouseBloodGroupEdited="";
			        $data->dateOfMarriage="";
			        $data->dateOfMarriageEdited="";
			        $data->residentialAddressLine1="";
			        $data->residentialAddressLine1Edited="";
			        $data->residentialAddressLine2="";
			        $data->residentialAddressLine2Edited="";
			        $data->residentialCity="";
			        $data->residentialCityEdited="";
			        $data->residentialState="";
			        $data->residentialStateEdited="";
			        $data->residentialPinCode="";
			        $data->residentialPinCodeEdited="";
			        $data->businessPhoneCountryCode="";
			        $data->businessPhoneCountryCodeEdited="";
			        $data->businessPhoneAreaCode="";
			        $data->businessPhoneAreaCodeEdited="";
			        $data->businessPhoneNumber="";
			        $data->businessPhoneNumberEdited="";
			        $data->businessAlternatePhoneCountryCode="";
			        $data->businessAlternatePhoneCountryCodeEdited="";
			        $data->businessAlternatePhoneAreaCode="";
			        $data->businessAlternatePhoneAreaCodeEdited="";
			        $data->businessAlternatePhoneNumber="";
			        $data->businessAlternatePhoneNumberEdited="";
			        $data->businessEmail="";
			        $data->businessEmailEdited="";
			        $data->businessAddressLine1="";
			        $data->businessAddressLine1Edited="";
			        $data->businessAddressLine2="";
			        $data->businessAddressLine2Edited="";
			        $data->businessCity="";
			        $data->businessCityEdited="";
			        $data->businessState="";
			        $data->businessStateEdited="";
			        $data->businessPinCode="";
			        $data->businessPinCodeEdited="";
			        $data->dependants=[];
			        $data->dependantsEdited=[];
					$emptyLocation = (['latitude' => '', 'longitude' => '']);
					$data->residenceLocation=$emptyLocation;
			        $data->residenceLocationEdited=$emptyLocation;
				}
					$this->statusCode = 200;
					$this->message = '';
					$this->data = $data;
					return new ApiResponse($this->statusCode, $this->data,$this->message);
			} else {
					$this->statusCode = 603;
					$this->message = 'The member details has been already approved by the admin.';
					$this->data = $data;
					return new ApiResponse($this->statusCode, $this->data,$this->message);
			}
		} else {
			$this->statusCode = 498;
			$this->message = 'Session invalid';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	/**
	 * Setting member response
	 */
	protected function memberDataEdited($responsePendingMembers,$responsememberadditionalinfo,$responsetempmemberadditionalinfo)
	{
		$memberData = new \stdClass();
		
		//Member Details
		$memberData->memberTitleId = (!empty($responsePendingMembers['membertitle']))? (int)$responsePendingMembers['membertitle']: 0;
		$memberData->memberTitleIdEdited = (!empty($responsePendingMembers['temp_membertitle']))? $responsePendingMembers['temp_membertitle']:0;
		$memberData->memberTitle = (!empty($responsePendingMembers['MemberTitleDescription']))? $responsePendingMembers['MemberTitleDescription']:'';
		$memberData->memberTitleEdited = (!empty($responsePendingMembers['TempMemberTitleDescription']))? $responsePendingMembers['TempMemberTitleDescription']:'';
		$memberData->memberFirstName = (!empty($responsePendingMembers['firstName']))? $responsePendingMembers['firstName']:'';
		$memberData->memberFirstNameEdited = (!empty($responsePendingMembers['temp_firstName']))? $responsePendingMembers['temp_firstName']:'';
		$memberData->memberMiddleName = (!empty($responsePendingMembers['middleName']))? $responsePendingMembers['middleName']:'';
		$memberData->memberMiddleNameEdited = (!empty($responsePendingMembers['temp_middleName']))? $responsePendingMembers['temp_middleName']:'';
		$memberData->memberLastName = (!empty($responsePendingMembers['lastName']))? $responsePendingMembers['lastName']:'';
		$memberData->memberLastNameEdited = (!empty($responsePendingMembers['temp_lastName']))? $responsePendingMembers['temp_lastName']:'';
		$memberData->memberNickName = (!empty($responsePendingMembers['membernickname']))? $responsePendingMembers['membernickname']:'';
		$memberData->memberNickNameEdited = (!empty($responsePendingMembers['temp_membernickname']))? $responsePendingMembers['temp_membernickname']:'';
		$memberData->memberImage = (!empty($responsePendingMembers['member_pic']))?(string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$responsePendingMembers['member_pic']):'';
		$memberData->memberImageEdited = (!empty($responsePendingMembers['temp_member_pic']))?(string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$responsePendingMembers['temp_member_pic']):'';
		$memberData->memberImageThumbnail = (!empty($responsePendingMembers['memberImageThumbnail']))?(string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$responsePendingMembers['memberImageThumbnail']):'';
		$memberData->memberImageThumbnailEdited = (!empty($responsePendingMembers['temp_memberImageThumbnail']))?(string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$responsePendingMembers['temp_memberImageThumbnail']):'';
		$memberData->memberDob = (!empty($responsePendingMembers['member_dob']))? date_format(date_create($responsePendingMembers['member_dob']),Yii::$app->params['dateFormat']['viewDateFormat']):'';
		$memberData->memberDobEdited = (!empty($responsePendingMembers['temp_member_dob']))? date_format(date_create($responsePendingMembers['temp_member_dob']),Yii::$app->params['dateFormat']['viewDateFormat']):'';
		$memberData->memberEmail = (!empty($responsePendingMembers['member_email']))? $responsePendingMembers['member_email']:'';
		$memberData->memberEmailEdited = (!empty($responsePendingMembers['temp_member_email']))? $responsePendingMembers['temp_member_email']:'';
		$memberData->memberProfession = (!empty($responsePendingMembers['occupation']))? $responsePendingMembers['occupation']:'';
		$memberData->memberProfessionEdited = (!empty($responsePendingMembers['temp_occupation']))? $responsePendingMembers['temp_occupation']:'';
		$memberData->memberBloodGroup = (!empty($responsePendingMembers['memberbloodgroup']))? $responsePendingMembers['memberbloodgroup']:'';
		$memberData->memberBloodGroupEdited = (!empty($responsePendingMembers['tempmemberBloodGroup']))? $responsePendingMembers['tempmemberBloodGroup']:'';
		$memberData->landLineCountryCode = (!empty($responsePendingMembers['member_residence_Phone1_countrycode']))? $responsePendingMembers['member_residence_Phone1_countrycode']:'';
		$memberData->landLineCountryCodeEdited = (!empty($responsePendingMembers['temp_member_residence_Phone1_countrycode']))? $responsePendingMembers['temp_member_residence_Phone1_countrycode']:'';
		$memberData->landLineAreaCode = (!empty($responsePendingMembers['member_residence_phone1_areacode']))? $responsePendingMembers['member_residence_phone1_areacode']:'';
		$memberData->landLineAreaCodeEdited = (!empty($responsePendingMembers['temp_member_residence_Phone1_areacode']))? $responsePendingMembers['temp_member_residence_Phone1_areacode']:'';
		$memberData->landLineNumber = (!empty($responsePendingMembers['member_residence_Phone1']))? $responsePendingMembers['member_residence_Phone1']:'';
		$memberData->landLineNumberEdited = (!empty($responsePendingMembers['temp_member_residence_Phone1']))? $responsePendingMembers['temp_member_residence_Phone1']:'';
		$memberData->homeChurchName = (!empty($responsePendingMembers['homechurch']))? $responsePendingMembers['homechurch']:'';
		$memberData->homeChurchNameEdited = (!empty($responsePendingMembers['temp_homechurch']))? $responsePendingMembers['temp_homechurch']:'';
		$memberData->tags = (!empty($responsememberadditionalinfo['tagcloud']))? $responsememberadditionalinfo['tagcloud']:'';
		$memberData->tagsEdited = (!empty($responsetempmemberadditionalinfo['temptagcloud']))? $responsetempmemberadditionalinfo['temptagcloud']:'';
		
		//Spouse Details
		$memberData->spouseTitleId = (!empty($responsePendingMembers['spousetitle']))? (int)$responsePendingMembers['spousetitle']: 0;
		$memberData->spouseTitleIdEdited = (!empty($responsePendingMembers['temp_spousetitle']))? $responsePendingMembers['temp_spousetitle']:'';
		$memberData->spouseTitle = (!empty($responsePendingMembers['SpouseTitleDescription']))? $responsePendingMembers['SpouseTitleDescription']:'';
		$memberData->spouseTitleEdited = (!empty($responsePendingMembers['TempSpouseTitleDescription']))? $responsePendingMembers['TempSpouseTitleDescription']:'';
		$memberData->spouseFirstName = (!empty($responsePendingMembers['spouse_firstName']))? $responsePendingMembers['spouse_firstName']:'';
		$memberData->spouseFirstNameEdited = (!empty($responsePendingMembers['temp_spouse_firstName']))? $responsePendingMembers['temp_spouse_firstName']:'';
		$memberData->spouseMiddleName = (!empty($responsePendingMembers['spouse_middleName']))? $responsePendingMembers['spouse_middleName']:'';
		$memberData->spouseMiddleNameEdited = (!empty($responsePendingMembers['temp_spouse_middleName']))? $responsePendingMembers['temp_spouse_middleName']:'';
		$memberData->spouseLastName = (!empty($responsePendingMembers['spouse_lastName']))? $responsePendingMembers['spouse_lastName']:'';
		$memberData->spouseLastNameEdited = (!empty($responsePendingMembers['temp_spouse_lastName']))? $responsePendingMembers['temp_spouse_lastName']:'';
		$memberData->spouseNickName = (!empty($responsePendingMembers['spousenickname']))? $responsePendingMembers['spousenickname']:'';
		$memberData->spouseNickNameEdited = (!empty($responsePendingMembers['temp_spousenickname']))? $responsePendingMembers['temp_spousenickname']:'';
		$memberData->spouseImage = (!empty($responsePendingMembers['spouse_pic']))?(string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$responsePendingMembers['spouse_pic']):'';
		$memberData->spouseImageEdited = (!empty($responsePendingMembers['temp_spouse_pic']))?(string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$responsePendingMembers['temp_spouse_pic']):'';
		$memberData->spouseImageThumbnail = (!empty($responsePendingMembers['spouseImageThumbnail']))?(string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$responsePendingMembers['spouseImageThumbnail']):'';
		$memberData->spouseImageThumbnailEdited = (!empty($responsePendingMembers['temp_spouseImageThumbnail']))? (string)preg_replace('/\s/', "%20", yii::$app->params['imagePath'].$responsePendingMembers['temp_spouseImageThumbnail']):'';
		$memberData->spouseDob = (!empty($responsePendingMembers['spouse_dob']))? date_format(date_create($responsePendingMembers['spouse_dob']),Yii::$app->params['dateFormat']['viewDateFormat']):'';
		$memberData->spouseDobEdited = (!empty($responsePendingMembers['temp_spouse_dob']))? date_format(date_create($responsePendingMembers['temp_spouse_dob']),Yii::$app->params['dateFormat']['viewDateFormat']):'';
		$memberData->spouseMobileCountryCode = (!empty($responsePendingMembers['spouse_mobile1_countrycode']))? $responsePendingMembers['spouse_mobile1_countrycode']:'';
		$memberData->spouseMobileCountryCodeEdited = (!empty($responsePendingMembers['temp_spouse_mobile1_countrycode']))? $responsePendingMembers['temp_spouse_mobile1_countrycode']:'';
		$memberData->spouseMobileNumber = (!empty($responsePendingMembers['spouse_mobile1']))? $responsePendingMembers['spouse_mobile1']:'';
		$memberData->spouseMobileNumberEdited = (!empty($responsePendingMembers['temp_spouse_mobile1']))? $responsePendingMembers['temp_spouse_mobile1']:'';
		$memberData->spouseEmail = (!empty($responsePendingMembers['spouse_email']))? $responsePendingMembers['spouse_email']:'';
		$memberData->spouseEmailEdited = (!empty($responsePendingMembers['temp_spouse_email']))? $responsePendingMembers['temp_spouse_email']:'';
		$memberData->spouseProfession = (!empty($responsePendingMembers['spouseoccupation']))? $responsePendingMembers['spouseoccupation']:'';
		$memberData->spouseProfessionEdited = (!empty($responsePendingMembers['temp_spouseoccupation']))? $responsePendingMembers['temp_spouseoccupation']:'';
		$memberData->spouseBloodGroup = (!empty($responsePendingMembers['spousebloodgroup']))? $responsePendingMembers['spousebloodgroup']:'';
		$memberData->spouseBloodGroupEdited = (!empty($responsePendingMembers['tempspouseBloodGroup']))? $responsePendingMembers['tempspouseBloodGroup']:'';
		$memberData->dateOfMarriage = (!empty($responsePendingMembers['dom']))? date_format(date_create($responsePendingMembers['dom']),Yii::$app->params['dateFormat']['viewDateFormat']):'';
		$memberData->dateOfMarriageEdited = (!empty($responsePendingMembers['temp_dom']))? date_format(date_create($responsePendingMembers['temp_dom']),Yii::$app->params['dateFormat']['viewDateFormat']):'';
		
		//Residential Address
		$memberData->residentialAddressLine1 = (!empty($responsePendingMembers['residence_address1']))? $responsePendingMembers['residence_address1']:'';
		$memberData->residentialAddressLine1Edited = (!empty($responsePendingMembers['temp_residence_address1']))? $responsePendingMembers['temp_residence_address1']:'';
		$memberData->residentialAddressLine2 = (!empty($responsePendingMembers['residence_address2']))? $responsePendingMembers['residence_address2']:'';
		$memberData->residentialAddressLine2Edited = (!empty($responsePendingMembers['temp_residence_address2']))? $responsePendingMembers['temp_residence_address2']:'';
		$memberData->residentialCity = (!empty($responsePendingMembers['residence_district']))? $responsePendingMembers['residence_district']:'';
		$memberData->residentialCityEdited = (!empty($responsePendingMembers['temp_residence_district']))? $responsePendingMembers['temp_residence_district']:'';
		$memberData->residentialState = (!empty($responsePendingMembers['residence_state']))? $responsePendingMembers['residence_state']:'';
		$memberData->residentialStateEdited = (!empty($responsePendingMembers['temp_residence_state']))? $responsePendingMembers['temp_residence_state']:'';
		$memberData->residentialPinCode = (!empty($responsePendingMembers['residence_pincode']))? $responsePendingMembers['residence_pincode']:'';
		$memberData->residentialPinCodeEdited = (!empty($responsePendingMembers['temp_residence_pincode']))? $responsePendingMembers['temp_residence_pincode']:'';
		
		//Business Details
		$memberData->businessPhoneCountryCode = (!empty($responsePendingMembers['member_business_phone1_countrycode']))? $responsePendingMembers['member_business_phone1_countrycode']:'';
		$memberData->businessPhoneCountryCodeEdited = (!empty($responsePendingMembers['temp_member_business_phone1_countrycode']))? $responsePendingMembers['temp_member_business_phone1_countrycode']:'';
		$memberData->businessPhoneAreaCode = (!empty($responsePendingMembers['member_business_phone1_areacode']))? $responsePendingMembers['member_business_phone1_areacode']:'';
		$memberData->businessPhoneAreaCodeEdited = (!empty($responsePendingMembers['temp_member_business_phone1_areacode']))? $responsePendingMembers['temp_member_business_phone1_areacode']:'';
		$memberData->businessPhoneNumber = (!empty($responsePendingMembers['member_musiness_Phone1']))? $responsePendingMembers['member_musiness_Phone1']:'';
		$memberData->businessPhoneNumberEdited = (!empty($responsePendingMembers['temp_member_business_Phone1']))? $responsePendingMembers['temp_member_business_Phone1']:'';
		
		//Alternate Phone
		$memberData->businessAlternatePhoneCountryCode = (!empty($responsePendingMembers['member_business_phone3_countrycode']))? $responsePendingMembers['member_business_phone3_countrycode']:'';
		$memberData->businessAlternatePhoneCountryCodeEdited = (!empty($responsePendingMembers['temp_member_business_phone3_countrycode']))? $responsePendingMembers['temp_member_business_phone3_countrycode']:'';
		$memberData->businessAlternatePhoneAreaCode = (!empty($responsePendingMembers['member_business_phone3_areacode']))? $responsePendingMembers['member_business_phone3_areacode']:'';
		$memberData->businessAlternatePhoneAreaCodeEdited = (!empty($responsePendingMembers['temp_member_business_phone3_areacode']))? $responsePendingMembers['temp_member_business_phone3_areacode']:'';
		$memberData->businessAlternatePhoneNumber = (!empty($responsePendingMembers['member_business_Phone3']))? $responsePendingMembers['member_business_Phone3']:'';
		$memberData->businessAlternatePhoneNumberEdited = (!empty($responsePendingMembers['temp_member_business_Phone3']))? $responsePendingMembers['temp_member_business_Phone3']:'';
		
		//Business Address
		$memberData->businessEmail = (!empty($responsePendingMembers['businessemail']))? $responsePendingMembers['businessemail']:'';
		$memberData->businessEmailEdited = (!empty($responsePendingMembers['temp_businessemail']))? $responsePendingMembers['temp_businessemail']:'';
		$memberData->businessAddressLine1 = (!empty($responsePendingMembers['business_address1']))? $responsePendingMembers['business_address1']:'';
		$memberData->businessAddressLine1Edited = (!empty($responsePendingMembers['temp_business_address1']))? $responsePendingMembers['temp_business_address1']:'';
		$memberData->businessAddressLine2 = (!empty($responsePendingMembers['business_address2']))? $responsePendingMembers['business_address2']:'';
		$memberData->businessAddressLine2Edited = (!empty($responsePendingMembers['temp_business_address2']))? $responsePendingMembers['temp_business_address2']:'';
		$memberData->businessCity = (!empty($responsePendingMembers['business_district']))? $responsePendingMembers['business_district']:'';
		$memberData->businessCityEdited = (!empty($responsePendingMembers['temp_business_district']))? $responsePendingMembers['temp_business_district']:'';
		$memberData->businessState = (!empty($responsePendingMembers['business_state']))? $responsePendingMembers['business_state']:'';
		$memberData->businessStateEdited = (!empty($responsePendingMembers['temp_business_state']))? $responsePendingMembers['temp_business_state']:'';
		$memberData->businessPinCode = (!empty($responsePendingMembers['business_pincode']))? $responsePendingMembers['business_pincode']:'';
		$memberData->businessPinCodeEdited = (!empty($responsePendingMembers['temp_business_pincode']))? $responsePendingMembers['temp_business_pincode']:'';  

		//Location
		$emptyLocation = (['latitude' => '', 'longitude' => '']);
		$memberData->residenceLocation = (!empty($responsePendingMembers['location']))? json_decode($responsePendingMembers['location']): $emptyLocation;  
		$memberData->residenceLocationEdited = (!empty($responsePendingMembers['temp_location']))? json_decode($responsePendingMembers['temp_location']):$emptyLocation;  
		return $memberData;
	}
	
	/**
	 * To update the member
	 * profile picture
	 */
	public function actionUpdateProfilePicture()
	{
		$request = Yii::$app->request;
		$memberId = $request->getBodyParam('memberId');
		$isSpouse = $request->getBodyParam('isSpouse') == 'false' ? false : true;
		
		if($memberId)
		{
			$memberId = filter_var($memberId, FILTER_SANITIZE_NUMBER_INT);
			$userId = Yii::$app->user->identity->id;
			$institutionId = Yii::$app->user->identity->institutionid;
			//$tempMemberObj = ExtendedTempmember::findOne($memberId);
			//$exists = ExtendedTempmember::find()->where([ 'temp_memberid' => $memberId])->exists();
			if($userId != 0)
			{
				if(isset($_FILES['file'])){
					$image = $_FILES['file'];
					$size = $_FILES['file']['size'];
					if($size < 5242880){
						$filename = explode('.', $_FILES['file']['name']);
						$extension = end($filename);
						if(strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg' || strtolower($extension) == 'png'){
							if(!$isSpouse)
							{
								$type = 'member';
								$targetPath = Yii::$app->params['image']['member']['main'].'/'.Yii::$app->params['image']['member']['memberImage'];
								$thumbnail = Yii::$app->params['image']['member']['main'].'/'.Yii::$app->params['image']['member']['memberthumbnailImage'];
								$memberImage = $this->fileUpload($image, $targetPath,$thumbnail);
								$status = ExtendedTempmember::saveTempMemberSpouseImage($memberId,$institutionId,$memberImage['orginal'],$memberImage['thumbnail'],$type);
								
							} else {
								$type = 'spouse';
								$targetPath = Yii::$app->params['image']['member']['main'].'/'.Yii::$app->params['image']['member']['spouseImage'];
								$thumbnail = Yii::$app->params['image']['member']['main'].'/'.Yii::$app->params['image']['member']['spousethumbnailImage'];
								$memberImage = $this->fileUpload($image, $targetPath,$thumbnail);
								$status = ExtendedTempmember::saveTempMemberSpouseImage($memberId,$institutionId,$memberImage['orginal'],$memberImage['thumbnail'],$type);
							}
							if($status)
							{
								$this->statusCode = 200;
								$this->message = '';
								$this->data = new \stdClass();
								return new ApiResponse($this->statusCode, $this->data,$this->message);
							}else{
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
						
					}else{
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
	 * Update dependant profile picture
	 */
	public function actionUpdateDependantProfilePicture()
	{
		$request = Yii::$app->request;
		$memberId = $request->getBodyParam('memberId');
		$dependantId = $request->getBodyParam('dependantId');
		$tempId = $request->getBodyParam('tempId');
		$isSpouse = $request->getBodyParam('isSpouse');

		if (empty($dependantId) && $tempId) {
			$dependantId = ExtendedDependant::getDependantId($tempId, $isSpouse);
		}

		if($memberId && isset($dependantId)) {
			$memberId = filter_var($memberId, FILTER_SANITIZE_NUMBER_INT);
			$userId = Yii::$app->user->identity->id;
				if(isset($_FILES['file'])) {
					$image = $_FILES['file'];
					$size = $_FILES['file']['size'];
					if($size < 5242880){
						$filename = explode('.', $_FILES['file']['name']);
						$extension = end($filename);
						if(strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg' || strtolower($extension) == 'png'){
							if($isSpouse == "false" || !($isSpouse)) {
								$type = 'dependant';
								$targetPath = Yii::$app->params['image']['member']['main'].'/'.Yii::$app->params['image']['member']['dependantImage'];
								$thumbnail = Yii::$app->params['image']['member']['main'].'/'.Yii::$app->params['image']['member']['thumbnailDepentant'];
								$dependantImages = $this->fileUpload($image, $targetPath,$thumbnail);
								$status = ExtendedTempdependant::updateDependentSpouseImage($memberId,$dependantId,$dependantImages['thumbnail'],$dependantImages['orginal'],$type);	
							} else {
								$type = 'dependantspouse';
								$targetPath = Yii::$app->params['image']['member']['main'].'/'.Yii::$app->params['image']['member']['dependantSpouse'];
								$thumbnail = Yii::$app->params['image']['member']['main'].'/'.Yii::$app->params['image']['member']['thumbnailDependantSpouse'];
								$dependantImages = $this->fileUpload($image, $targetPath,$thumbnail);
								$status = ExtendedTempdependant::updateDependentSpouseImage($memberId,$dependantId,$dependantImages['thumbnail'],$dependantImages['orginal'],$type);
							}
							if($status) {
								$this->statusCode = 200;
								$this->message = '';
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
                    $this->statusCode = 500;
                    $this->message = 'An error occurred while processing the request';
                    $this->data = new \stdClass();
                    return new ApiResponse($this->statusCode,$this->data,$this->message);
			}
		}
		
	}
	/**
	 * To modify
	 * profile details
	 */
	public function actionModifyMyProfileDetails()
	{
		$request = Yii::$app->request;
		$memberId = $request->getBodyParam('memberId');
		$institutionId = $request->getBodyParam('institutionId');
		$isAdminApproval = $request->getBodyParam('isAdminApproval');
		$memberTitle = $request->getBodyParam('memberTitle');
		$memberTitleId = $request->getBodyParam('memberTitleId');
		$memberName = $request->getBodyParam('memberName');
		$memberFirstName = $request->getBodyParam('memberFirstName');
		$memberMiddleName = $request->getBodyParam('memberMiddleName');
		$memberLastName = $request->getBodyParam('memberLastName');
		$memberNickName = $request->getBodyParam('memberNickName');
		$memberImage = $request->getBodyParam('memberImage');
		$memberImageThumbnail = $request->getBodyParam('memberImageThumbnail');
		$memberDob = $request->getBodyParam('memberDob');
		$memberEmail = $request->getBodyParam('memberEmail');
		$memberProfession = $request->getBodyParam('memberProfession');
		$memberBloodGroup = $request->getBodyParam('memberBloodGroup');
		$landLineCountryCode = $request->getBodyParam('landLineCountryCode');
		$landLineAreaCode = $request->getBodyParam('landLineAreaCode ');
		$landLineNumber = $request->getBodyParam('landLineNumber');
		$homeChurchName = $request->getBodyParam('homeChurchName');
		$tags = $request->getBodyParam('tags');
		$spouseTitle = $request->getBodyParam('spouseTitle');
		$spouseTitleId = $request->getBodyParam('spouseTitleId');
		$spouseName = $request->getBodyParam('spouseName');
		$spouseFirstName = $request->getBodyParam('spouseFirstName');
		$spouseMiddleName = $request->getBodyParam('spouseMiddleName');
		$spouseLastName = $request->getBodyParam('spouseLastName');
		$spouseNickName = $request->getBodyParam('spouseNickName');
		$spouseImage = $request->getBodyParam('spouseImage');
		$spouseImageThumbnail = $request->getBodyParam('spouseImageThumbnail');
		$spouseDob = $request->getBodyParam('spouseDob');
		$spouseMobileCountryCode = $request->getBodyParam('spouseMobileCountryCode');
		$spouseMobileNumber = $request->getBodyParam('spouseMobileNumber');
		$spouseEmail = $request->getBodyParam('spouseEmail');
		$spouseProfession = $request->getBodyParam('spouseProfession');
		$spouseBloodGroup = $request->getBodyParam('spouseBloodGroup');
		$dateOfMarriage = $request->getBodyParam('dateOfMarriage');
		$residentialAddressLine1 = $request->getBodyParam('residentialAddressLine1');
		$residentialAddressLine2 = $request->getBodyParam('residentialAddressLine2');
		$residentialCity = $request->getBodyParam('residentialCity');
		$residentialState = $request->getBodyParam('residentialState');
		$residentialPinCode = $request->getBodyParam('residentialPinCode');
		$businessPhoneCountryCode = $request->getBodyParam('businessPhoneCountryCode');
		$businessPhoneAreaCode = $request->getBodyParam('businessPhoneAreaCode');
		$businessPhoneNumber = $request->getBodyParam('businessPhoneNumber');
		$businessAlternatePhoneCountryCode = $request->getBodyParam('businessAlternatePhoneCountryCode');
		$businessAlternatePhoneAreaCode = $request->getBodyParam('businessAlternatePhoneAreaCode');
		$businessAlternatePhoneNumber = $request->getBodyParam('businessAlternatePhoneNumber');
		$businessEmail = $request->getBodyParam('businessEmail');
		$businessAddressLine1 = $request->getBodyParam('businessAddressLine1');
		$businessAddressLine2 = $request->getBodyParam('businessAddressLine2');
		$businessCity = $request->getBodyParam('businessCity');
		$businessState = $request->getBodyParam('businessState');
		$businessPinCode = $request->getBodyParam('businessPinCode');
		$dependantId = $request->getBodyParam('dependantId');
		$dependantTempId = $request->getBodyParam('dependantTempId');
		$dependantSpouseTempId = $request->getBodyParam('dependantSpouseTempId');
		$dependantName = $request->getBodyParam('dependantName');
		$dependantDob = $request->getBodyParam('dependantDob');
		$dependantRelation = $request->getBodyParam('dependantRelation');
		$dependantTitle = $request->getBodyParam('dependantTitle');
		$dependantTitleId = $request->getBodyParam('dependantTitleId');
		$dependantMaritalStatus = $request->getBodyParam('dependantMaritalStatus');
		$dependantSpouseId = $request->getBodyParam('dependantSpouseId');
		$dependantSpouseTitleId = $request->getBodyParam('dependantSpouseTitleId');
		$dependantSpouseName = $request->getBodyParam('dependantSpouseName');
		$dependantSpouseDateOfBirth = $request->getBodyParam('dependantSpouseDateOfBirth');
		$dependantWeddingAnniversary = $request->getBodyParam('dependantWeddingAnniversary');
		$dependantImage = $request->getBodyParam('dependantImage');
		$dependantImageThumbnail = $request->getBodyParam('dependantImageThumbnail');
		$dependantSpouseImage = $request->getBodyParam('dependantSpouseImage');
		$dependantSpouseImageThumbnail = $request->getBodyParam('dependantSpouseImageThumbnail');
		if ($memberId) {
			$memberId = filter_var($memberId, FILTER_SANITIZE_NUMBER_INT);
			$existingMember = ExtendedMember::getExistingMemberDetails($memberId);
			$userId = Yii::$app->user->identity->id;
			if($userId) {
				// 					$isApproved = ExtendedTempmember::isTempMemberApproved($memberId) ; 
				// 					$isApprovedMember = $isApproved['temp_approved'];
									//if (!$isApprovedMember) {
						$tempMemberModel = ExtendedTempmember::find()->where(['temp_memberid' =>$memberId ])->one();
						if ($tempMemberModel == null) {		
							$tempMemberModel = new ExtendedTempmember();		
						}
						$tempMemberMailModel = ExtendedTempmembermail::find()->where(['temp_memberid' =>$memberId ])->one();
						if ($tempMemberMailModel == null) {
							$tempMemberMailModel = new ExtendedTempmembermail();
						}
						//Adding member image to the temporary table
						$memberImages = [];
						$spouseImages = [];
						$memberImages['orginal'] = '';
						$memberImages['thumbnail'] = '';
						if (isset($_FILES['memberImage'])) {
							$image = $_FILES['memberImage'];
							$size = $_FILES['memberImage']['size'];
							if($size < 5242880){
								$filename = explode('.', $_FILES['memberImage']['name']);
								$extension = end($filename);
								if(strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg' || strtolower($extension) == 'png'){
									//memberImage 
									$type = 'member';
									$targetPath = Yii::$app->params['image']['member']['main'].'/'.Yii::$app->params['image']['member']['memberImage'];
									$thumbnail = Yii::$app->params['image']['member']['main'].'/'.Yii::$app->params['image']['member']['memberthumbnailImage'];
									$memberImages = $this->fileUpload($image, $targetPath,$thumbnail);
								}
							}
						}else{
							$memberImages['orginal'] = $existingMember['member_pic'];
							$memberImages['thumbnail'] = $existingMember['memberImageThumbnail'];
						}
						//Adding spouse image to the temporary table
						$spouseImages['orginal'] = '';
						$spouseImages['thumbnail'] = '';
						if (isset($_FILES['spouseImage'])) {
							$image = $_FILES['spouseImage'];
							$size = $_FILES['spouseImage']['size'];
							if($size < 5242880){
								$filename = explode('.', $_FILES['spouseImage']['name']);
								$extension = end($filename);
								if(strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg' || strtolower($extension) == 'png'){
									//spouseImage
									$type = 'spouse';
									$targetPath = Yii::$app->params['image']['member']['main'].'/'.Yii::$app->params['image']['member']['spouseImage'];
									$thumbnail = Yii::$app->params['image']['member']['main'].'/'.Yii::$app->params['image']['member']['spousethumbnailImage'];
									$spouseImages = $this->fileUpload($image, $targetPath,$thumbnail);
									
								}
							}
						}else{
							$spouseImages['orginal'] = $existingMember['spouse_pic'];
							$spouseImages['thumbnail'] = $existingMember['spouseImageThumbnail'];
							
						}
						$postDetails = $request->post();
						if(empty($postDetails['location']['latitude']) && empty($postDetails['location']['longitude'])) {
                            $postDetails['location'] = NULL;
                        }
                        else{
                            $checkValidation = $this->validateLocation($postDetails['location']);
                            if (!$checkValidation) {
                                $this->statusCode = 500;
                                $this->message = 'Invalid location format';
                                $this->data = new \stdClass();
                                return new ApiResponse($this->statusCode, $this->data,$this->message);
                            }
                            $postDetails['location'] = $postDetails['location'];
                            
                        }
						$changeBit = $this->checkThereIsAnyChanage($postDetails,$memberImages,$spouseImages) ? 0 : 1;

						$bachInsert = [];
						$fields     = [
							'tempmemberid',
							'dependantid',
							'titleid',
							'dependantname',
							'dependantmobilecountrycode',
							'dependantmobile',
							'dob',
							'relation',
							'weddinganniversary',
							'spousedependantid',
							'ismarried',
							'isapproved',
							'tempimage',
							'tempimagethumbnail'
						];
                            ExtendedTempdependantmail::deleteAll(['tempmemberid' => $memberId]);
                            ExtendedTempdependant::deleteAll(['tempmemberid' => $memberId]);
						foreach ($postDetails['dependants'] as $key => $value) {
							$newMemberDependantId = null;
							$newSpouseDependantId = null;
							$existingDependant = ExtendedDependant::getExistingDependantDetails($value['dependantId']);
                            $tempImage = '';
							$tempImageThumb = '';
							if(!empty($_FILES['dependants']['name'][$key]['dependantImage'])) {
								$image = [];
								$image['name'] = $_FILES['dependants']['name'][$key]['dependantImage'];
								$image['tmp_name'] = $_FILES['dependants']['tmp_name'][$key]['dependantImage'];
								$targetPath = Yii::$app->params['image']['member']['main'].'/'.Yii::$app->params['image']['member']['dependantImage'];
								$thumbnail = Yii::$app->params['image']['member']['main'].'/'.Yii::$app->params['image']['member']['thumbnailDepentant'];
								$dependantImages = $this->fileUpload($image, $targetPath,$thumbnail);
								
								$tempImage = $dependantImages['orginal'];
								$tempImageThumb = $dependantImages['thumbnail'];
                                
								
							}else{
                                
                                $tempImage = isset($existingDependant['image'])? $existingDependant['image']:'';
                                $tempImageThumb = isset($existingDependant['thumbnailimage'])?$existingDependant['thumbnailimage']:'';
							}
                            
							if (!empty($value['dependantName'])) {
								if (empty($value['dependantTempId'])){
									$bachInsert[] = [
										$memberId,
										$value['dependantId'],
										$value['dependantTitleId'],
										$value['dependantName'],
										$value['dependantMobileCountryCode'] ?? NULL, 
										$value['dependantMobile'] ?? NULL, 
										$this->sqlDateConversion($value['dependantDob']),
										$value['dependantRelation'],
										$this->sqlDateConversion($value['dependantWeddingAnniversary']),
										null,
										$value['dependantMaritalStatus'],
										0,
										$tempImage,
										$tempImageThumb
									];
									$newMemberDependantId = $value['dependantId'];
								}
								else{
									$tempDependant = [
										'dependantTitleId' => null,
										'dependantName' => null,
										'dependantMobileCountryCode' => null,
										'dependantMobile' => null,
										'dependantDob' => null,
										'dependantRelation' => null,
										'dependantWeddingAnniversary' => null,
										'dependantId' => null,
										'dependantMaritalStatus' => $value['dependantMaritalStatus'],
										'tempdependantId' => $value['dependantTempId']
									];

									$newMemberDependantId = ExtendedDependant::addDependants($tempDependant,$memberId);
									$bachInsert[] = [
										$memberId,
										$newMemberDependantId,
										$value['dependantTitleId'],
										$value['dependantName'],
										$value['dependantMobileCountryCode'] ?? NULL,
										$value['dependantMobile'] ?? NULL,
										$this->sqlDateConversion($value['dependantDob']),
										$value['dependantRelation'],
										$this->sqlDateConversion($value['dependantWeddingAnniversary']),
										null,
										$value['dependantMaritalStatus'],
										0,
										$tempImage,
										$tempImageThumb
									];
								}
							}
							$tempImage = '';
							$tempImageThumb = '';
							if(!empty($_FILES['dependants']['name'][$key]['dependantSpouseImage'])) {
								$image = [];
								$image['name'] = $_FILES['dependants']['name'][$key]['dependantSpouseImage'];
								$image['tmp_name'] = $_FILES['dependants']['tmp_name'][$key]['dependantSpouseImage'];
								$targetPath = Yii::$app->params['image']['member']['main'].'/'.Yii::$app->params['image']['member']['dependantSpouse'];
								$thumbnail = Yii::$app->params['image']['member']['main'].'/'.Yii::$app->params['image']['member']['thumbnailDependantSpouse'];
								$dependantImages = $this->fileUpload($image, $targetPath,$thumbnail);
								$tempImage = $dependantImages['orginal'];
								$tempImageThumb = $dependantImages['thumbnail'];
								
							}else{
							    if(isset($value['dependantSpouseId'])){
                                    $existingDependant = ExtendedDependant::getExistingDependantDetails($value['dependantSpouseId']);
                                    $tempImage = isset($existingDependant['image'])? $existingDependant['image']:'';
                                    $tempImageThumb = isset($existingDependant['thumbnailimage'])?$existingDependant['thumbnailimage']:'';
                                }

							}
							if (!empty($value['dependantSpouseName'])) {
								// if (true){
								if (empty($value['dependantSpouseTempId'])) {
									$bachInsert[] = [
											$memberId,
											$value['dependantSpouseId'],
											$value['dependantSpouseTitleId'],
											$value['dependantSpouseName'],
											$value['dependantSpouseMobileCountryCode'] ?? NULL,
											$value['dependantSpouseMobile'] ?? NULL,
											$this->sqlDateConversion($value['dependantSpouseDateOfBirth']),
											null,
                                            $this->sqlDateConversion($value['dependantWeddingAnniversary']),
											$value['dependantId'],
											0,0,
											$tempImage,
											$tempImageThumb
									];	
								}
								else{
									$tempDependant = [
										'dependantTitleId' => null,
										'dependantName' => null,
										'dependantMobileCountryCode' => null,
										'dependantMobile' => null,
										'dependantDob' => null,
										'dependantRelation' => null,
										'dependantWeddingAnniversary' => null,
										'dependantId' => $newMemberDependantId,
										'dependantMaritalStatus' => $value['dependantMaritalStatus'],
										'tempdependantId' => isset($value['dependantSpouseTempId']) ? $value['dependantSpouseTempId'] : null
									];

									$newSpouseDependantId = ExtendedDependant::addDependants($tempDependant,$memberId);

									$bachInsert[] = [
										$memberId,
										$newSpouseDependantId,
										$value['dependantSpouseTitleId'],
										$value['dependantSpouseName'],
										$value['dependantSpouseMobileCountryCode'] ?? NULL,
										$value['dependantSpouseMobile'] ?? NULL,
										$this->sqlDateConversion($value['dependantSpouseDateOfBirth']),
										null,
                                        $this->sqlDateConversion($value['dependantWeddingAnniversary']),
										$newMemberDependantId,
										0,0,
										$tempImage,
										$tempImageThumb
									];

								}
							}
						}
						$insertTemp = false;
						$insertTempDependantMail = false;
						if (count($bachInsert)>0) {
							$insertTemp = Yii::$app->db->createCommand()->batchInsert('tempdependant', $fields,$bachInsert )->execute();
							$insertTempDependantMail = Yii::$app->db->createCommand()->batchInsert('tempdependantmail', $fields,$bachInsert )->execute();
						}
						$memberAdditionalInfo = false;
						$memberAdditionalInfoMail = false;
						
						$memberAdditionalInfo = $this->addMemberAdditionalInfo($postDetails,$memberId);
						$memberAdditionalInfoMail = $this->addMemberAdditionalInfoMail($postDetails, $memberId);
						$postReqDetails = $request->post();
						$postReqDetails['temp_approved'] = $changeBit;
						// temp fix
						if (isset($postReqDetails['spouseTitle']) && empty($postReqDetails['spouseTitle']) && isset($postReqDetails['spouseTitleId']) && empty($postReqDetails['spouseTitleId'])) {
							$postReqDetails['spouseTitleId'] = '';
						}
						$model = $this->findModel($memberId);
						if ($model){
                            $postReqDetails ['businessAddressLine1']    =
                                trim($postReqDetails['businessAddressLine1'])== preg_replace('/\s/', ' ',$model->business_address1) ?
                                    $model->business_address1 :$postReqDetails['businessAddressLine1'];
                            $postReqDetails['businessAddressLine2']     =
                                trim($postReqDetails['businessAddressLine2'])== preg_replace('/\s/', ' ',$model->business_address2)?
                                    $model->business_address2 :$postReqDetails['businessAddressLine2'];
                            $postReqDetails['residentialAddressLine1'] 	=
                                trim($postReqDetails['residentialAddressLine1'])==preg_replace('/\s/', ' ',$model->residence_address1)?
                                   $model->residence_address1:$postReqDetails['residentialAddressLine1'];
                            $postReqDetails['residentialAddressLine2']
                                = trim($postReqDetails['residentialAddressLine2'])==preg_replace('/\s/', ' ',$model->residence_address2)?
                                $model->residence_address2:$postReqDetails['residentialAddressLine2'];

                        }
						if(!empty($postReqDetails['location'])){
							$postReqDetails['location'] = $postReqDetails['location'];
						} else {
							$postReqDetails['location'] = NULL;
						}
						$response = $this->saveTempMemberDetails($postReqDetails,$tempMemberModel,$memberImages,$spouseImages);
						$tempMemberMail = $this->saveTempMemberDetails($postReqDetails,$tempMemberMailModel,$memberImages,$spouseImages);

						if($response && $memberAdditionalInfo){
							// && $insertTemp && $insertTempDependantMail 
							// && $memberAdditionalInfoMail) {
							//$model = $this->findModel($memberId);
							$memberNo = $model->memberno;
							//sending notification
							ExtendedProfileupdatenotification::profileUpdateNotification($model);
							//sending mail
							$srTitle    =  $model->membertitle0['Description'] ? $model->membertitle0['Description']:'' ;
							$firstName  =  $model->firstName ? $model->firstName:' ';
							$middleName =  $model->middleName ? $model->middleName:' ';
							$lastName   =  $model->lastName ? $model->lastName :'';
							$displayName = $firstName . ' '.$middleName. ' '. $lastName;
							$adminMail = ExtendedInstitution::find()
										->where('id = :institutionid', [':institutionid' => $institutionId])
										->one();
							$toEmailId = $adminMail->email;
							$institutionLogo = Yii::$app->user->identity->institution->institutionlogo;
							$contentToMember = "I have submitted a request to edit my membership data. Kindly approve and let me know.";
							$fromName = '';
							$this->toSendMsg('ADMIN',$displayName,$toEmailId,$fromName,$contentToMember,$institutionLogo,$memberId,$memberNo);
							$toMail = $model->member_email;
							$fromMail = $adminMail->email;
							$institutionName = $adminMail->name;
							$contentToMember = "We have received your request for updating your directory information. We will review and let you know shortly";
							$this->toSendMsg($displayName, $institutionName, $toMail, $fromMail, $contentToMember, $institutionLogo, $memberId, $memberNo);
							
							$this->statusCode = 200;
							$this->message = 'Your information has been successfully submitted. You will receive a notification after these details are approved';
							$this->data = new \stdClass();
							return new ApiResponse($this->statusCode, $this->data,$this->message);
						} else {
							$this->statusCode = 500;
							$this->message = 'An error occurred while processing the request';
							$this->data = new \stdClass();
							return new ApiResponse($this->statusCode,$this->data,$this->message);
						}	
				// 					} else {
				// 						$this->statusCode = 603;
				// 						$this->message = 'The member details has been already approved by the admin.';
				// 						$this->data = new \stdClass();
				// 						return new ApiResponse($this->statusCode,$this->data,$this->message);
				// 					}
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
	 * To check any profile modifications
	 */

	protected function checkThereIsAnyChanage($postDetails,$memberImages,$spouseImages){
		$memberDetails = [
			'memberid' => $postDetails['memberId'],
			// 'institutionid ' => (int) $postDetails['institutionId'],
			'membertitle' => !empty($postDetails['memberTitleId'])?$postDetails['memberTitleId']:'',
			'firstName' => $postDetails['memberFirstName'],
			'middleName' => $postDetails['memberMiddleName'],
			'lastName' => $postDetails['memberLastName'],
			'membernickname' => $postDetails['memberNickName'],
			'member_pic' => $memberImages['orginal'],
			'memberImageThumbnail' => $memberImages['thumbnail'],
			'member_dob' => $this->sqlDateConversion($postDetails['memberDob']),
			'memberBloodGroup' => $postDetails['memberBloodGroup'],
			// 'tags' => $postDetails['tags'],
			'member_email' => $postDetails['memberEmail'],
			'occupation' => $postDetails['memberProfession'],
			'homechurch' => $postDetails['homeChurchName'],
			// 'familyUnit' => $postDetails['familyUnit'],
			'spousetitle' => !empty($postDetails['spouseTitleId'])?$postDetails['spouseTitleId']:'',
			'spouse_firstName' => $postDetails['spouseFirstName'],
			'spouse_middleName' => $postDetails['spouseMiddleName'],
			'spouse_lastName' => $postDetails['spouseLastName'],
			'spousenickname' => $postDetails['spouseNickName'],
			'spouse_pic' => $spouseImages['orginal'],
			'spouseImageThumbnail' => $spouseImages['thumbnail'],
			'spouse_dob' => $this->sqlDateConversion($postDetails['spouseDob']),
			'spouseBloodGroup' => $postDetails['spouseBloodGroup'],
			'spouse_email' => $postDetails['spouseEmail'],
			'spouseoccupation' => $postDetails['spouseProfession'],
			'dom' => $this->sqlDateConversion($postDetails['dateOfMarriage']),
			'residence_address1' => $postDetails['residentialAddressLine1'],
			'residence_address2' => $postDetails['residentialAddressLine2'],
			'residence_district' => $postDetails['residentialCity'],
			'residence_state' => $postDetails['residentialState'],
			'residence_pincode' => $postDetails['residentialPinCode'],
			'member_business_phone3_countrycode' => $postDetails['businessAlternatePhoneCountryCode'],
			'member_business_phone3_areacode' => $postDetails['businessAlternatePhoneAreaCode'],
			'member_business_Phone3' => $postDetails['businessAlternatePhoneNumber'],
			'member_business_phone1_countrycode' => $postDetails['businessPhoneCountryCode'],
			'member_business_phone1_areacode' => $postDetails['businessPhoneAreaCode'],
			'member_musiness_Phone1' => $postDetails['businessPhoneNumber'],
			'businessemail' => $postDetails['businessEmail'],
			'business_address1' => $postDetails['businessAddressLine1'],
			'business_address2' => $postDetails['businessAddressLine2'],
			'business_district' => $postDetails['businessCity'],
			'business_state' => $postDetails['businessState'],
			'business_pincode' => $postDetails['businessPinCode'],
			'member_residence_Phone1_countrycode' => $postDetails['landLineCountryCode'],
			'member_residence_Phone1_areacode' => $postDetails['landLineAreaCode'],
			'member_residence_Phone1' => $postDetails['landLineNumber'],
			'spouse_mobile1_countrycode' => $postDetails['spouseMobileCountryCode'],
			'spouse_mobile1' => $postDetails['spouseMobileNumber'],
			'location' => !empty($postDetails['location']) ? json_encode($postDetails['location']) : NULL
		];
		$dataSelect = "select 
        memberid,membertitle,firstName,middleName,lastName,membernickname,member_pic,memberImageThumbnail,member_dob,memberBloodGroup,member_email,occupation,homechurch,spousetitle,spouse_firstName,spouse_middleName,spouse_lastName,spousenickname,spouse_pic,spouseImageThumbnail,spouse_dob,spouseBloodGroup,spouse_email,spouseoccupation,dom,residence_address1,residence_address2,residence_district,residence_state,residence_pincode,member_business_phone3_countrycode,member_business_phone3_areacode,member_business_Phone3,member_business_phone1_countrycode,member_business_phone1_areacode,member_musiness_Phone1,businessemail,business_address1,business_address2,business_district,business_pincode,business_state,member_residence_Phone1_countrycode,member_residence_Phone1_areacode,member_residence_Phone1,spouse_mobile1_countrycode,spouse_mobile1, location
        from member 
        where memberid = $postDetails[memberId] and institutionid = $postDetails[institutionId]";
		$queryResult = Yii::$app->db->createCommand ($dataSelect)->queryOne();
		if(empty(array_diff_assoc($queryResult,$memberDetails))){
        	$queryResult = Yii::$app->db->createCommand(
				"CALL get_dependants(:memberId)")
				->bindValue(':memberId' , $postDetails['memberId'])
				->queryAll();
			$dependants = $postDetails['dependants'];
			if(count($queryResult) == count($dependants)){
				if(!count($dependants)){
					return false;
				}
				array_multisort(
					array_column($queryResult, 'id'),
					SORT_ASC, SORT_NUMERIC, $queryResult
				);
				array_multisort(
					array_column($dependants, 'dependantId'),
					SORT_ASC, SORT_NUMERIC, $dependants
				);
				foreach($dependants as $key => $value){
					if(!empty($_FILES['dependants']['name'][$key]['dependantImage']) && !empty($_FILES['dependants']['name'][$key]['dependantSpouseImage'])) {
						return true;
					}
					$dependant =  $queryResult[$key];
					if($value['dependantId'] != $dependant['id']){
						return true;
					}
					unset($dependant['memberid'], $dependant['dependantfullname'], $dependant['spousedependantid'], $dependant['spousefullname'], $dependant['dependantimage'], $dependant['dependantspouseimage'], $dependant['dependantthumbnailimage'], $dependant['dependantspousethumbnailimage']);
					$tempDependant = [
						'id' => $value['dependantId'],
						'dependanttitle' => $value['dependantTitle'],
						'dependanttitleid' => $value['dependantTitleId'],
						'dependantname' => $value['dependantName'],
						'dependantmobilecountrycode' => $value['dependantMobileCountryCode'],
						'dependantmobile' => $value['dependantMobile'],
						'relation' => $value['dependantRelation'],
						'dob' => $this->sqlDateConversion($value['dependantDob']),
						'ismarried' => isset($value['dependantMaritalStatus'])?(int)$value['dependantMaritalStatus']:0,
						'weddinganniversary' => $value['dependantWeddingAnniversary'],
						'dependantspouseid' => isset($value['dependantSpouseId'])?$value['dependantSpouseId']:'',
						'spousetitleid' => $value['dependantSpouseTitleId'],
						'spousetitle' => $value['dependantSpouseTitle'],
						'spousename' => $value['dependantSpouseName'],
						'spousemobile' => $value['dependantSpouseMobile'] ?? NULL,
						'spousemobilecountrycode' => $value['dependantSpouseMobileContryCode'] ?? NULL,
						'spousedob' => $this->sqlDateConversion($value['dependantSpouseDateOfBirth']),
					];
					if(!empty(array_diff_assoc($dependant,$tempDependant))){
						return true;
					}
				}
				return false;
			}
			return true;
		}
		return true;
	}


	/**
	 * to send profile edit mail
	 */
	protected function toSendMsg($toadmin,$toName,$toEmailId,$fromName,$content,$institutionLogo, $memberId, $memberNo)
	{
		 
		$mailContent = [];
		$from = yii::$app->user->identity->emailid;
		$to = $toEmailId;
		$institutionName = Yii::$app->user->identity->institution->name;

		if ($toadmin == "ADMIN") {
          	$subject  = "Request for updating membership data from " .$toName;
          	$mailContent['template'] = 'profile-request-mail';
          	$link = Yii::$app->urlManagerBackend->createAbsoluteUrl(
                    [
                    'member/member-approvel',
                    'id' => $memberId
                    ]
                );
           $mailContent['updateLink'] = $link;
	    } else {
	      	$subject = "Your request for updation of membership data - ". $institutionName;
	      	$mailContent['template'] = 'email-message';
	    }

		$title = '';
		$emailModal = 	new EmailHandlerComponent();
		$mailContent['content'] = $content;
		$mailContent['name'] = $toName;
		$mailContent['toname'] = $toadmin;
		$mailContent['logo'] = !empty($institutionLogo) ? yii::$app->params['imagePath'].$institutionLogo : '';
		$mailContent['memberno'] = $memberNo;
		$attach = '';
	
		$temp = $emailModal->sendEmail($from,$to,$title,$subject,$mailContent,$attach);
		if ($temp) {
			return 'success';
		} else {
			return 'Error';
		}
	}
	/**
	 * To add temp member additional info mail
	 */
	protected function addMemberAdditionalInfoMail($postDetails, $memberId)
	{
		$memberAdditionalInfoMailModel = ExtendedTempmemberadditionalinfomail::find()->where(['memberid' =>$memberId ])->one();
		if ($memberAdditionalInfoMailModel == null){
			$memberAdditionalInfoMailModel = new ExtendedTempmemberadditionalinfomail();
		}
		$memberAdditionalInfoMailModel->memberid = $memberId;
		$memberAdditionalInfoMailModel->temptagcloud = $postDetails['tags'];
		$memberAdditionalInfoMailModel->isapproved = 0;
		if ($memberAdditionalInfoMailModel->save(false)) {
			return $memberAdditionalInfoMailModel;
		} else {
			return false;
		}
	}
	
	/**
	 * To add member additional info
	 */
	protected function addMemberAdditionalInfo($postDetails,$memberId)
	{
		$memberAdditionalInfoModel = ExtendedTempmemberadditionalinfo::find()->where(['memberid' =>$memberId ])->one();
		if ($memberAdditionalInfoModel == null){
			$memberAdditionalInfoModel = new ExtendedTempmemberadditionalinfo();
		}
		$memberAdditionalInfoModel->memberid = $memberId;
		$memberAdditionalInfoModel->temptagcloud = $postDetails['tags'];
		$memberAdditionalInfoModel->isapproved = 0;
		if ($memberAdditionalInfoModel->save(false)){
			return $memberAdditionalInfoModel;
		} else {
			return false;
		}
	}
	/**
	 * To add temp member details
	 */
	protected function saveTempMemberDetails($postDetails,$tempMemberModel,$memberImages,$spouseImages)
	{
		if($postDetails)
		{
			$memberDob   = $this->sqlDateConversion($postDetails['memberDob']);
			$spouseDob   = $this->sqlDateConversion($postDetails['spouseDob']);
			$dom         = $this->sqlDateConversion($postDetails['dateOfMarriage']);
			$member = $this->addTempMember($postDetails,$tempMemberModel,$memberDob,$spouseDob,$dom,$memberImages,$spouseImages);
			if($member){
				return true;
			}else{
				return false;
			}
		}
		
	}
	
	/*
	 * to conver the date into sql formate
	 */
	protected function sqlDateConversion($date){
			
		return  !empty($date)?date(yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($date)):'';
	
	}
	
	/**
	 * To save temp member details
	 */
	protected function addTempMember($memberDetails,$memberModel,$memberDob,$spouseDob,$dom,$memberImages,$spouseImages)
	{
		$memberModel->temp_memberid = trim($memberDetails['memberId']);
		$memberModel->temp_institutionid    = trim($memberDetails['institutionId']);
		$memberModel->temp_firstName 	= trim($memberDetails['memberFirstName']);
		$memberModel->temp_middleName 	= trim($memberDetails['memberMiddleName']);
		$memberModel->temp_lastName 	= trim($memberDetails['memberLastName']);
		$memberModel->temp_business_address1 = trim($memberDetails['businessAddressLine1']);
		$memberModel->temp_business_address2 = trim($memberDetails['businessAddressLine2']);
		$memberModel->temp_business_district = trim($memberDetails['businessCity']);
		$memberModel->temp_business_state 	= trim($memberDetails['businessState']);
		$memberModel->temp_business_pincode = trim($memberDetails['businessPinCode']);
		$memberModel->temp_member_dob = $memberDob;
		$memberModel->temp_member_mobile2 	= null;
		$memberModel->temp_member_business_Phone1 	= trim($memberDetails['businessPhoneNumber']);
		$memberModel->temp_member_business_Phone2 	= null;
		$memberModel->temp_member_residence_Phone1 	= trim($memberDetails['landLineNumber']);
		$memberModel->temp_member_residence_Phone2 	= '';
		$memberModel->temp_member_email 	= trim($memberDetails['memberEmail']);
		$memberModel->temp_spouse_firstName 	= trim($memberDetails['spouseFirstName']);
		$memberModel->temp_spouse_middleName 	= trim($memberDetails['spouseMiddleName']);
		$memberModel->temp_spouse_lastName 	= trim($memberDetails['spouseLastName']);
		$memberModel->temp_spouse_dob 	= $spouseDob;
		$memberModel->temp_dom 	= $dom;
		$memberModel->temp_spouse_mobile1 	= trim($memberDetails['spouseMobileNumber']);
		$memberModel->temp_spouse_mobile2 	= '';
		$memberModel->temp_spouse_email 	= trim($memberDetails['spouseEmail']);
		$memberModel->temp_residence_address1 	= trim($memberDetails['residentialAddressLine1']);
		$memberModel->temp_residence_address2 	= trim($memberDetails['residentialAddressLine2']);
		$memberModel->temp_residence_address3 	= '';
		$memberModel->temp_residence_district 	= trim($memberDetails['residentialCity']);
		$memberModel->temp_residence_state 	= trim($memberDetails['residentialState']);
		$memberModel->temp_residence_pincode 	= trim($memberDetails['residentialPinCode']);
		$memberModel->temp_member_pic 	= $memberImages['orginal'];
		$memberModel->temp_spouse_pic 	= $spouseImages['orginal'];
		$memberModel->temp_memberImageThumbnail 	= $memberImages['thumbnail'];
		$memberModel->temp_spouseImageThumbnail 	= $spouseImages['thumbnail'];
		$memberModel->temp_app_reg_member 	= '';
		$memberModel->temp_app_reg_spouse 	= '';
		$memberModel->temp_active 	= 1;
		$memberModel->temp_businessemail 	= trim($memberDetails['businessEmail']);
		$memberModel->temp_membertitle 	= trim($memberDetails['memberTitleId']);
		$memberModel->temp_spousetitle 	= trim($memberDetails['spouseTitleId']);
		$memberModel->temp_membernickname 	= trim($memberDetails['memberNickName']);
		$memberModel->temp_spousenickname 	= trim($memberDetails['spouseNickName']);
		$memberModel->temp_createddate 	= date('Y-m-d H:i:s');
		$memberModel->temp_occupation 	= trim($memberDetails['memberProfession']);
		$memberModel->temp_spouseoccupation 	= trim($memberDetails['spouseProfession']);
		$memberModel->temp_spouse_mobile1_countrycode 	= trim($memberDetails['spouseMobileCountryCode']);
		$memberModel->temp_member_business_phone1_countrycode 	= trim($memberDetails['businessPhoneCountryCode']);
		$memberModel->temp_member_business_phone1_areacode 	= trim($memberDetails['businessPhoneAreaCode']);
		$memberModel->temp_member_business_phone2_countrycode 	= '';
		$memberModel->temp_member_business_Phone3 	= '';
		$memberModel->temp_member_business_phone3_areacode 	= '';
		$memberModel->temp_member_business_phone3_countrycode 	= '';
		$memberModel->tempmemberBloodGroup 	= trim($memberDetails['memberBloodGroup']);
		$memberModel->tempspouseBloodGroup 	= trim($memberDetails['spouseBloodGroup']);
		$memberModel->temp_member_residence_Phone1_areacode 	= trim($memberDetails['landLineAreaCode']);
		$memberModel->temp_member_residence_Phone1_countrycode  	= trim($memberDetails['landLineCountryCode']);
		$memberModel->temp_homechurch = trim($memberDetails['homeChurchName']);
		
		$memberModel->temp_member_business_Phone3 = trim($memberDetails['businessAlternatePhoneNumber']);
		$memberModel->temp_member_business_phone3_countrycode = trim($memberDetails['businessAlternatePhoneCountryCode']);
		$memberModel->temp_member_business_phone3_areacode = trim($memberDetails['businessAlternatePhoneAreaCode']);
		if(!empty($memberDetails['location'])){
			$memberModel->location = $memberDetails['location'];
		}
		$memberModel->temp_approved = $memberDetails['temp_approved'];	
		if ($memberModel->save(false)){
			return $memberModel;
		} else {
			return false;
		}
		
	}
	/**
	 * To upload the images
	 */
	protected  function fileUpload($image,$targetPath,$thumbnail)
	{
	
		$fileHandlerObj = new FileuploadComponent();
		$tempName = $image['tmp_name'];
		$uploadFilename = $image['name'];
		$uploadImages = $fileHandlerObj->uploader($uploadFilename,$targetPath,$tempName,$thumbnail,false,false,false);
	
		return $uploadImages;
	}
	/**
	 * To save the necessary 
	 * details of the user by admin
	 */
	public function actionApproveProfileDetails()
	{
		$request = Yii::$app->request;
		$categoryId = $request->getBodyParam('categoryId');
		$hasPendingItems = $request->getBodyParam('hasPendingItems');
		$memberId = $request->getBodyParam('memberId');
		$subcategoryId = $request->getBodyParam('subcategoryId');
		$data = $request->getBodyParam('data');
		$dependant = $request->getBodyParam('dependant');
		$operationType = isset($dependant['operationType'])?$dependant['operationType']:'';
		$dependantId = isset($dependant['dependantId'])?$dependant['dependantId']:'';
		$dependantName = isset($dependant['dependantName'])?$dependant['dependantName']:'';
		$dependantDob = isset($dependant['dependantDob'])?$dependant['dependantDob']:'';
		$dependantRelation = isset($dependant['dependantRelation'])?$dependant['dependantRelation']:'';
		$dependantTitle = isset($dependant['dependantTitle'])?$dependant['dependantTitle']:'';
		$dependantTitleId = isset($dependant['dependantTitleId'])?$dependant['dependantTitleId']:'';
		$dependantMaritalStatus = isset($dependant['dependantMaritalStatus'])?(int)$dependant['dependantMaritalStatus']:0;
		$dependantSpouseId = isset($dependant['dependantSpouseId'])?$dependant['dependantSpouseId']:'';
		$dependantSpouseTitleId = isset($dependant['dependantSpouseTitleId'])? $dependant['dependantSpouseTitleId']:'';
		$dependantSpouseName = isset($dependant['dependantSpouseName'])?$dependant['dependantSpouseName']:'';
		$dependantSpouseDateOfBirth = isset($dependant['dependantSpouseDateOfBirth'])?$dependant['dependantSpouseDateOfBirth']:'';
		$dependantWeddingAnniversary = isset($dependant['dependantWeddingAnniversary'])?$dependant['dependantWeddingAnniversary']:'';
		$dependantImage = isset($dependant['dependantImage'])?$dependant['dependantImage']:'';
		$dependantImageThumbnail = isset($dependant['dependantImageThumbnail'])?$dependant['dependantImageThumbnail']:'';
		$dependantSpouseImage = isset($dependant['dependantSpouseImage'])?$dependant['dependantSpouseImage']:'';
		$dependantSpouseImageThumbnail = isset($dependant['dependantSpouseImageThumbnail'])?$dependant['dependantSpouseImageThumbnail']:'';
		
		/* $postDetails = $request->post();  
		$subcategories = $postDetails['subcategories']; */
		if($memberId)
		{
			$isApprovedValue = ExtendedTempmember::isTempMemberApproved($memberId);
			$isApprovedMember = $isApprovedValue['temp_approved'] ?? 0;
			if(!$isApprovedMember)
			{
					$postDetails = $request->post();
					$approveProfile = $this->approveProfileDetails($categoryId,$memberId,$subcategoryId,$data,$operationType,$dependantId,$dependantName,
							$dependantDob,$dependantRelation,$dependantTitle,$dependantTitleId,$dependantMaritalStatus,$dependantSpouseId,$dependantSpouseTitleId,
							$dependantSpouseName,$dependantSpouseDateOfBirth,$dependantWeddingAnniversary,$dependantImage,$dependantImageThumbnail,$dependantSpouseImage,
							$dependantSpouseImageThumbnail,$postDetails);
			
				if($approveProfile == true)
				{
					$memberModal = ExtendedMember::findOne($memberId);
					$tempMemberMailModal = ExtendedTempmembermail::find()->where(['temp_memberid' =>$memberId ])->one();
					if(!$hasPendingItems)
					{
						$dependantModelObj = new ExtendedDependant();
    					$tempMemberModelDao = new ExtendedTempmember();
						//$tempDependantMailModel = ExtendedTempdependantmail::find()->where(['dependantid' => $dependantId])->one();
    					$depentantResponse = [];
						$depentantResponse['isApproved'] = [];
						
						$approveMemberDetails = $this->storeAndSendApprovaldetails($memberModal,$tempMemberMailModal);
						// $dependants = ExtendedTempdependant::getEditMemberTempDependants($memberId);
						$dependants = $dependantModelObj->getDependants($memberId, false);
						$tempDependants = ExtendedTempdependant::getTempDependants($memberId);

                        $dependantIdLists = [];
                        if (!empty($tempDependants)){
                            foreach ($tempDependants as $data) {
                                $dependantIdLists[] = ['DependantId' => $data['dependantid']];
                            }
                        }

						$depentantResponse = $this->storeApprovalDependantMember($dependants, $tempDependants);
						if($approveMemberDetails && $depentantResponse) {
						    ExtendedTempmemberadditionalinfomail::deleteAll(['memberid' => $memberId]);
						    ExtendedTempdependantmail::deleteAll(['tempmemberid' => $memberId]);
						    ExtendedTempmembermail::deleteAll(['temp_memberid' => $memberId]);
						    ExtendedTempmember::setApprovedBit($memberId);
						    ExtendedTempmemberadditionalinfo::deleteAll(['memberid' => $memberId]);
						    ExtendedTempdependant::deleteAll(['tempmemberid' => $memberId]);
							//sending mail
							$mailStatus = $this->sentApprovalEmailNotification($depentantResponse['isApproved'],$approveMemberDetails['isApproved'],$memberModal,$dependantIdLists);
							//sending notification
							$institutionId = $memberModal->institutionid;
							$institutionName =   $memberModal->institution->name;
							$userType = Yii::$app->user->identity->usertype;
							ExtendedMember::profileApprovedNotification($memberId,$userType,$institutionId,$institutionName);
							/* if($mailStatus)
							{ */
								$this->statusCode = 200;
								$this->message = 'The member details are approved';
								$this->data = new \stdClass();
								return new ApiResponse($this->statusCode, $this->data,$this->message);
							/* }else{
								$this->statusCode = 500;
								$this->message = 'An error occurred while processing the request';
								$this->data = new \stdClass();
								return new ApiResponse($this->statusCode,$this->data,$this->message);
							} */
						 }else {
							$this->statusCode = 500;
							$this->message = 'An error occurred while processing the request1';
							$this->data = new \stdClass();
							return new ApiResponse($this->statusCode,$this->data,$this->message);
						}
					}else{
						$this->statusCode = 200;
						$this->message = 'The member details are approved';
						$this->data = new \stdClass();
						return new ApiResponse($this->statusCode, $this->data,$this->message);
					}
				} else {
					$this->statusCode = 500;
					$this->message = 'An error occurred while processing the request2';
					$this->data = new \stdClass();
					return new ApiResponse($this->statusCode,$this->data,$this->message);
				}
			} else {
				$this->statusCode = 603;
				$this->message = 'The member details  has been already approved by the admin.';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
			}
			
		}else{
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request3';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}
	
	/**
	 * To approve profile details
	 */
	protected function approveProfileDetails($categoryId,$memberId,$subcategoryId,$data,$operationType,$dependantId,$dependantName,
							$dependantDob,$dependantRelation,$dependantTitle,$dependantTitleId,$dependantMaritalStatus,$dependantSpouseId,$dependantSpouseTitleId,
							$dependantSpouseName,$dependantSpouseDateOfBirth,$dependantWeddingAnniversary,$dependantImage,$dependantImageThumbnail,$dependantSpouseImage,
							$dependantSpouseImageThumbnail,$postDetails)
	{

		try 
		{
			if(!empty($postDetails)){
			    if ($categoryId == 24) //Dependant details
			    {

			        $flag = 0;
			        	
			        $dependant = $postDetails['dependant'];
			        $dependant['memberid'] = $memberId;
			        $dependantIds = $dependant['dependantId'];
			        if($dependant['operationType'] == 2)
			        {
			            if(empty($dependant['dependantName']))
			            {
			                // $dependantIds = $dependant['dependantId'];
			                $deleteTempDependant = ExtendedTempdependant::deleteTempDependantById($memberId,$dependantIds);
			                $deleteTempDepSpouse = ExtendedTempdependant::deleteTempDependantSpouse($memberId,$dependantIds);
			                $deleteDependant = ExtendedDependant::deleteDependantByDependantId($memberId,$dependantIds);
			                $deleteDependantSpouse = ExtendedDependant::deleteDependantSpouseByDependantId($memberId,$dependantIds);
			            }
			            else //adding dependant to temp table
			            {
			                $addTempDependant = ExtendedTempdependant::addTempDependant($dependant,$memberId);
			                if(!empty($dependant['dependantSpouseName']) && $dependant['dependantMaritalStatus'] == 2)
			                {
			                    //adding temp dependant spouse
			                    $addTempDepSpouse = ExtendedTempdependant::addTempDependantSpouse($dependant,$memberId);
			                }
			            }
			            	
			        }
			        //modified
			        if($dependant['operationType'] == 0)
			        {
			            	
			            if(empty($dependant['dependantName']))
			            {
			                $deleteTempDependant = ExtendedTempdependant::deleteTempDependantById($memberId,$dependantIds);
			                $deleteTempDepSpouse = ExtendedTempdependant::deleteTempDependantSpouse($memberId,$dependantIds);
			                $deleteDependant = ExtendedDependant::deleteDependantByDependantId($memberId,$dependantIds);
			                $deleteDependantSpouse = ExtendedDependant::deleteDependantSpouseByDependantId($memberId,$dependantIds);
			                if(empty($dependant['dependantSpouseName']))
			                {
			                    $deleteTempDepSpouse = ExtendedTempdependant::deleteTempDependantSpouse($memberId,$dependantIds);
			                    $deleteDependantSpouse = ExtendedDependant::DeleteDependantSpouseByDependantId($memberId,$dependantIds);
			                }
			                	
			            }
			            else
			            {
			                //checking if dependant exist
			                $dependantIds = $dependant['dependantId'];
			                	
			                $dependantexist = ExtendedDependant::isDependantExist($memberId,$dependantIds);
			                $isDepExist = $dependantexist['id'] ?? 0;
			                if($isDepExist != 0)
			                {
			                    $updateDependants = ExtendedDependant::updateDependants($dependant);
			                }
			                else
			                {
			                    $addDependants = ExtendedDependant::addDependants($dependant,$memberId);
			                }
			                //checking if dependant spouse exist
			                $dependantspouseexist = ExtendedDependant::isDependantSpouseExist($memberId,$dependantIds);
							$dependantspouseexistId = $dependantspouseexist['id'] ?? 0;
			                $dependant['memberid'] = $memberId;
			                if($dependantspouseexistId == 0 && $dependant['dependantMaritalStatus'] == 2 && !empty($dependant['dependantSpouseName']))
			                {
			                    //add dependant spouse to dependant table
			                    $addDependantSposue = ExtendedDependant::addDependantSpouse($dependant);
			                }
			                elseif ($dependantspouseexistId != 0 && $dependant['dependantMaritalStatus'] == 2 && !empty($dependant['dependantSpouseName']))
			                {
			                    //updating dependant spouse details
			                    $dependant['dependantSpouseId'] = $dependantspouseexistId;
			                    $updateDependantSpouse = ExtendedDependant::updateDependantSpouse($dependant);
			                }
			                elseif ($dependantspouseexistId != 0 && ($dependant['dependantMaritalStatus'] == 0 || $dependant['dependantMaritalStatus'] == 1))
			                {
			                    //if the martial status is changed to single
			                    $deleteDependantSpouse = ExtendedDependant::DeleteDependantSpouseByDependantId($memberId,$dependantIds);
			                }
			                //checking if temp dependant exist
			                $tempdependantexist = ExtendedTempdependant::isTempDependantExist($memberId,$dependantIds);
			                $isTempDependantExist = $tempdependantexist['id'] ?? 0;
			                if($isTempDependantExist != 0)
			                {
			                    $updateTempDependant = ExtendedTempdependant::updateTempDependent($dependant);
			                }
			                else
			                {
			                    $addTempDependant = ExtendedTempdependant::addTempDependant($dependant, $memberId);
			                }
			                $tempdependantspouseexist = ExtendedTempdependant::isTempDependantSpouseExist($memberId,$dependantIds);
			                $isDepSpouseExist = $tempdependantspouseexist['id'] ?? 0;
			                if($isDepSpouseExist == 0 && $dependant['dependantMaritalStatus'] == 2 && !empty($dependant['dependantSpouseName']))
			                {
			                    //adding temp dependant spouse
			                    $dependant['memberid'] = $memberId;
			                    $addDependantSposue =  ExtendedTempdependant::addTempDependantSpouse($dependant,$memberId);
			                }
			                elseif ($isDepSpouseExist != 0 && $dependant['dependantMaritalStatus'] == 2 && !empty($dependant['dependantSpouseName']))
			                {
			                    //updating temp dependant spouse
			                    $dependant['memberid'] = $memberId;
			                    $dependant['dependantSpouseId'] = $isDepSpouseExist;
			                    $updateTempDepSpouse = ExtendedTempdependant::updateTempDependantSpouse($dependant);
			                }
			                elseif ($isDepSpouseExist != 0 && ($dependant['dependantMaritalStatus'] == 0 || $dependant['dependantMaritalStatus'] == 1))
			                {
			                    $deleteTempDepSpouse = ExtendedTempdependant::deleteTempDependantSpouse($memberId,$dependantIds);
			                }
			            }
			            //dependant image
			            if($dependantImage)
			            {
			                if($dependant['operationType'] != 2)
			                {
                                $dependantImageThumbnail =  str_replace(Yii::$app->params['imagePath'],'',$dependantImageThumbnail);
                                $dependantImage = str_replace(Yii::$app->params['imagePath'],'',$dependantImage);
			                    $updateDependantImage = ExtendedDependant::updateDependantImage($dependantImage,$dependantImageThumbnail,$dependantIds);
			                    $updateTempDependantImage = ExtendedTempdependant::updateTempDependantImage($dependantImage,$dependantImageThumbnail,$dependantIds);
			                    $updateTempImageMail = ExtendedTempdependantmail::updateTempImageMailWithDependantId($dependantImage,$dependantImageThumbnail,$dependantIds);
			                }
			            }
			            //spouse image
			            if($dependantSpouseImage)
			            {
			                if($dependant['operationType'] != 2)
			                {
                                $dependantSpouseImageThumbnail =  str_replace(Yii::$app->params['imagePath'],'',$dependantSpouseImageThumbnail);
                                $dependantSpouseImage = str_replace(Yii::$app->params['imagePath'],'',$dependantSpouseImage);
			                    $updateDependantSpouseImage = ExtendedDependant::updateDependantSpouseImage($dependantSpouseImage,$dependantSpouseImageThumbnail,$dependantIds);
			                    $updateTempDepSpouseImage = ExtendedTempdependant::updateTempDependentSpouseImage($dependantSpouseImage,$dependantSpouseImageThumbnail,$dependantIds);
			                    $updateTempDepSpouseMail = ExtendedTempdependantmail::updateTempDependantSpouseMailImage($dependantSpouseImage,$dependantSpouseImageThumbnail,$dependantIds);
			                }
			            }
			        }
			        //added
			        if($dependant['operationType'] == 1)
			        {
			            if(empty($dependant['dependantName']))
			            {
			                $deleteTempDependant = ExtendedTempdependant::deleteTempDependantById($memberId,$dependantIds);
			                $deleteTempDepSpouse = ExtendedTempdependant::deleteTempDependantSpouse($memberId,$dependantIds);
			                $deleteDependant = ExtendedDependant::deleteDependantByDependantId($memberId,$dependantIds);
			                $deleteDependantSpouse = ExtendedDependant::deleteDependantSpouseByDependantId($memberId,$dependantIds);
			            }
			            else
			            {
			                $dependant['memberid'] = $memberId;
			                $tempdependantexist = ExtendedTempdependant::isTempDependantExist($memberId,$dependantIds);
			                $isTempDependantExist = $tempdependantexist['id'] ?? 0;

			                if($isTempDependantExist != 0 || $isTempDependantExist != null)
			                {
			                    //updating temp dependant
			                    $updateTempDependant = ExtendedTempdependant::updateTempDependent($dependant);
			                }
			                $tempdependantspouseexist = ExtendedTempdependant::isTempDependantSpouseExist($memberId,$dependantIds);
			                $isDepSpouseExist = $tempdependantspouseexist['id'] ?? 0;
			                if(($isDepSpouseExist == 0 || $isDepSpouseExist == null) && $dependant['dependantMaritalStatus'] == 2 )
			                {
			                    //adding temp dependant spouse
			                    $dependant['memberid'] = $memberId;
			                    $addDependantSposue =  ExtendedTempdependant::addTempDependantSpouse($dependant,$memberId);
			                }
			                elseif(($isDepSpouseExist != 0 || $isDepSpouseExist != null) && $dependant['dependantMaritalStatus'] == 2)
			                {
			                    //updating temp dependant spouse
			                    $dependant['memberid'] = $memberId;
			                    $updateTempDepSpouse = ExtendedTempdependant::updateTempDependantSpouse($dependant);
			                }
			                elseif (($isDepSpouseExist != 0 || $isDepSpouseExist != null) && ($dependant['dependantMaritalStatus'] == 0 || $dependant['dependantMaritalStatus'] == 1))
			                {
			                    $deleteTempDepSpouse = ExtendedTempdependant::deleteTempDependantSpouse($memberId,$dependantIds);
			                }
			        
			                //adding dependant details
			                $dependantExist = ExtendedDependant::isDependantExist($memberId,$dependantIds);
			                if(($dependantExist == 0 || $dependantExist == null) && $dependant['dependantMaritalStatus'] == 2 )
			                {
			                    //adding temp dependant spouse
			                    $dependant['memberid'] = $memberId;
			                    $addDependants = ExtendedDependant::addDependants($dependant,$memberId);

			                }
			                elseif(($dependantExist != 0 || $dependantExist != null) && $dependant['dependantMaritalStatus'] == 2)
			                {
			                    //updating temp dependant spouse
			                    $dependant['memberid'] = $memberId;
			                    $updateDependant = ExtendedDependant::updateDependant($dependant, $memberId);
			                    $updateSpouse = ExtendedDependant::updateDependantSpouseByDependantId($dependant, $memberId);
			                }
			                elseif(($dependantExist != 0 || $dependantExist != null) && $dependant['dependantMaritalStatus'] == 1)
			                {
			                    //updating dependant
			                    $dependant['memberid'] = $memberId;
			                    $updateDependant = ExtendedDependant::updateDependant($dependant, $memberId);
			                   /* $updateSpouse = ExtendedDependant::updateDependantSpouseByDependantId($dependant, $memberId);*/
			                }
			                elseif(($dependantExist != 0 || $dependantExist != null) && $dependant['dependantMaritalStatus'] == 0)
			                {
			                    //updating dependant
			                    $dependant['memberid'] = $memberId;
			                    $updateDependant = ExtendedDependant::updateDependant($dependant, $memberId);
			                   /* $updateSpouse = ExtendedDependant::updateDependantSpouseByDependantId($dependant, $memberId);*/
			                }

			                elseif ($isDepSpouseExist != 0 && ($dependant['dependantMaritalStatus'] == 0 || $dependant['dependantMaritalStatus'] == 1))
			                {
			                    $deleteTempDepSpouse = ExtendedTempdependant::deleteTempDependantSpouse($memberId,$dependantIds);
			                    $deleteDependant = ExtendedDependant::deleteDependantByDependantId($memberId,$dependantIds);
			                }
			                // $addDependants = ExtendedDependant::addDependants($dependant,$memberId);
			                 $newDependantId = $dependant['dependantId'];
			                // if($dependant['dependantMaritalStatus'] == 2 && (!empty($dependant['dependantSpouseName'])))
			                // {
			                //     //adding dependant spouse
			                //     $addDependantSposue = ExtendedDependant::addDependantSpouse($dependant);
			                // }
			            }
			            //in add condition there is no dependent is so here we use new dependant id
			            if(!empty($dependantImage))
			            {
			                if($dependant['operationType'] != 2)
			                {
								$dependantImageThumbnail =  str_replace(Yii::$app->params['imagePath'],'',$dependantImageThumbnail);
                                $dependantImage = str_replace(Yii::$app->params['imagePath'],'',$dependantImage);
			                    $updateDependantImage = ExtendedDependant::updateDependantImage($dependantImage,$dependantImageThumbnail,$newDependantId);
			                    $updateTempDependantImage = ExtendedTempdependant::updateTempDependantImage($dependantImage,$dependantImageThumbnail,$dependantIds);
			                    $updateTempImageMail = ExtendedTempdependantmail::updateTempImageMailWithDependantId($dependantImage,$dependantImageThumbnail,$dependantIds);
			                }
			            }
			            //spouse image
			            if(!empty($dependantSpouseImage))
			            {
			                if($dependant['operationType'] != 2)
			                {
								$dependantSpouseImageThumbnail =  str_replace(Yii::$app->params['imagePath'],'',$dependantSpouseImageThumbnail);
                                $dependantSpouseImage = str_replace(Yii::$app->params['imagePath'],'',$dependantSpouseImage);
			                    $updateDependantSpouseImage = ExtendedDependant::updateDependantSpouseImage($dependantSpouseImage,$dependantSpouseImageThumbnail,$newDependantId);
			                    $updateTempDepSpouseImage = ExtendedTempdependant::updateTempDependentSpouseImage($dependantSpouseImage,$dependantSpouseImageThumbnail,$dependantIds);
			                    $updateTempDepSpouseMail = ExtendedTempdependantmail::updateTempDependantSpouseMailImage($dependantSpouseImage,$dependantSpouseImageThumbnail,$dependantIds);
			                }
			            }
			        }
			        $flag = 1;
			        
			    }
				else if ($categoryId == 25) { //location
					$flag = 0;
					$locationData = [
						'latitude' => '',
						'longitude' => ''
					];
					
					foreach ($postDetails['subcategories'] as $item) {
						if ($item['subcategoryId'] == 0) {
							$locationData['latitude'] = $item['data'];
						} elseif ($item['subcategoryId'] == 1) {
							$locationData['longitude'] = $item['data'];
						}
					}
					ExtendedTempmember::approveMemberDetails($categoryId, 0, json_encode($locationData), $memberId);
					$flag = 1;
				} else {
			        if (is_array($postDetails['subcategories']))
	    			foreach ($postDetails['subcategories'] as $key => $value)
	    			{
	    				
	    				if($categoryId == 19) //Spouse mobile number
	    				{
	    					$flag = 0;
	    					$approveDetails = ExtendedTempmember::approveMemberDetails($categoryId, $value['subcategoryId'], $value['data'], $memberId);
	    					$spouseMobile1 = $value['data'];
	    					$institutionDetails = ExtendedInstitution::getInstitutionDetailsByMemberId($memberId);
	    					$institutionId = $institutionDetails['institutionid'];
	    					$memberDetails = ExtendedMember::find()->where(['memberid' => $memberId])->one();
	    					$memberDetails->memberid = $memberId;
	    					$memberDetails->spouse_mobile1 = $spouseMobile1;
	    					$memberDetails->institutionid = $institutionId;
	    					$updateUserCredentials = BaseModel::updateUserCredentials($memberDetails);
	    					$flag = 1;
	    				}
	    				
	    				elseif ($categoryId == 0)
	    				{
	    					$flag = 0;
	    					if($value['data'])
	    					{
                                $value['data'] = str_replace(Yii::$app->params['imagePath'],'',$value['data']);
	    						$updateMemberImageThumbnail = ExtendedMember::updateMemberImage($memberId,$value['data']);
	    						$updateTempMemberImageThumbnail = ExtendedTempmember::updateTempMemberImage($memberId,$value['data']);
	    						$flag = 1;
	    					}else{
								$flag = 1; //if previosly no image, new image is updated but rejects then data would be empty
							}
							
							
	    				}
	    				elseif ($categoryId == 15)
	    				{
	    					$flag = 0;
	    					if($value['data'])
	    					{
                                $value['data'] = str_replace(Yii::$app->params['imagePath'],'',$value['data']);
	    						$updateSpouseImageThumbnail = ExtendedMember::updateSpouseImage($memberId,$value['data']);
	    						$updateTempSpouseImage = ExtendedTempmember::updateTempSpouseImage($memberId,$value['data']);
	    						$flag = 1;
	    					}else{
								$flag = 1; //if previosly no image, new image is updated but rejects then data would be empty
							}
	    				}
	    				else
	    				{
	    					$flag = 0;
	    					if($categoryId == 16 && $value['subcategoryId'] == 1 && ($value['data'] == ''))
	    					{
	    						ExtendedMember::approveMemberDetails($categoryId,$value['subcategoryId'],$value['data'],$memberId);
	    					}
	    					else
	    					{
	    						ExtendedMember::approveMemberDetails($categoryId,$value['subcategoryId'],$value['data'],$memberId);
	    					}
	    					$flag = 1;
	    				}
	    			}
			    }
				if($flag == 1)
				{
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}catch (\ErrorException $e){
			yii::error($e);
			return false;
		}
	}
	/**
	 * check the differnce is available
	 * @param unknown $newData
	 * @param unknown $oldData
	 * @return string
	 */
	protected function isDiffer($newData,$oldData)
	{

		$pendinginfo = true;
		if($newData != $oldData ){
			$pendinginfo = false;
		}
		 
		return $pendinginfo;
		 
	}
	protected function isDifferArray($array1, $array2)
	{
		return $array1 !== $array2;
    }

	/**
	 * To approve dependant details
	 */
	protected function storeApprovalDependantMember($dependantModel,$tempDependantMailModel)
	{
		$isApproved = [];
		$allAccept = 0;
		$allReject = 0;
		$total = 0;
		if(!empty($dependantModel) && !empty($tempDependantMailModel)){
			foreach ($dependantModel as $model){
				foreach ($tempDependantMailModel as $key => $value) {
					if($model['id'] == $value['dependantid']) {
						$dependantId = $value['dependantid'];
                        $isMarried = ['' => '', '1'=>'Single', '2'=>'Married'];
					//title id
						if (!$this->isDiffer($value['dependanttitleid'],
								$model['dependanttitleid'])){
							$total++;
							$isApproved['dependanttitle_'.$dependantId] = ['isApproved'=> false, 'value' => $value['dependanttitleid']];
							$allReject++;
						}
						else{
							$total++;
							$allAccept++;
							$isApproved['dependanttitle_'.$dependantId] = ['isApproved'=> true, 'value' => $value['dependanttitleid']];
						}
						//dependant name
						if (!$this->isDiffer($value['dependantname'], $model['dependantname'])){
							$total++;
							$isApproved['dependantname_'.$dependantId] = ['isApproved'=> false, 'value' => $value['dependantname']];
							$allReject++;
						}
						else{
							$total++;
							$allAccept++;
							$isApproved['dependantname_'.$dependantId] = ['isApproved'=> true, 'value' => $value['dependantname']];
						}
						//dependant mobile country code
						if (!$this->isDiffer($value['dependantmobilecountrycode'], $model['dependantmobilecountrycode'])){
							$total++;
							$isApproved['dependantmobilecountrycode_'.$dependantId] = ['isApproved'=> false, 'value' => $value['dependantmobilecountrycode']];
							$allReject++;
						}
						else{
							$total++;
							$allAccept++;
							$isApproved['dependantmobilecountrycode_'.$dependantId] = ['isApproved'=> true, 'value' => $value['dependantmobilecountrycode']];
						}
						//dependant mobile
						if (!$this->isDiffer($value['dependantmobile'], $model['dependantmobile'])){
							$total++;
							$isApproved['dependantmobile_'.$dependantId] = ['isApproved'=> false, 'value' => $value['dependantmobile']];
							$allReject++;
						}
						else{
							$total++;
							$allAccept++;
							$isApproved['dependantmobile_'.$dependantId] = ['isApproved'=> true, 'value' => $value['dependantmobile']];
						}
						//dob
						if (!$this->isDiffer($value['dob'],$model['dob'])){
							$total++;
							$isApproved['dob_'.$dependantId] = ['isApproved'=> false, 'value' => $value['dob']];
							$allReject++;
						}
						else{
							$total++;
							$allAccept++;
							$isApproved['dob_'.$dependantId] = ['isApproved'=> true, 'value' => $value['dob']];
						}
						//relation
						if (!$this->isDiffer($value['relation'], $model['relation'])){
							$total++;
							$isApproved['relation_'.$dependantId] = ['isApproved'=> false, 'value' => $value['relation']];
							$allReject++;
						}
						else{
							$total++;
							$allAccept++;
							$isApproved['relation_'.$dependantId] = ['isApproved'=> true, 'value' => $value['relation']];
						}

						//ismarried
						if (!$this->isDiffer($value['ismarried'], $model['ismarried'])){
							$total++;
							$isApproved['ismarried_'.$dependantId] = ['isApproved'=> false, 'value' => isset($isMarried[$value['ismarried']])?$isMarried[$value['ismarried']]:''];
							$allReject++;
						}
						else{
							$total++;
							$allAccept++;
							$isApproved['ismarried_'.$dependantId] = ['isApproved'=> true, 'value' => isset($isMarried[$value['ismarried']])?$isMarried[$value['ismarried']]:''];
						}
						//dependant image
						if (!$this->isDiffer($value['tempimage'], $model['dependantimage'])){
							$total++;
							$isApproved['dependantPic_'.$dependantId] = ['isApproved'=> false, 'value' => $value['tempimage']];
							$allReject++;
						}
						else{
							$total++;
							$allAccept++;
							$isApproved['dependantPic_'.$dependantId] = ['isApproved'=> true, 'value' => $value['tempimage']];
						}
						//thumbnail image
						if (!$this->isDiffer($value['tempimagethumbnail'], $model['dependantthumbnailimage'])){
							$total++;
							$isApproved['thumbnailimage'] = ['isApproved'=> false, 'value' => $value['tempimagethumbnail']];
							$allReject++;
						}
						else{
							$total++;
							$allAccept++;
							$isApproved['thumbnailimage'] = ['isApproved'=> true, 'value' => $value['tempimagethumbnail']];
						}
						if (!empty($model['spousetitleid'])){

                            //Spouse Title
                            if (!$this->isDiffer($value['spousetitleid'], $model['spousetitleid'])){
                                $total++;
                                $isApproved['spousetitle_'.$dependantId] = ['isApproved'=> false, 'value' => $value['spousetitleid']];
                                $allReject++;
                            }
                            else{
                                $total++;
                                $allAccept++;
                                $isApproved['spousetitle_'.$dependantId] = ['isApproved'=> true, 'value' => $value['spousetitleid']];
                            }
                            //Spouse Image
                            if (!$this->isDiffer($value['tempdependantspouseimage'], $model['dependantspouseimage'])){
                                $total++;
                                $isApproved['dependantSpousePic_'.$dependantId] = ['isApproved'=> false, 'value' => $value['tempdependantspouseimage']];
                                $allReject++;
                            }
                            else{
                                $total++;
                                $allAccept++;
                                $isApproved['dependantSpousePic_'.$dependantId] = ['isApproved'=> true, 'value' => $value['tempdependantspouseimage']];
                            }
                            // Spouse name
                            if (!$this->isDiffer($value['spousename'], $model['spousename'])){
                                $total++;
                                $isApproved['spousename_'.$dependantId] = ['isApproved'=> false, 'value' => $value['spousename']];
                                $allReject++;
                            }
                            else{
                                $total++;
                                $allAccept++;
                                $isApproved['spousename_'.$dependantId] = ['isApproved'=> true, 'value' => $value['spousename']];
                            }
							 // Spouse Mobile Country code
							 if (!$this->isDiffer($value['dependantspousemobilecountrycode'], $model['dependantspousemobilecountrycode'])){
                                $total++;
                                $isApproved['dependantspousemobilecountrycode_'.$dependantId] = ['isApproved'=> false, 'value' => $value['dependantspousemobilecountrycode']];
                                $allReject++;
                            }
                            else{
                                $total++;
                                $allAccept++;
                                $isApproved['dependantspousemobilecountrycode_'.$dependantId] = ['isApproved'=> true, 'value' => $value['dependantspousemobilecountrycode']];
                            }
							 // Spouse Mobile
							 if (!$this->isDiffer($value['dependantspousemobile'], $model['dependantspousemobile'])){
                                $total++;
                                $isApproved['dependantspousemobile_'.$dependantId] = ['isApproved'=> false, 'value' => $value['dependantspousemobile']];
                                $allReject++;
                            }
                            else{
                                $total++;
                                $allAccept++;
                                $isApproved['dependantspousemobile_'.$dependantId] = ['isApproved'=> true, 'value' => $value['dependantspousemobile']];
                            }
                            // Spouse DOB
                            if (!$this->isDiffer($value['spousedob'], $model['spousedob'])){
                                $total++;
                                $isApproved['spousedob_'.$dependantId] = ['isApproved'=> false, 'value' => $value['spousedob']];
                                $allReject++;
                            }
                            else{
                                $total++;
                                $allAccept++;
                                $isApproved['spousedob_'.$dependantId] = ['isApproved'=> true, 'value' => $value['spousedob']];
                            }
                            //wedding anniversary
                            if (!$this->isDiffer($value['weddinganniversary'], $model['weddinganniversary'])){
                                $total++;
                                $isApproved['weddinganniversary_'.$dependantId] = ['isApproved'=> false, 'value' => $value['weddinganniversary']];
                                $allReject++;
                            }
                            else{
                                $total++;
                                $allAccept++;
                                $isApproved['weddinganniversary_'.$dependantId] = ['isApproved'=> true, 'value' => $value['weddinganniversary']];
                            }

                        }

						/*if ($dependantModel->save(false)) {
							if (!$value->save(false)) {
								return false;
							}
						} else{
					 		return false;
						}*/
					}
				}	
			}
			return array('isApproved'=> $isApproved, 'allAccept'=>$allAccept, 'allReject'=>$allReject);
		} else {
			return array('isApproved'=> $isApproved, 'allAccept'=>$allAccept, 'allReject'=>$allReject);
		}
	}
	
	/**
	 * Mail on profile approval
	 */
	protected function storeAndSendApprovaldetails($memberModal,$tempMemberMailModal)
	{
		$isApproved = [];
		$allAccept = 0;
		$allReject = 0;
		$total = 0;
		$acceptMailContent = '';
		
		$userMemberModel     = new ExtendedUserMember();
		$spousePhoneAccept = false;
		//first name
		if (!$this->isDiffer($tempMemberMailModal->temp_firstName,$memberModal->firstName)){
			$total++;
			$isApproved['firstName'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_firstName];
			$allReject++;
		}
		else{
			$total++;
			$allAccept++;
			$isApproved['firstName'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_firstName];
		}
		//MiddleName
		if (!$this->isDiffer($tempMemberMailModal->temp_middleName,$memberModal->middleName)){
			$total++;
			$isApproved['middleName'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_middleName];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['middleName'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_middleName];
		}
		
		// last name
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_lastName,$memberModal->lastName)){
			$total++;
			$isApproved['lastName'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_lastName];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['lastName'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_lastName];
		}
		 
		// business_address1
		
		if (!$this->isDiffer($tempMemberMailModal->temp_business_address1,$memberModal->business_address1)){
			$total++;
			$isApproved['business_address1'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_business_address1];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['business_address1'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_business_address1];
		}
		 
		// business_address2
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_business_address2,$memberModal->business_address2)){
			$total++;
			$isApproved['business_address2'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_business_address2];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['business_address2'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_business_address2];
		}
		 
		// business_district
		
		if (!$this->isDiffer($tempMemberMailModal->temp_business_district,$memberModal->business_district)){
			$total++;
			$isApproved['business_district'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_business_district];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['business_district'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_business_district];
		}
		 
		// business_state
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_business_state, $memberModal->business_state)){
			$total++;
			$isApproved['business_state'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_business_state];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['business_state'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_business_state];
		}
		 
		// business_pincode
		
		if (!$this->isDiffer($tempMemberMailModal->temp_business_pincode, $memberModal->business_pincode)){
			$total++;
			$isApproved['business_pincode'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_business_pincode];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['business_pincode'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_business_pincode];
		}
		 
		// member_dob
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_member_dob, $memberModal->member_dob)){
			$total++;
			$isApproved['member_dob'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_member_dob];
			$allReject++;
		} else {
			$total++;
			$allAccept++;
			$isApproved['member_dob'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_member_dob];
		}
		// member_business_Phone1
		
		if (!$this->isDiffer($tempMemberMailModal->temp_member_business_Phone1,$memberModal->member_musiness_Phone1)){
			$total++;
			$isApproved['member_musiness_Phone1'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_member_business_Phone1];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['member_musiness_Phone1'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_member_business_Phone1];
		}
		 
		// member_business_Phone2
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_member_business_Phone2,$memberModal->member_business_Phone2)){
			$total++;
			$isApproved['member_business_Phone2'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_member_business_Phone2];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['member_business_Phone2'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_member_business_Phone2];
		}
		 
		// member_residence_Phone1
		
		if (!$this->isDiffer($tempMemberMailModal->temp_member_residence_Phone1,$memberModal->member_residence_Phone1)){
			$total++;
			$isApproved['member_residence_Phone1'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_member_residence_Phone1];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['member_residence_Phone1'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_member_residence_Phone1];
		}
		 
		 
		// member_residence_Phone2
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_member_residence_Phone2,$memberModal->member_residence_Phone2)){
			$total++;
			$isApproved['member_residence_Phone2'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_member_residence_Phone2];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['member_residence_Phone2'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_member_residence_Phone2];
		}
		 
		// member_email
		
		if (!$this->isDiffer($tempMemberMailModal->temp_member_email, $memberModal->member_email)){
			$total++;
			$isApproved['member_email'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_member_email];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['member_email'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_member_email];
		}
		 
		// spouse_firstName
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_spouse_firstName,$memberModal->spouse_firstName)){
			$total++;
			$isApproved['spouse_firstName'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_spouse_firstName];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['spouse_firstName'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_spouse_firstName];
		}
		 
		// spouse_middleName
		
		if (!$this->isDiffer($tempMemberMailModal->temp_spouse_middleName,$memberModal->spouse_middleName)){
			$total++;
			$isApproved['spouse_middleName'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_spouse_middleName];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['spouse_middleName'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_spouse_middleName];
		}
		 
		// spouse_lastName
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_spouse_lastName,$memberModal->spouse_lastName)){
			$total++;
			$isApproved['spouse_lastName'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_spouse_lastName];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['spouse_lastName'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_spouse_lastName];
		}
		 
		 
		// spouse_dob
		
		if (!$this->isDiffer($tempMemberMailModal->temp_spouse_dob,$memberModal->spouse_dob)){
			$total++;
			$isApproved['spouse_dob'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_spouse_dob];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['spouse_dob'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_spouse_dob];
		}
		 
		// dom
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_dom, $memberModal->dom)){
			$total++;
			$isApproved['dom'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_dom];
			$allReject++;
			 
		}else{
			$total++;
			$allAccept++;
			$isApproved['dom'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_dom];
		}
		 
		// spouse_mobile1
		
		if (!$this->isDiffer($tempMemberMailModal->temp_spouse_mobile1,$memberModal->spouse_mobile1)){
			$total++;
			$isApproved['spouse_mobile1'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_spouse_mobile1];
			$allReject++;
		
		}else{
			$total++;
			$allAccept++;
			$isApproved['spouse_mobile1'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_spouse_mobile1];
		}
		 
		// spouse_mobile2
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_spouse_mobile2,$memberModal->spouse_mobile2)){
			$total++;
			$isApproved['spouse_mobile2'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_spouse_mobile2];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['spouse_mobile2'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_spouse_mobile2];
		}
		 
		 
		// spouse_email
		
		if (!$this->isDiffer($tempMemberMailModal->temp_spouse_email,$memberModal->spouse_email)){
			$total++;
			$isApproved['spouse_email'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_spouse_email];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['spouse_email'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_spouse_email];
		}
		 
		// residence_address1
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_residence_address1,$memberModal->residence_address1)){
			$total++;
			$isApproved['residence_address1'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_residence_address1];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['residence_address1'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_residence_address1];
		}
		
		// residence_address2
		
		if (!$this->isDiffer($tempMemberMailModal->temp_residence_address2,$memberModal->residence_address2)){
			$total++;
			$isApproved['residence_address2'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_residence_address2];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['residence_address2'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_residence_address2];
		}
		 
		 
		// residence_district
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_residence_district,$memberModal->residence_district)){
			$total++;
			$isApproved['residence_district'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_residence_district];
			$allReject++;
			 
		}else{
			$total++;
			$allAccept++;
			$isApproved['residence_district'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_residence_district];
		}
		 
		 
		// residence_state
		
		if (!$this->isDiffer($tempMemberMailModal->temp_residence_state,$memberModal->residence_state)){
			$total++;
			$isApproved['residence_state'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_residence_state];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['residence_state'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_residence_state];
		}
		 
		// residence_pincode
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_residence_pincode,$memberModal->residence_pincode)){
			$total++;
			$isApproved['residence_pincode'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_residence_pincode];
			$allReject++;
			 
		}else{
			$total++;
			$allAccept++;
			$isApproved['residence_pincode'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_residence_pincode];
		}
		 
		 
		// member_pic
		
		if (!$this->isDiffer($tempMemberMailModal->temp_member_pic,$memberModal->member_pic)){
			$total++;
			$isApproved['member_pic'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_member_pic];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['member_pic'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_member_pic];
		}
		
		 
		// spouse_pic
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_spouse_pic, $memberModal->spouse_pic)){
			$total++;
			$isApproved['spouse_pic'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_spouse_pic];
			$allReject++;
			 
		}else{
			$total++;
			$allAccept++;
			$isApproved['spouse_pic'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_spouse_pic];
		}
		 
		 
		// businessemail
		
		if (!$this->isDiffer($tempMemberMailModal->temp_businessemail,$memberModal->businessemail)){
			$total++;
			$isApproved['businessemail'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_businessemail];
			$allReject++;
			 
		}else{
			$total++;
			$allAccept++;
			$isApproved['businessemail'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_businessemail];
		}
		 
		 
		// membertitle
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_membertitle,$memberModal->membertitle)){
			$total++;
			$isApproved['membertitle'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_membertitle];
			$allReject++;
			 
		}else{
			$total++;
			$allAccept++;
			$isApproved['membertitle'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_membertitle];
		}
		 
		 
		// spousetitle
		
		if (!$this->isDiffer($tempMemberMailModal->temp_spousetitle,$memberModal->spousetitle)){
			$total++;
			$isApproved['spousetitle'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_spousetitle];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['spousetitle'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_spousetitle];
		}
		 
		 
		// membernickname
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_membernickname,$memberModal->membernickname)){
			$total++;
			$isApproved['membernickname'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_membernickname];
			$allReject++;
			 
		}else{
			$total++;
			$allAccept++;
			$isApproved['membernickname'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_membernickname];
		}
		 
		 
		// spousenickname
		
		if (!$this->isDiffer($tempMemberMailModal->temp_spousenickname,$memberModal->spousenickname)){
			$total++;
			$isApproved['spousenickname'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_spousenickname];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['spousenickname'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_spousenickname];
		}
		 
		// homechurch
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_homechurch,$memberModal->homechurch)){
			$total++;
			$isApproved['homechurch'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_homechurch];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['homechurch'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_homechurch];
		}
		 
		// occupation
		
		if (!$this->isDiffer($tempMemberMailModal->temp_occupation,$memberModal->occupation)){
			$total++;
			$isApproved['occupation'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_occupation];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['occupation'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_occupation];
		}
		 
		 
		// spouseoccupation
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_spouseoccupation,$memberModal->spouseoccupation)){
			$total++;
			$isApproved['spouseoccupation'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_spouseoccupation];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['spouseoccupation'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_spouseoccupation];
		}
		 
		 
		// spouse_mobile1_countrycode
		
		if (!$this->isDiffer($tempMemberMailModal->temp_spouse_mobile1_countrycode,$memberModal->spouse_mobile1_countrycode)){
			$total++;
			$isApproved['spouse_mobile1_countrycode'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_spouse_mobile1_countrycode];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['spouse_mobile1_countrycode'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_spouse_mobile1_countrycode];
		}
		 
		// member_business_phone1_countrycode
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_member_business_phone1_countrycode,$memberModal->member_business_phone1_countrycode)){
			$total++;
			$isApproved['member_business_phone1_countrycode'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_member_business_phone1_countrycode];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['member_business_phone1_countrycode'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_member_business_phone1_countrycode];
		}
		
		
		// member_business_phone1_countrycode
		
		if (!$this->isDiffer($tempMemberMailModal->temp_member_business_phone1_areacode,$memberModal->member_business_phone1_areacode)){
			$total++;
			$isApproved['member_business_phone1_areacode'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_member_business_phone1_areacode];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['member_business_phone1_areacode'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_member_business_phone1_areacode];
		}
		 
		// member_business_phone1_countrycode
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_member_business_phone1_areacode,$memberModal->member_business_phone1_areacode)){
			$total++;
			$isApproved['member_business_phone1_areacode'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_member_business_phone1_areacode];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['member_business_phone1_areacode'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_member_business_phone1_areacode];
		}
		 
		 
		// memberImageThumbnail
		
		if (!$this->isDiffer($tempMemberMailModal->temp_memberImageThumbnail,$memberModal->memberImageThumbnail)){
			$total++;
			$isApproved['memberImageThumbnail'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_memberImageThumbnail];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['memberImageThumbnail'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_memberImageThumbnail];
		}
		// spouseImageThumbnail
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_spouseImageThumbnail,$memberModal->spouseImageThumbnail)){
			$total++;
			$isApproved['spouseImageThumbnail'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_spouseImageThumbnail];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['spouseImageThumbnail'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_spouseImageThumbnail];
		}
		 
		 
		// member_business_Phone3
		
		if (!$this->isDiffer($tempMemberMailModal->temp_member_business_Phone3,$memberModal->member_business_Phone3)){
			$total++;
			$isApproved['member_business_Phone3'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_member_business_Phone3];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['member_business_Phone3'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_member_business_Phone3];
		}
		 
		// member_business_phone3_countrycode
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_member_business_phone3_countrycode,$memberModal->member_business_phone3_countrycode)){
			$total++;
			$isApproved['member_business_phone3_countrycode'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_member_business_phone3_countrycode];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['member_business_phone3_countrycode'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_member_business_phone3_countrycode];
		}
		 
		// member_business_phone3_areacode
		
		if (!$this->isDiffer($tempMemberMailModal->temp_member_business_phone3_areacode,$memberModal->member_business_phone3_areacode)){
			$total++;
			$isApproved['member_business_phone3_areacode'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_member_business_phone3_areacode];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['member_business_phone3_areacode'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_member_business_phone3_areacode];
		}
		 
		// memberbloodgroup
		 
		if (!$this->isDiffer($tempMemberMailModal->tempmemberBloodGroup,$memberModal->memberbloodgroup)){
			$total++;
			$isApproved['memberbloodgroup'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->tempmemberBloodGroup];
			$allReject++;
			 
		}else{
			$total++;
			$allAccept++;
			$isApproved['memberbloodgroup'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->tempmemberBloodGroup];
		}
		 
		// spousebloodgroup
		
		if (!$this->isDiffer($tempMemberMailModal->tempspouseBloodGroup,$memberModal->spousebloodgroup)){
			$total++;
			$isApproved['memberbloodgroup'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->tempspouseBloodGroup];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['memberbloodgroup'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->tempspouseBloodGroup];
		}
		 
		// member_residence_phone1_areacode
		 
		if (!$this->isDiffer($tempMemberMailModal->temp_member_residence_Phone1_areacode,$memberModal->member_residence_phone1_areacode)){
			$total++;
			$isApproved['member_residence_phone1_areacode'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_member_residence_Phone1_areacode];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['member_residence_phone1_areacode'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_member_residence_Phone1_areacode];
		}
		 
		// member_residence_Phone1_countrycode
		
		if (!$this->isDiffer($tempMemberMailModal->temp_member_residence_Phone1_countrycode,$memberModal->member_residence_Phone1_countrycode)){
			$total++;
			$isApproved['member_residence_Phone1_countrycode'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->temp_member_residence_Phone1_countrycode];
			$allReject++;
		}else{
			$total++;
			$allAccept++;
			$isApproved['member_residence_Phone1_countrycode'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->temp_member_residence_Phone1_countrycode];
		}

		//location
		if (!$this->isDifferArray($tempMemberMailModal->location,$memberModal->location)){
			$total++;
			$isApproved['location'] = ['isApproved'=> false, 'value' => $tempMemberMailModal->location];
			$allReject++;
		}
		else{
			$total++;
			$allAccept++;
			$isApproved['location'] = ['isApproved'=> true, 'value' => $tempMemberMailModal->location];
		}
		 
		// Track who approved the member info
		$memberModal->updated_by = Yii::$app->user->id;
		
		if ($memberModal->save(false)){
		
			$spousePhoneAccept = true;
			 
			// add spouse details in creadential
			$spouseMobleNo = $memberModal->spouse_mobile1;
			$spouseEmail = $memberModal->spouse_email;
		
		
			if (!empty($spouseMobleNo) && $spousePhoneAccept )
			{
				
				$userCredentialModel = new ExtendedUserCredentials();
				$spouseUserCredentialExist = $userCredentialModel->memberCredentialExist($spouseMobleNo,$spouseEmail);
		
				if (!empty($spouseUserCredentialExist))
				{
					$spouseUserCredentialId = $spouseUserCredentialExist['id'];
				}
				else
				{
					$institutionid = Yii::$app->user->identity->institutionid;
					$spouseUserCredentialId   = 	$this->addUserCredential($institutionid,$spouseEmail,'remember','S',$spouseMobleNo);
				}
		
				$userMemberList = $userMemberModel->userMemberExist($spouseUserCredentialId,$memberModal->memberid,Yii::$app->user->identity->institutionid,'S');
		
				if ($userMemberList == null || count($userMemberList) <= 0)
				{
					$this->addUserModel($spouseUserCredentialId,$memberModal->memberid,Yii::$app->user->identity->institutionid,'S');
				}
			}
		
			$tempMemberMailModal->temp_approved = 1;
			if ($tempMemberMailModal->save(false)) {	 
				return array('isApproved'=>$isApproved,'allAccept'=>$allAccept, 'allReject'=>$allReject);
			} else {
				return false;
			}
		} else{
			return false;
		}
	}
	/**
	 * add details into usermodel table
	 * @param unknown $memberUserCredentialId
	 * @param unknown $memberId
	 * @param unknown $institusionId
	 * @param unknown $type
	 */
	
	protected function addUserModel($memberUserCredentialId,$memberId,$institusionId,$type)
	{
		
		$userMemberModel = ExtendedUserMember::find()
    			->where(['memberid' => $memberId])
    			->andWhere(['institutionid' => $institusionId])
    			->andWhere(['usertype' => $type])->one();
    	if (!$userMemberModel) {
    		$userMemberModel = new ExtendedUserMember();
    	}

		$userMemberModel->userid = $memberUserCredentialId;
		$userMemberModel->memberid = $memberId;
		$userMemberModel->institutionid = $institusionId;
		$userMemberModel->usertype = $type;
		if ($userMemberModel->save()) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * To sent the approval details to the member
	 * @param unknown $depentantResponse
	 * @param unknown $memberResponse
	 * @param unknown $model
	 * @param unknown $depentandList
	 * @return string
	 */
	protected function sentApprovalEmailNotification($depentantResponse,$memberResponse,$model,$depentandList)
	{
	
		$mailContent = [];
		$from = yii::$app->user->identity->emailid;
		$to = $model->member_email;
		$institutionName =   $model->institution->name;
		$subject = "Your request for updation of membership data - ". $institutionName;
		$title = '';
		$emailModal = 	new EmailHandlerComponent();
		$mailContent['template'] = 'pending-approvel';
		$srTitle      =  $model->membertitle0['Description'] ? $model->membertitle0['Description']:'' ;
		$firstName  =  $model->firstName ? $model->firstName:' ';
		$middleName =  $model->middleName ? $model->middleName:' ';
		$lastName   =  $model->lastName ? $model->lastName :'';
		$displayName = $srTitle.' '. $firstName . ' '.$middleName. ' '. $lastName;


		if(!empty($memberResponse['member_dob']['value'])) {
            $memberResponse['member_dob']['value'] = date('d-F-Y',strtotimeNew($memberResponse['member_dob']['value']));
        }
        if(!empty($memberResponse['spouse_dob']['value'])) {
            $memberResponse['spouse_dob']['value'] = date('d-F-Y',strtotimeNew($memberResponse['spouse_dob']['value']));
        }
        if(!empty($memberResponse['dom']['value'])) {
            $memberResponse['dom']['value'] = date('d-F-Y',strtotimeNew($memberResponse['dom']['value']));
        }
        if(!empty($memberResponse['member_pic']['value'])) {
            $memberResponse['member_pic']['value'] =  Yii::$app->params['imagePath'].$memberResponse['member_pic']['value'];
        } else {
            $memberResponse['member_pic']['value'] =Yii::$app->params['imagePath'].'/Member/default-user.png';
        }
        if(!empty($memberResponse['spouse_pic']['value'])) {
            $memberResponse['spouse_pic']['value'] =  Yii::$app->params['imagePath'].$memberResponse['spouse_pic']['value'];
        } else {
            $memberResponse['spouse_pic']['value'] =Yii::$app->params['imagePath'].'/Member/default-user.png';
        }
        if(!empty($memberResponse['memberImageThumbnail']['value'])) {
            $memberResponse['memberImageThumbnail']['value'] =  Yii::$app->params['imagePath'].$memberResponse['memberImageThumbnail']['value'];
        } else {
            $memberResponse['memberImageThumbnail']['value'] =Yii::$app->params['imagePath'].'/Member/default-user.png';
        }
        if(!empty($memberResponse['spouseImageThumbnail']['value'])) {
            $memberResponse['spouseImageThumbnail']['value'] =  Yii::$app->params['imagePath'].$memberResponse['spouseImageThumbnail']['value'];
        } else {
            $memberResponse['spouseImageThumbnail']['value'] =Yii::$app->params['imagePath'].'/Member/default-user.png';
        }


		$mailContent['name'] = $displayName;
		$mailContent['approvelDependantDetails'] = $depentantResponse;
		$mailContent['approvelMemberDetails'] = $memberResponse;
		$mailContent['approvelDepentandList'] = $depentandList;
		$mailContent['institutionname'] = $institutionName;

		//Institution logo
        if(!empty($model->institution->institutionlogo)){
            $logo = Yii::$app->params['imagePath'].$model->institution->institutionlogo;
        }
        else{
            $logo = Yii::$app->params['imagePath'].'/institution/institution-icon-grey.png';
        }
		$mailContent['logo'] = $logo;
		$attach = '';
		 
		$temp = $emailModal->sendEmail($from,$to,$title,$subject,$mailContent,$attach);
		
		
		if ($temp){
			return 'success';
		}else{
			return 'Error';
		}
	}
	protected function findModel($id)
	{
		if (($model = ExtendedMember::findOne($id)) !== null) {
			return $model;
		}
		throw new yii\web\NotFoundHttpException('The requested page does not exist.');
	}
	/**
	 * To add user creadential
	 * @param unknown $institutionId
	 * @param unknown $email
	 * @param unknown $password
	 * @param unknown $userType
	 * @param unknown $mobileNo
	 */
	protected  function addUserCredential($institutionId,$email,$password,$userType,$mobileNo,$role=null)
	{
		$userCredentialModel = new ExtendedUserCredentials();
		$userCredentialModel->institutionid = $institutionId;
		$userCredentialModel->emailid = $email;
		$passwordHash = Yii::$app->getSecurity()->generatePasswordHash($password);
		$userCredentialModel->userpin = $passwordHash;
		$userCredentialModel->initiallogin = false;
		$userCredentialModel->usertype = $userType;
		$userCredentialModel->mobileno = $mobileNo;
		$userCredentialModel->created_at = date('Y-m-d H:i:s');
		$userCredentialModel->generateAuthKey();
		if ($userCredentialModel->save(false)) {
			$memberUserCredentialId = $userCredentialModel->id;
			return $memberUserCredentialId;
		} else {
			return false;
		}
	}

	/**
	 * To check valid location cordinates are passed
	 * @param array|null $location
	 * @return bool
	 */
	private function validateLocation(array $location): bool
	{
		if (!isset($location['latitude'], $location['longitude'])) {
			return false;
		}
		if (empty($location['latitude']) || empty($location['longitude'])) {
			return true;
		}		

		// Normalize values (convert to string)
		$latitude = trim((string) $location['latitude']);
		$longitude = trim((string) $location['longitude']);


		if (!is_numeric($latitude) || $latitude < -90 || $latitude > 90) {
			return false; // Invalid latitude
		}

		if (!is_numeric($longitude) || $longitude < -180 || $longitude > 180) {
			return false; // Invalid longitude
		}

		return true;
	}

	/**
	 * Update member location
	 * Updates the location (latitude and longitude) for the authenticated member
	 * @return ApiResponse
	 */
	public function actionUpdateMemberLocation()
	{
		$request = Yii::$app->request;
		$userId = Yii::$app->user->identity->id;
		
		if ($userId) {
			// Get member ID from the authenticated user
			$userMember = ExtendedUserMember::find()
				->where(['userid' => $userId])
				->one();
			
			if (!$userMember) {
				$this->statusCode = 404;
				$this->message = 'Member not found for this user';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode, $this->data, $this->message);
			}
			
			$memberId = $userMember->memberid;
			
			// Get location from POST data
			$latitude = $request->post('latitude', '');
			$longitude = $request->post('longitude', '');
			
			// If both are empty, set location to null
			if (empty($latitude) && empty($longitude)) {
				$locationData = null;
			} else {
				// Validate location format
				$locationArray = [
					'latitude' => $latitude,
					'longitude' => $longitude
				];
				
				$isValid = $this->validateLocation($locationArray);
				if (!$isValid) {
					$this->statusCode = 400;
					$this->message = 'Invalid location format. Latitude must be between -90 and 90, longitude must be between -180 and 180';
					$this->data = new \stdClass();
					return new ApiResponse($this->statusCode, $this->data, $this->message);
				}
				
				// Encode location as JSON
				$locationData = json_encode($locationArray);
			}
			
			// Update member location
			$member = ExtendedMember::findOne($memberId);
			if (!$member) {
				$this->statusCode = 404;
				$this->message = 'Member not found';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode, $this->data, $this->message);
			}
			
			$member->location = $locationData;
			
			if ($member->save(false)) {
				$responseLocation = ['latitude' => '', 'longitude' => ''];
				if ($locationData) {
					$decodedLocation = json_decode($locationData, true);
					$responseLocation = [
						'latitude' => $decodedLocation['latitude'] ?? '',
						'longitude' => $decodedLocation['longitude'] ?? ''
					];
				}
				
				$data = [
					'location' => $responseLocation
				];
				
				$this->statusCode = 200;
				$this->message = 'Location updated successfully';
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data, $this->message);
			} else {
				$this->statusCode = 500;
				$this->message = 'Failed to update location';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode, $this->data, $this->message);
			}
		} else {
			$this->statusCode = 498;
			$this->message = 'Session invalid';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode, $this->data, $this->message);
		}
	}

	/**
	 * Sync member connections
	 * Inserts connections if not exist, deletes if not in the provided list
	 * @return ApiResponse
	 */
	public function actionSyncMemberConnections()
	{
		$request = Yii::$app->request;
		$userId = Yii::$app->user->identity->id;
		
		if ($userId) {
			// Get member ID from the authenticated user
			$userMember = ExtendedUserMember::find()
				->where(['userid' => $userId])
				->one();
			
			if (!$userMember) {
				$this->statusCode = 404;
				$this->message = 'Member not found for this user';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode, $this->data, $this->message);
			}
			
			$memberId = $userMember->memberid;
			
			// Get connection IDs from POST data
			$connectionIds = $request->post('connectionIds', []);
			
			// Validate that connectionIds is an array
			if (!is_array($connectionIds)) {
				$this->statusCode = 400;
				$this->message = 'connectionIds must be an array';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode, $this->data, $this->message);
			}
			
			// Sync the connections
			$result = ExtendedMemberConnection::syncConnections($memberId, $connectionIds);
			
			if ($result['success']) {
				$connectionsList = [];
				foreach ($result['connections'] as $connection) {
					$connectionsList[] = [
						'id' => (string)$connection->id,
						'memberId' => (string)$connection->member_id,
						'connectedMemberId' => (string)$connection->connected_member_id,
						'createdAt' => $connection->created_at
					];
				}
				
				$data = [
					'connections' => $connectionsList,
					'totalConnections' => count($connectionsList)
				];
				
				$this->statusCode = 200;
				$this->message = 'Member connections synced successfully';
				$this->data = $data;
				return new ApiResponse($this->statusCode, $this->data, $this->message);
			} else {
				// Check if it's a validation error (self-connection)
				$errorMessage = $result['error'] ?? 'Unknown error';
				if (strpos($errorMessage, 'cannot connect to themselves') !== false) {
					$this->statusCode = 400;
				} else {
					$this->statusCode = 500;
				}
				$this->message = $errorMessage;
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode, $this->data, $this->message);
			}
		} else {
			$this->statusCode = 498;
			$this->message = 'Session invalid';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode, $this->data, $this->message);
		}
	}
}
