<?php
namespace api\modules\v3\controllers;

use api\modules\v3\models\responses\ApiResponse;
use api\components\ApiRequestHandler;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\base\ActionEvent;
use yii;
use api\modules\v3\models\AuthorizationManager;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\extendedmodels\ExtendedPrivilege;
use common\models\basemodels\RememberAppConstModel;
use common\models\basemodels\BaseModel;
use api\components\ApiAuth;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedUserMember;
use common\models\extendedmodels\ExtendedDashboard;
use common\helpers\Utility;

class AccountController extends \yii\rest\Controller
{

    public $message = "";
    public $statusCode;
    public $data;
    private $responseFromVersionCheck;

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'bootstrap' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON
                ]
            ],
            'authenticator' => [
                    'class' => ApiAuth::className(),
                    'only' => ['logout']
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'logon' => [
                        'POST'
                    ],
                    'logout' => [
                        'POST'
                    ],
                    'validate-otp' => [
                        'POST'
                    ],
                    'generate-otp' => [
                        'POST'
                    ],
                    'reset-password' => [
                        'POST'
                    ],
                    'change-user' => [
                        'POST'
                    ],
                    'check-app-update-available' => [
                        'POST'
                    ]
                ]
            ]
        ]);
    }

    public function init()
    {
        parent::init();
        $this->on(self::EVENT_BEFORE_ACTION, function (ActionEvent $event) {
            switch ($event->action->id) {
                case 'logon':
                    $this->responseFromVersionCheck = $this->versionCheck();
                    break;
            }
        });
    }

    /**
     * Default routing to this action
     */
    public function actionIndex()
    {
        return new ApiResponse(404, new \stdClass(), "Method not found");
    }

    /**
     * Logs the on the user,this method accept only httpp ost method.
     *
     * @param
     *            username
     * @param
     *            password
     * @param
     *            deviceKey
     * @param
     *            deviceIdentifier
     * @return user info
     */
    public function actionLogon()
    {   
        if ($this->responseFromVersionCheck['hasError'] && $this->responseFromVersionCheck['statusCode'] != 200 ) {
            return new ApiResponse($this->responseFromVersionCheck['statusCode'], $this->responseFromVersionCheck['data'], $this->responseFromVersionCheck['message']);
        } else {
            $screenName = "";
            $titleName = "";
            $OTPSent = false;
            $request = \Yii::$app->request;
            $apirequestHandler = new ApiRequestHandler();
            $apirequestHandler->setRequestData();
            if ($apirequestHandler->header->status == 200) {
                $username = $request->getBodyParam('username');
                $password = $request->getBodyParam('password');
                $deviceKey = $request->getBodyParam('deviceKey');
                $deviceIdentifier = $request->getBodyParam('deviceIdentifier');
                $deviceIdentifierLA = $request->getBodyParam('device');
                $deviceType = $apirequestHandler->header->deviceType;
                if ($username != '') {
                    $tokenResponse = new \stdClass();
                    $response = new \stdClass();
                    $feedbackResponse = new \stdClass();
                    $responseNotificationcount = new \stdClass();
                    $committeeresponse = new \stdClass();
                    $institutions = [];
                    $communicationAddresses = [];
                    $featuresEnabled = [];
                    $feedbackTypes = [];
                    $committeeTypes = [];
                    $periods = [];
                    $user = new \stdClass();
                    $counts = new \stdClass();
                    $institutionFeatures = new \stdClass();
                    $authorizations = new \stdClass();
                    
                    $activeAppVersion = null;
                    $appVersionNo = $apirequestHandler->header->appVersion;
                    $appVersionNo = trim(str_replace("ClubApp", "", $appVersionNo));
                    $requestedDeviceType = $apirequestHandler->header->deviceType;
                    
                    switch (strtolower($requestedDeviceType)) {
                        case 'ios':
                            $activeAppVersion = yii::$app->params['runningAppVersions']['ios']['latestAppVersion'];
                            break;
                        case 'android':
                            $activeAppVersion = yii::$app->params['runningAppVersions']['android']['latestAppVersion'];
                            break;
                    }
            
                    $response = AuthorizationManager::validateUser($username, $password, $deviceKey, $deviceType, $deviceIdentifier, $appVersionNo, $activeAppVersion, $deviceIdentifierLA);    
                        
                        if ((! empty($response) && ! $response->Status) && ($response->StatusCode == 601)) {
                            $institution = BaseModel::getUserInstitution($response->value['userid']);
                            if (! empty($institution->value) && count($institution->value) > 0) {
                                $opResponse = BaseModel::updateUserInstitutionandType($institution->value[0]['id'], $response->value['userid'], $response->value['usertype']);
                                if ($opResponse->Status) {
                                    $response = new \stdClass();
                                    $response = AuthorizationManager::validateUser($username, $password, $deviceKey, $deviceType, $deviceIdentifier, $appVersionNo, $activeAppVersion, $deviceIdentifierLA);
                                }
                            }
                        }
                        
                        if ($response && $response->Status === true && ! empty($response->value)) {
                            $userMemberId = ExtendedUserMember::getUserMemberId($response->value['memberid'], $response->value['institutionid'], $response->value['usertype']);
                            if($userMemberId) {
                                if (!yii::$app->checkAdminGroup->checkAppAccessGroup($userMemberId)) {
                                    yii::error('Authorization missing for member.please check auth assignment');
                                    $this->message = "Invalid username or password.";
                                    $this->data = new \stdClass();
                                    $this->statusCode = 500;
                                    return new ApiResponse($this->statusCode, $this->data, $this->message);
                                }
                            }
                            if ($password != null && $password != "" || $deviceIdentifierLA != null && $deviceIdentifierLA != "") {
                                try {
                                    $familyUnits = [];
                                    if ($response->value['institutiontypeid'] == ExtendedInstitution::INSTITUTION_TYPE_CHURCH) {
                                        $familyUnitList = BaseModel::getFamilyUnits($response->value['institutionid']);
                                        if ($familyUnitList->Status === true && ! empty($familyUnitList->value)) {
                                            $dataItem = [];
                                            foreach ($familyUnitList->value as $famliyUnit) {
                                                $dataItem['familyUnit'] = $famliyUnit['description'];
                                                $dataItem['familyUnitId'] = $famliyUnit['familyunitid'];
                                                array_push($familyUnits, $dataItem);
                                            }
                                        }
                                    }
                                    
                                    $committeeresponse = BaseModel::getAllCommitteePeriodsAndTypes($response->value['institutionid']);
                                    
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

                                    $batch = '';
                                    if ($response->value['institutiontypeid'] == ExtendedInstitution::INSTITUTION_TYPE_EDUCATION) {
                                       $batch =  !empty($response->value['batch']) ? $response->value['batch'] : '';
                                    }
                                    
                                    
                                    $user->userId = (string) $response->value['userid'];
                                    $user->memberId = (string) $response->value['memberid'];
                                    $user->isFirstLogin = (empty($response->value['userpin'])) ? true : false;
                                    $user->requiresOtp = $response->RequiresOtp;
                                    $user->otpInfoText = $response->otpInfoText ?? '';
                                    $user->email = ($response->value['emailid']) ? (string)$response->value['emailid'] : "";
                                    $user->mobile = ($response->value['Mobile']) ? $response->value['Mobile'] : "";
                                    $user->screenName = $response->value['firstName'] . ' ' . $response->value['middleName'] . ' ' . $response->value['lastName'];
                                    $user->userThumbnailImage = (! empty($response->value['Photo'])) ? (string)preg_replace('/\s+/', "%20",yii::$app->params['imagePath'] . $response->value['Photo'] ): "";
                                    
                                    $user->institutionId = (string) $response->value['institutionid'];
                                    $user->institutionName = ($response->value['insitutionname']) ? $response->value['insitutionname'] : "";
                                    $user->userType = $response->value['usertype'];
                                    $user->institutionLogo = (! empty($response->value['institutionlogo'])) ? (string)preg_replace('/\s+/', "%20",yii::$app->params['imagePath'] . $response->value['institutionlogo']) : "";
                                    $user->institutionType = (string)$response->value['institutiontypeid'];
                                    $user->moreInfoUrl = ($response->value['moreurl']) ? (string)$response->value['moreurl'] : "";
                                    $user->familyUnits = $familyUnits;
                                    $user->isAdsEnabled = true;
                                    $user->isContactFilterEnabled = (boolean) $response->value['IsAdvancedSearch'];
                                    $user->isTagsEnabled = (boolean) $response->value['tagcloud'];
                                    $user->userGroup = $response->value['membertype'] == 0 ? ($response->value['usertype'] == 'M' ? 0 : 1) : 2;

                                    $user->committeeTypes = $committeeTypes;
                                    $user->batch = $batch;
                                } catch (\Exception $e) {
                                    yii::error($e->getMessage());
                                    $this->message = "An error occurred while processing the request";
                                    $this->statusCode = 500;
                                    $this->data = new \stdClass();
                                    return new ApiResponse($this->statusCode, $this->data, $this->message);

                                }
                                $responseAddressType = BaseModel::getAddressType();
                                if ((! empty($responseAddressType) && $responseAddressType->Status === true) && ! empty($responseAddressType->value)) {
                                    foreach ($responseAddressType->value as $addresstype) {
                                        $_communicationAddresses = [];
                                        $_communicationAddresses['addressTypeId'] = (string) $addresstype['id'];
                                        $_communicationAddresses['addressDescription'] = (string) $addresstype['type'];
                                        array_push($communicationAddresses, $_communicationAddresses);
                                    }
                                } else {
                                    $this->message = "An error occurred while processing the request";
                                    $this->statusCode = 500;
                                    $this->data = new \stdClass();
                                    return new ApiResponse($this->statusCode, $this->data, $this->message);
                                }
                                
                                $responseInstitution = [];
                                $responseInstitutionDetails = [];
                                // all privileges of the user
                                $responsePrivileges = BaseModel::getAllAppUserPrivileges($response->value['memberid'], $response->value['institutionid'], $response->value['usertype']);
                                $feedbackResponse = BaseModel::getFeedbackTypesByUnderInstitution($response->value['institutionid'], true);
                                $responseInstitution = BaseModel::getUserInstitution($response->value['userid']);
                                if (! empty($responseInstitution) && $responseInstitution->Status === true && ! empty($responseInstitution->value)) {
                                    
                                    foreach ($responseInstitution->value as $key => $eachValue) {
                                        $list = $eachValue['DashboardList'];
                                        $featuresEnabled = [];
                                        if (! empty($eachValue['DashboardList']) && count($eachValue['DashboardList']) > 0) {
                                            $rolePrivileges = new \stdClass();
                                            $responseInstitutionDetails = BaseModel::getInstitutionFeatureEnabledValue($eachValue['id']);
                                            $committee = BaseModel::checkUserAvailableInCommittee($response->value['userid'], $eachValue['id']);
                                            foreach ($list as $k => $item) {
                                                if(($responseInstitutionDetails->value['prayerrequestenabled'] == 0) && ($item['dashboardid'] == ExtendedDashboard::PRAYERREQUEST)) {
                                                    unset($list[$k]);   
                                                    continue;
                                                }
                                                if(!$committee && $item['dashboardid'] == ExtendedDashboard::CONVERSATION) {
                                                    unset($list[$k]);
                                                    continue;   
                                                }  
                                                if(($responseInstitutionDetails->value['feedbackenabled'] == 0 ) && $item['dashboardid'] == ExtendedDashboard::FEEDBACK) {
                                                    unset($list[$k]);
                                                    continue; 
                                                }
                                                if($responseInstitutionDetails->value['moreenabled'] ==0 && ($item['dashboardid'] == ExtendedDashboard::MORE)) {
                                                    unset($list[$k]);
                                                    continue;
                                                }
                                                $_feature = array(
                                                    "featureId" => (!empty($item['dashboardid']) ? (int)$item['dashboardid'] : 0)
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
                                        $_institutions = array(
                                            "institutionId" => (string) $eachValue['id'],
                                            "institutionName" => (string) $eachValue['name'],
                                            "institutionLogo" => (! empty($response->value['institutionlogo'])) ? (string)preg_replace('/\s+/', "%20",yii::$app->params['imagePath'] . $response->value['institutionlogo']) : "",
                                            "userGroup" => $response->value['membertype'] == 0 ? ( $response->value['usertype'] == 'M' ? 0 : 1 ): 2,
                                            "featuresEnabled" => $featuresEnabled
                                        );
                                        array_push($institutions, $_institutions);
                                    }
                                } else {
                                    $this->message = "An error occurred while processing the request";
                                    $this->statusCode = 500;
                                    $this->data = new \stdClass();
                                    return new ApiResponse($this->statusCode, $this->data, $this->message);
                                }
                                
                                $responseNotificationcount = BaseModel::getNotificationsLoginCount($response->value['userid'], $response->value['timezone']);
                                if (!empty($responseNotificationcount) && $responseNotificationcount->Status === true && $responseNotificationcount->value != null) {
                                    $conversationCount = new \stdClass();
                                    $billCount = new \stdClass();
                                    $adminCountResponse = new \stdClass();
                                    $conversationCount = BaseModel::getUnreadConversationCount($response->value['userid']);
                                    $totalUnreadConversation = 0;
                                    $unreadbillcount = 0;
                                    $adminCount = 0;
                                    $foodOrderCount = 0;
                                    $billCount = BaseModel::getBillSeenCount($response->value['memberid'], $response->value['usertype'], $response->value['institutionid']);
                                    
                                    if ($conversationCount->Status === true && !empty($conversationCount->value)) {
                                        $totalUnreadConversation = $conversationCount->value['unreadconversationcount'];
                                    }
                                    if ($billCount != null && $billCount->value != null) {
                                        $unreadbillcount = $billCount->value['billseencount'];
                                    }
                                }
                                $adminCountResponse = BaseModel::getManagementCounts($response->value['userid'],$response->value['institutionid']);
                                
                                if ($adminCountResponse->Status === true && $adminCountResponse != null && $adminCountResponse->value != null) {
                                    $adminCount = $adminCountResponse->value->PrayerRequestCount['count(result.id)'] + $adminCountResponse->value->RSVPCount['sum(result.count)'] + $adminCountResponse->value->ProfileApprovalCount['count(result.id)'] + $adminCountResponse->value->FeedbackCount['count(result.id)'] + $adminCountResponse->value->PendingAlbumCount['count(result.id)'] + $adminCountResponse->value->FoodOrdrCount['ordercount'];
                                    $foodOrderCount = $adminCountResponse->value->FoodOrdrCount['ordercount'];
                                }
                                $counts = array(
                                    "announcementCount" => (int)$responseNotificationcount->value['announcementcount'],
                                    "birthdayCount" => (int)($responseNotificationcount->value['memberbirthday'] + $responseNotificationcount->value['spousebirthday']),
                                    "anniversaryCount" => (int)$responseNotificationcount->value['weddingannuversery'],
                                    "eventsCount" => (int)$responseNotificationcount->value['eventcount'],
                                    "conversationCount" => (int)$totalUnreadConversation,
                                    "billsCount" => (int)$unreadbillcount,
                                    "foodOrderCount" => (int)$foodOrderCount,
                                    "adminOptionCount" => (int)$adminCount
                                );
                                if ($response->value['feedbackenabled']) {
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
                                
                                if ($response != null && $response->Status === true) {
                                    $institutionFeatures = array(
                                        "feedbackFeatureAvailable" => ($response->value['feedbackenabled'] == true && count($feedbackTypes) > 0) ? true : false,
                                        "paymentFeatureAvailable" => (bool) $response->value['paymentoptionenabled'],
                                        "isDemoEnabled" => false,
                                        "feedbackTypes" => $feedbackTypes
                                    );
                                } else {
                                    $this->message = "An error occurred while processing the request";
                                    $this->statusCode = 500;
                                    $this->data = new \stdClass();
                                    return new ApiResponse($this->statusCode, $this->data, $this->message);
                                }
                                
                                $committeefound = BaseModel::checkUserAvailableInCommittee($response->value['userid'], $response->value['institutionid']);
                                
                                $authorizations = array(
                                    "committeeMember" => (!$committeefound) ? false :true
                                );
                                
                                $committeeList = [];

                                $committeeListResponse = BaseModel::getMemberCommitteeForUser($response->value['userid']);
                                if($committeeListResponse->Status === true && !empty($committeeListResponse->value)) {
                                    $committeeList = $committeeListResponse->value;
                                }

                                $memberCommittee = [];
                                if ($committeeList != null && count($committeeList) > 0) {
                                    foreach ($committeeList as $eachCommitee) {
                                        $dataItem = (object) [
                                            'institutionId' => (string)$eachCommitee['InstitutionId']
                                        ];
                                        
                                        array_push($memberCommittee, $dataItem);
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
                                $data = array(
                                    "authToken" => $response->token,
                                    "user" => $user,
                                    "communicationAddresses" => $communicationAddresses,
                                    "institutions" => $institutions,
                                    "counts" => $counts,
                                    "authorizations" => $authorizations,
                                    "committeeMemberOf" => $memberCommittee,
                                    "institutionFeatures" => $institutionFeatures,
                                    "accessPrivilege" => $accessPrivilege
                                );
                                
                                $this->data = $data;
                                $this->statusCode = 200;
                                $this->message = $response->ErrorMessage;
                                // end with password
                            } else {
                                $user = array(
                                    
                                    "memberId" => (string) $response->value['memberid'],
                                    "isFirstLogin" => (empty($response->value['userpin'])) ? true : false,
                                    "requiresOtp" => $response->RequiresOtp,
                                    "otpInfoText" => $response->otpInfoText ?? '',
                                    "screenName" => $response->value['firstName'] . ' ' . $response->value['middleName'] . ' ' . $response->value['lastName'],
                                    "userId" => (string) $response->value['userid'],
                                    "institutionId" => (string) $response->value['institutionid']
                                );
                                
                                $data = array(
                                    "authToken" => $response->token,
                                    "user" => $user
                                );
                                $this->data = $data;
                                $this->statusCode = 200;
                                $this->message = $response->ErrorMessage;
                            }
                        } else {
                            $this->data = new \stdClass();
                            $this->statusCode = $response->StatusCode;
                            $this->message = $response->ErrorMessage;
                        }
                } else {
                    $this->message = "Invalid username / password";
                    $this->statusCode = 500;
                    $this->data = new \stdClass();
                }
            } else {
                $this->message = "Required header missing";
                $this->statusCode = 500;
                $this->data = new \stdClass();
            }
            return new ApiResponse($this->statusCode, $this->data, $this->message);
        }
    }

    protected function versionCheck()
    {
        $apirequestHandler = new ApiRequestHandler();
        $apirequestHandler->setRequestData();
        if ($apirequestHandler->header->status == 200) {
            $requestedDeviceType = $apirequestHandler->header->deviceType;
            $appVersionNo = $apirequestHandler->header->appVersion;
            $description = "";
            $forceUpdate = [];
            $requireForceUpdate = false;
            $activeAppVersion = null;
            $minimumAppVersion = null;
            switch (strtolower($requestedDeviceType)) {
                case 'ios':
                    $activeAppVersion = yii::$app->params['runningAppVersions']['ios']['latestAppVersion'];
                    $forceUpdate = yii::$app->params['runningAppVersions']['ios']['forceUpdate'];
                    $description = yii::$app->params['runningAppVersions']['ios']['description'];
                    $minimumAppVersion = yii::$app->params['runningAppVersions']['ios']['minimumAppVersion'];
                    break;
                case 'android':
                    $activeAppVersion = yii::$app->params['runningAppVersions']['android']['latestAppVersion'];
                    $forceUpdate = yii::$app->params['runningAppVersions']['android']['forceUpdate'];
                    $description = yii::$app->params['runningAppVersions']['android']['description'];
                    $minimumAppVersion = yii::$app->params['runningAppVersions']['android']['minimumAppVersion'];
                break;
            }
            
            
            if($activeAppVersion && $appVersionNo ) {
                if(version_compare($appVersionNo, $activeAppVersion , '<' )) {

                    if(in_array((string)$appVersionNo, $forceUpdate))
                        $requireForceUpdate = true;

                    if ($requireForceUpdate || version_compare($appVersionNo, $minimumAppVersion, '<' )) {
                        return [
                            'hasError' => true,
                            'message' => $description,
                            'data' => new \stdClass(),
                            'statusCode' => 600
                        ]; 
                    } else {
                        return [
                            'hasError' => true,
                            'message' => $description,
                            'data' => new \stdClass(),
                            'statusCode' => 200
                        ];
                    }
                } else {
                    return [
                        'hasError' => false,
                        'message' => "",
                        'data' => new \stdClass(),
                        'statusCode' => 200
                    ];
                            
                }
            } else {
                return [
                    'hasError' => true,
                    'message' => "Required header missing",
                    'data' => new \stdClass(),
                    'statusCode' => 500
                ];
            }
        } else {
            return [
                'hasError' => true,
                'message' => "Required header missing",
                'data' => new \stdClass(),
                'statusCode' => 500
            ];
        }
    }

    /**
     * Validates the otp.
     * @param The mobile number
     * @param The otp
     */
    public function actionValidateOtp()
    {
        $request = \Yii::$app->request;
        $apirequestHandler = new ApiRequestHandler();
        $apirequestHandler->setRequestData();
        $response = new \stdClass();
        if ($apirequestHandler->header->status == 200) {
            $mobileNumber = (string) $request->getBodyParam('mobileNumber');
            $otp = (string) $request->getBodyParam('otp');
            if ($mobileNumber && $otp) {
                $response = AuthorizationManager::validateOtp($mobileNumber, $otp);
                if (! empty($response) && $response->Status === true) {
                    $this->message = $response->ErrorMessage; // success
                    $this->statusCode = $response->ErrorCode; // success code
                    $this->data = new \stdClass();
                } else {
                    $this->message = $response->ErrorMessage;
                    $this->statusCode = $response->ErrorCode;
                    $this->data = new \stdClass();
                }
            } else {
                $this->message = "Required parameter(s) missing";
                $this->statusCode = 500;
                $this->data = new \stdClass();
            }
        } else {
            $this->message = "Required header missing";
            $this->statusCode = 500;
            $this->data = new \stdClass();
        }
        return new ApiResponse($this->statusCode, $this->data, $this->message);
    }

    /**
     * generate the otp.
     * @param The mobile number
     */
    public function actionGenerateOtp()
    {
        $request = \Yii::$app->request;
        $apirequestHandler = new ApiRequestHandler();
        $apirequestHandler->setRequestData();
        $response = new \stdClass();
        if ($apirequestHandler->header->status == 200) {
            $mobileNumber = (string) $request->getBodyParam('mobileNumber');
            if ($mobileNumber) {
                $response = yii::$app->communicationManager->generateOtp($mobileNumber);
                if (! empty($response) && $response->Status === true) {
                    $this->message = $response->ErrorMessage;
                    $this->statusCode = $response->ErrorCode;
                    $this->data = new \stdClass();
                    $this->data->otpInfoText = $response->otpInfoText ?? '';
                } else {
                    $this->message = "An error occurred while processing the request";
                    $this->statusCode = 500;
                    $this->data = new \stdClass();
                }
            } else {
                $this->message = "Required parameter missing";
                $this->statusCode = 500;
                $this->data = new \stdClass();
            }
        } else {
            $this->message = "Required header missing";
            $this->statusCode = 500;
            $this->data = new \stdClass();
        }
        return new ApiResponse($this->statusCode, $this->data, $this->message);
    }

    /**
     * Resets the passcode.
     * @param mobile number
     * @param passcode
     */
    public function actionResetPassword()
    {
        $request = \Yii::$app->request;
        $headers = $request->headers;
        $apirequestHandler = new ApiRequestHandler();
        $apirequestHandler->setRequestData();
        $response = new \stdClass();
        if ($apirequestHandler->header->status == 200) {
            $isFirstLogin = (boolean) $request->getBodyParam('isFirstLogin');
            $passcode = (string) $request->getBodyParam('newPassword');
            
            if ($headers->has('authToken')) {
                $token = $headers->get('authToken');
                if ($passcode) {
                    $response = AuthorizationManager::resetPasscode($token, $passcode, $isFirstLogin);
                    if (! empty($response)) {
                        $this->statusCode = $response->statusCode;
                        $this->message = $response->ErrorMessage;
                        $this->data = new \stdClass();
                    } else {
                        $this->message = "An error occurred while processing the request";
                        $this->statusCode = 500;
                        $this->data = new \stdClass();
                    }
                } else {
                    $this->message = "Required parameter missing";
                    $this->statusCode = 500;
                    $this->data = new \stdClass();
                }
            } else {
                $this->message = "Authenication failed.";
                $this->statusCode = 500;
                $this->data = new \stdClass();
            }
        } else {
            $this->message = "Required header missing";
            $this->statusCode = 500;
            $this->data = new \stdClass();
        }
        return new ApiResponse($this->statusCode, $this->data, $this->message);
    }

    /**
     * This web-service is used to end the users active session.
     */
    public function actionLogout()
    { 
        Yii::$app->user->logout();
        $this->message = "Logout successfully";
        $this->statusCode = 200;
        $this->data = new \stdClass();  
        return new ApiResponse($this->statusCode, $this->data, $this->message);
    }

    public function actionChangeUser()
    {
        $request = \Yii::$app->request;
        $apirequestHandler = new ApiRequestHandler();
        $apirequestHandler->setRequestData();
        $response = new \stdClass();
        $isMail = false;
        if ($apirequestHandler->header->status == 200) {
            $userName = $request->getBodyParam('username');
            $deviceKey = $request->getBodyParam('deviceKey');
            if ($userName && $deviceKey) {
                if (filter_var($userName, FILTER_VALIDATE_EMAIL)) {
                    $isMail = true;
                }
                if(!$isMail){
                    $response = ExtendedUserCredentials::findByMobileNumber($userName);    
                } else {
                    $response = ExtendedUserCredentials::findByUsername($userName); 
                }
                if(!empty($response)) {
                    $deviceDetails = $response->deviceDetails;
                    $sql = "DELETE from notificationlog where deviceid =:id";
                    try {
                        if(!empty($deviceDetails) && $deviceDetails->deviceid === $deviceKey ) {
                        Yii::$app->db->createCommand($sql)->bindValue(":id", $deviceDetails->id)->execute();
                        if ($deviceDetails->delete()) {
                            $this->message = "";
                            $this->statusCode = 200;
                            $this->data = new \stdClass();
                        }
                    } else {
                        $this->message = "";
                        $this->statusCode = 200;
                        $this->data = new \stdClass();
                    }  
                    } catch (Exception $e) {
                        $this->message = "Something went wrong.Please try again.";
                        $this->statusCode = 500;
                        $this->data = new \stdClass();         
                    }
                } else {
                    $this->message = "Session has expired. Please login again.";
                    $this->statusCode = 498;
                    $this->data = new \stdClass();      
                }
                
            } else {
                $this->message = "Required parameter missing";
                $this->statusCode = 500;
                $this->data = new \stdClass();
            }
        } else {
            $this->message = "Service temporarly unavaiable.Please restart the app and try again";
            $this->statusCode = 500;
            $this->data = new \stdClass();
        }
        return new ApiResponse($this->statusCode, $this->data, $this->message);
    }
    public function actionCheckAppUpdateAvailable() 
    {
        $request = \Yii::$app->request;
        $requestedAppVersion = $request->post("versionName");
        $headers = $request->headers;
        $requestedDeviceType = $headers->has('devicetype') ? strtolower($headers->get('devicetype')) : "";
        $appLatestVersion = null;
        $appMinimumVersion = null;
        $forceUpdate = [];
        $requireForceUpdate = false;
        $response = new \stdClass();
        $response->isUpdateAvailable = false;
      
        switch ($requestedDeviceType) {
            case 'ios':
                $forceUpdate = yii::$app->params['runningAppVersions']['ios']['forceUpdate'];
                $appLatestVersion = yii::$app->params['runningAppVersions']['ios']['latestAppVersion'];
                $appMinimumVersion = yii::$app->params['runningAppVersions']['ios']['minimumAppVersion'];
                $description = yii::$app->params['runningAppVersions']['ios']['description'];
                break;
            case 'android':
                $forceUpdate = yii::$app->params['runningAppVersions']['android']['forceUpdate'];
                $appLatestVersion = yii::$app->params['runningAppVersions']['android']['latestAppVersion'];
                $appMinimumVersion = yii::$app->params['runningAppVersions']['android']['minimumAppVersion'];
                $description = yii::$app->params['runningAppVersions']['android']['description'];
                break;
        }
            
        if($appLatestVersion && $requestedAppVersion ) {
            if(version_compare($requestedAppVersion, $appLatestVersion , '<' )) {
                
                if(in_array((string)$requestedAppVersion, $forceUpdate))
                    $requireForceUpdate = true;

                if ($requireForceUpdate || version_compare($requestedAppVersion, $appMinimumVersion, '<' )) {
                    $response->isUpdateAvailable = true;
                    $response->description = $description;
                    $response->version = $appLatestVersion;
                    $this->message = "";
                    $this->statusCode = 600;
                    $this->data = $response;
                } else {
                    $response->isUpdateAvailable = true;
                    $response->description = $description;
                    $response->version = $appLatestVersion;
                    $this->message = "";
                    $this->statusCode = 200;
                    $this->data = $response;
                }
            } else {
                $response->isUpdateAvailable = false;
                $response->description = "";
                $response->version = "";
                $this->message = "";
                $this->statusCode = 200;
                $this->data = $response;
            }
        } else {
            $this->message = "An error occurred while processing the request";
            $this->statusCode = 500;
            $this->data = new \stdClass();
        }
        return new ApiResponse($this->statusCode, $this->data, $this->message);
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
    
}