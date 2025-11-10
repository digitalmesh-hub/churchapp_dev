<?php 

namespace backend\controllers;

use yii;
use backend\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\formmodels\CreateAdminForm;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedUserProfile;
use yii\helpers\ArrayHelper;
use common\models\basemodels\CustomRoleModel;
use common\models\formmodels\LoginForm;
use common\models\formmodels\UpdateAdminPassword;
use yii\helpers\Url;
use common\models\extendedmodels\ExtendedRoleGroup;
use yii\base\ActionEvent;
use common\models\formmodels\BillFormModel;

/**
* 
*/
class AccountController extends BaseController
{
    public $defaultAction = 'login';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                    'only' => [
                            'login',
                            'logout',
                            'home',
                            'manage-admin',
                            'list-admin',
                            'update-admin',
                            'deactivate',
                            'activate',
                            'impersonate',
                            'exit-impersonate',
                            'edit-admin-profile'
                    ],
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['login'],
                            'roles' => ['?'],
                        ],
                        [
                            'allow' => false,
                            'actions' => ['login'],
                            'roles' => ['@'],
                            'denyCallback' => function ($rule, $action) {
                                //to redirect to  home
                                return $this->redirect('/');
                            }
                        ],
                        [
                            'allow' => true,
                            'actions' => ['logout', 'exit-impersonate', 'edit-admin-profile'],
                            'roles' => ['@'],
                        ],
                        [
                            'allow' => true,
                            'actions' => [
                                'list-admin',
                                'manage-admin',
                                'update-admin',
                                'deactivate',
                                'activate',
                                'home',
                                'impersonate'
                            ],
                            'roles' => ['superadmin'],
                            'denyCallback' => function ($rule, $action) {
                                //to redirect to  home
                                return $this->redirect('/');
                            }    
                        ],
                        [
                            'allow' => true,
                            'actions' => ['home','edit-admin-profile','dep-drop'],
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                return Yii::$app->checkAdminGroup->checkAdminGroupAccess($this->currentUserId());
                            },
                            
                        ],
                        
                       
                ],
            ],
        ];
    }


    function beforeAction($action)
    {
        $this->on(self::EVENT_BEFORE_ACTION,function(ActionEvent $event)
        {
           if (in_array($event->action->id,['old-system-support'])) {
                $this->enableCsrfValidation = false;    
           }
        });
        return parent::beforeAction($action);
    }
// apns push notification test
    public function actionTesting()
    {
        die('not fine');
        /* 
        $member_userid =  $userMemberId = ExtendedUserMember::getUserMemberId(970, 59,ExtendedMember::USER_TYPE_MEMBER);
        $spouse_userid =  $userMemberId = ExtendedUserMember::getUserMemberId(970, 59,ExtendedMember::USER_TYPE_SPOUSE);
        if ($member_userid) {
            $memberRoleData = CustomRoleModel::loadMemberRole($member_userid);
            if(!empty($memberRoleData)) {
                $selectedMemberCat = $memberRoleData['RoleCategoryID'];
            }
        }
        echo "<pre>";
        print_r($memberRoleData);die; */
        
        //  $OTP = str_pad (rand(0, pow(10, 4)-1), 4, '0', STR_PAD_LEFT);
        // // $OTPMessage = "Greetings from Re-member! " . (string)$OTP . " is the OTP to verify your mobile no. Verify and enjoy using Re-member app. - RE-MEMBER";
        // $OTPMessage = "Please use the OTP " . (string)$OTP . " to verify your mobile number. Do not share this verification code with anyone - RE-MEMBER";
        // $response = yii::$app->textMessageHandler->sendSMS(9400984372, $OTPMessage);
        // echo  $OTPMessage;
        // echo "<pre>";
        // print_r($response); die('here'); 
        /* $data = [
            "institutionid" => 66,
            "year" => '2021',
            "month" => '5' 
        ];
        $institutionName = "DM Challengers";
        $bills = BillFormModel::checkBillAlreadyExist($data);
        $id = date('ymdhi');
        if(empty($bills)) {
            $success = BillFormModel::insertUploadedBills($data);
        }
        else {
            $data['created'] = $bills['created']; 
            $success =  BillFormModel::updateUploadedBills($data);
        }
        $createdBy = 55;

        Yii::$app->consoleRunner->run('bill-notification/bill-notification  --eventType="P" --eventId='.$id.' --year="'.$data['year'].'" --institutionName="'.$institutionName.'" --institutionId='.$data['institutionid'].' --month='.$data['month'].' --createdBy='.$createdBy);
        
        echo "<pre>";
        print_r($success);
            die; */
        $pushNotificationSender = Yii::$app->PushNotificationHandler;
        $requestData ['contentTitle'] = 'Notifications Test';
        $requestData ['aps'] = [
            'alert' =>[
                    'body' => '',
            ],
            'badge' => (int)5,
            'sound' => '',
            "contentAvailable" => false
        ];
        
        
        // $response  = $pushNotificationSender->sendNotification('ios', '681e7bb3c7bc146e6dacb2ba4caafaa83dee030379b59d2352eaa52486642c22', $requestData);
        $requestData 	= [];
    	$requestData['contentTitle']   = 'Notifications Test';
    	$requestData['message']		   = "test";
    	$requestData['type']		   = "";
    	$requestData['item-id']	       = 0;
    	$requestData['institution']	   = "Digital Mesh Softech India (P) Limited";
    	$requestData['institution-id'] = 76;
        $requestData['member-id'] = 10079;
        $requestData = [
            'token' => 'eX4Eir3KTsK-8VjNPAvLkO:APA91bH9rbz3Dw0AEzxtv1uow-g9FmdlHNukFxz-Dwh_uVNe8WgkYOdIc7bq3tui2OpBcCz71b-Lg6fLtzIwi2_tAJ_Fvli2MJsqzXxORxdHCP0HgqBR7aQ',
            'data' => [
                'contentTitle' => 'Notifications Test',
                'message' => 'test',
                'type' => 'birthday',
                'item-id' => "0",
                'institution' => 'Digital Mesh Softech India (P) Limited',
                'institution-id' => "76",
                'member-id' => "10079"
            ]
        ];
        $token = "eX4Eir3KTsK-8VjNPAvLkO:APA91bH9rbz3Dw0AEzxtv1uow-g9FmdlHNukFxz-Dwh_uVNe8WgkYOdIc7bq3tui2OpBcCz71b-Lg6fLtzIwi2_tAJ_Fvli2MJsqzXxORxdHCP0HgqBR7aQ";
        // $response = $pushNotificationSender->getAccessToken();
        $notificationData = $pushNotificationSender->setPushNotificationRequest(
            $token,
            "test message from digitalmesh",
            "birthday",
            76,
            0,
            "Digital Mesh Softech India (P) Limited",
            'android',
            null,
            10079

        );
        //Android Testing
       $response = $pushNotificationSender->sendNotification('android', $token, $notificationData);
        $data = [
            'contentTitle' => 'Notifications Test',
            'message' => 'test',
            'type' => 'birthday',
            'item-id' => "0",
            'institution' => 'Digital Mesh Softech India (P) Limited',
            'institution-id' => "76",
            'member-id' => "10079"
        ];
    	$notificationType = 'profile-approval';
    	$requestData  = $pushNotificationSender->setPushNotificationRequest(10079,$data['message'],$notificationType,76,$data['member-id'],'DM Challengers Test','ios',8185);
    	//IOS Testing
        $response = $pushNotificationSender->sendNotification('ios', 'f48d449be49a628b5b82911be9c89d2580f3f68a085e15b93db6ca8a07ef66ed', $requestData);
        if (!empty($response)) {
            echo "success";
        } else {
            echo "failed";
        }
    }

    private function testMail()
    {
        $IsMailSent = false;
        try {
            $mailContent['content'] = "<p>testing</p>";
                $mailContent['template'] = "send-otp";

			   $IsMailSent = yii::$app->EmailHandler->sendEmail(
				're-member@digitalmesh.in',
				'manu@digitalmesh.com',
				[],
				'OTP - Re-member app',
				$mailContent,
				null,
				null
			);
        } catch (\Exception $ex) {
            echo $ex->getMessage().PHP_EOL;
        }
        
        print_r($IsMailSent);
        if ($IsMailSent) {
        }
        echo PHP_EOL."test mail";
        die;
    }

    public function actionManageAdmin()
    {
        $institutions = $this->getInstitutions();
        $userCredentialModel = new ExtendedUserCredentials();
        $userCredentialModel->scenario = $userCredentialModel::SCENARIO_CREATE;
        $userProfileModel = new ExtendedUserProfile();
        if (Yii::$app->request->isPost) {
            if (($userCredentialModel->load(Yii::$app->request->post())) && $userProfileModel->load(Yii::$app->request->post())) {
                $userCredentialModel->generateAuthKey();
                $userCredentialModel->usertype = 'A';
                $userCredentialModel->created_at = date('Y-m-d H:i:s');
                if ($userCredentialModel->validate()) {
                        $userCredentialModel->setPassword($userCredentialModel->password);
                    if ($userCredentialModel->save(false)) {
                        $userProfileModel->userid = $userCredentialModel->id;
                        $userProfileModel->emailid = $userCredentialModel->emailid;
                        $userProfileModel->institutionid = $userCredentialModel->institutionid;
                        $userProfileModel->mobilenumber = $userCredentialModel->mobileno;
                        if ($userProfileModel->validate() && $userProfileModel->save(false)) {
                            $auth = Yii::$app->authManager;
                            $role = $auth->getRole($userCredentialModel->role);
                            if (!empty($role)) {
                                    $auth->assign($role, $userCredentialModel->id);
                                    $this->sessionAddFlashArray('success', 'Successfully saved admin', true);
                                    return $this->redirect('list-admin');
                            } else {
                                $userProfileModel->delete();
                                $userCredentialModel->delete();
                                $this->sessionAddFlashArray('error', 'unable to fetch role', true);
                            }
                        } else {
                            $userCredentialModel->delete();
                            $this->sessionAddFlashArray('error', $userProfileModel->getErrors(), true);
                        }
                    } else {
                        $this->sessionAddFlashArray('error', $userCredentialModel->getErrors(), true);
                    }
                } else {
                        $this->sessionAddFlashArray('error', $userCredentialModel->getErrors(), true);
                }
                return $this->redirect('manage-admin');
            }
        }
        return $this->render(
            'create_admin',
            [
                'institutions' => $institutions,
                'userCredentialModel' => $userCredentialModel,
                'userProfileModel' => $userProfileModel
            ]
        );
    }
    public function actionListAdmin()
    {
        $auth = Yii::$app->authManager;
        return $this->render('list-admin');
    }
    public function actionUpdateAdmin($id)
    {
        $userProfileModel = $this->findModel($id);
        $institutions = $this->getInstitutions();
        $userCredentialModel = $this->findUserCredentialModel($userProfileModel->userid);
        $userCredentialModel->scenario = $userCredentialModel::SCENARIO_UPDATE;
        
        $oldPassword = $userCredentialModel->password;
        $userCredentialModel->password = "";
        if (Yii::$app->request->isPost) {
            if ($userCredentialModel->load(Yii::$app->request->post())
                && $userProfileModel->load(Yii::$app->request->post())) {
                $userCredentialModel->updated_at = date('Y-m-d H:i:s');
                if ($userCredentialModel->validate()) {
                    if (!empty($userCredentialModel->password)) {
                     
                        $userCredentialModel->setPassword($userCredentialModel->password);
                    } else {
                        $userCredentialModel->password = $oldPassword;
                    }
                    if ($userCredentialModel->update(false)) {
                        $userProfileModel->emailid = $userCredentialModel->emailid;
                        $userProfileModel->mobilenumber = $userCredentialModel->mobileno;
                        if ($userProfileModel->validate()) {
                            $userProfileModel->update(false);
                            $auth = Yii::$app->authManager;
                            if ($auth->revokeAll($userCredentialModel->id)) {
                                $role = $auth->getRole($userCredentialModel->role);
                                if (!empty($role)) {
                                    $auth->assign($role, $userCredentialModel->id);
                                     $this->sessionAddFlashArray('success', 'Successfully updated admin', true);
                                    return $this->redirect(['list-admin']);
                                } else {
                                    $this->sessionAddFlashArray('error', 'unable to fetch role', true);
                                }
                            } else {
                                $this->sessionAddFlashArray('error', 'Something went wrong. Please try again', true);
                            }
                        } else {
                            $this->sessionAddFlashArray('error', $userProfileModel->getErrors(), true);
                        }
                    } else {
                        $this->sessionAddFlashArray('error', $userCredentialModel->getErrors(), true);
                    }
                } else {
                    $this->sessionAddFlashArray('error', $userCredentialModel->getErrors(), true);
                }
            } else {
                $this->sessionAddFlashArray('error', 'Unable to process you request', true);
            }
            return $this->redirect(['update-admin', 'id' => $userProfileModel->id]);
        }
        return $this->render(
            'create_admin',
            [
                'userProfileModel' => $userProfileModel,
                'userCredentialModel' => $userCredentialModel,
                'institutions' => $institutions
            ]
        );
        
    }
    protected function getInstitutions()
    {
        return ArrayHelper::map(
            ExtendedInstitution::find()
            ->select(['id', 'name'])
            ->where(['active' => 1])
            ->orderBy('name')->all(),
            'id',
            'name'
        );
    }
    protected function findModel($id)
    {
        if (($model = ExtendedUserProfile::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    protected function findUserCredentialModel($id)
    {
        if (($model = ExtendedUserCredentials::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionDepDrop()
    {
        $request = Yii::$app->request;
        $institutionId = $request->post('institutionId');
        $roleCategoryId = $request->post('roleCategoryId');
        $userId = $request->post('userId');
        if ($userId) {
            $previousData = CustomRoleModel::loadRoleForUpdate($userId);
        }
        $type = $request->post('type');
        if ($institutionId && $type === 'role-category') {
            $roleCategory = CustomRoleModel::getInstitutionAllRoleCategories($institutionId, ExtendedRoleGroup::ADMIN);
            if (!empty($roleCategory)) {
                echo "<option value >Please select</option>";
                foreach ($roleCategory as $roleKey => $category) {
                    if (!empty($previousData) && $previousData['RoleCategoryID'] == $roleKey) {
                        echo "<option value='".$roleKey."' selected>".$category."</option>";
                    } else {
                        echo "<option value='".$roleKey."'>".$category."</option>";
                    }
                    
                }
            } else {
                    echo "<option value selected>Please select</option>";
            }
        } elseif (($institutionId && $roleCategoryId) && $type = 'role') {
            $roles = CustomRoleModel::getselectedRoles($roleCategoryId, $institutionId);
            if (!empty($roles)) {
                echo "<option value>Please select</option>";
                foreach ($roles as $Key => $role) {
                    if (!empty($previousData) && $previousData['roleid'] == $role['roleid']) {
                        echo "<option value='".$role['roleid']."' selected>".$role['roledescription']."</option>";
                    } else {
                        echo "<option value='".$role['roleid']."'>".$role['roledescription']."</option>";
                    }
                    
                }
            } else {
                    echo "<option value selected>Please select</option>";
            }
        }
    }

    public function actionDeactivate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
            $id = yii::$app->request->post('id');
            if ($id) {
                $id = (int)$id;
                $model = $this->findModel($id);
                if (!empty($model)) {
                    $model->isactive = 0;
                    $model->user->status = 0;
                    $model->user->updated_at = date('Y-m-d H:i:s');
                    $model->user->scenario = ExtendedUserCredentials::SCENARIO_DEACTIVE;
                    if ($model->update() &&  $model->user->update()) {
                            return [
                                'status' => 'success',
                                'data' => null,
                            ];
                    } else {
                        return [
                            'status' => 'error',
                            'data' => null
                        ];
                    }
                }
                 return [
                        'status' => 'error',
                        'data' => null,
                ];
            }
             return [
                        'status' => 'error',
                        'data' => null,
            ];
        }

    }
    public function actionActivate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (yii::$app->request->isAjax) {
            $id = yii::$app->request->post('id');
            if ($id) {
                $id = (int)$id;
                $model = $this->findModel($id);
                if (!empty($model)) {
                    $model->isactive = 1;
                    $model->user->status = 1;
                    $model->user->updated_at = date('Y-m-d H:i:s');
                    $model->user->scenario = ExtendedUserCredentials::SCENARIO_DEACTIVE;
                    if ($model->update() && $model->user->update()) {
                            return [
                                'status' => 'success',
                                'data' => null,
                            ];
                    } else {
                        return [
                            'status' => 'error',
                            'data' => null
                        ];
                    }
                }
                 return [
                        'status' => 'error',
                        'data' => null,
                ];
            }
             return [
                    'status' => 'error',
                    'data' => null,
            ];
        }

    }
    
    /**
     * @inheritdoc
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            $this->goBack();
        }
        $loginModel = new LoginForm();
        if ($loginModel->load(Yii::$app->request->post()) && $loginModel->login()) {
            if (yii::$app->user->can("superadmin")) { 
                return $this->goBack();     
            } elseif (yii::$app->checkAdminGroup->checkAdminGroupAccess($this->currentUserId()) && yii::$app->user->identity->institution->active == 1) {
                return $this->goBack(); 
            } else {
                Yii::$app->user->logout();
                $loginModel->password = '';
                $loginModel->addError('password', 'Invalid username or password !');
                return $this->render('login', ['loginModel' => $loginModel]);
            }
        } else {
            $loginModel->password = '';
            return $this->render('login', ['loginModel' => $loginModel]);
        }
    }
    
    /**
     * logout user
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect('login');
    }
     /**
    * Lists all ExtendedUserprofile models with 
    * same institution except the logged in user
    * @return mixed
    */
    public function actionHome()
    {
        $viewName = null;
        $viewData = ['header' => false];
       /*  yii\helpers\VarDumper::dump(Yii::$app->user?->identity, 10, true);

        exit; */
        if (Yii::$app->user->can('superadmin')) {
             $viewName = 'super_admin_home';
        } elseif (Yii::$app->checkAdminGroup->checkAdminGroupAccess($this->currentUserId())) {
            $viewName = 'admin_home';
        }
        if ($viewName) {
            return $this->homeRender($viewName, $viewData);
        } else {
            return $this->redirect('login');
        }
        
        
    }
    function homeRender($view, $data)
    {
        return $this->render($view, $data);
    }
    public function actionEditAdminProfile()
    {   
        $id = yii::$app->user->id;
        if ($id) {
            $userCredentialModel = $this->findUserCredentialModel($id);
            $userCredentialModel->scenario = $userCredentialModel::SCENARIO_ADMIN_UPDATE;
            $userProfileModel = $userCredentialModel->userprofile;
            $institutions = $this->getInstitutions();
             $oldPassword = $userCredentialModel->password;
            $userCredentialModel->password = "";
            if (Yii::$app->request->isPost) {
                if ($userCredentialModel->load(Yii::$app->request->post())
                    && $userProfileModel->load(Yii::$app->request->post())) {
                    $userCredentialModel->updated_at = date('Y-m-d H:i:s');
                    if ($userCredentialModel->validate()) {
                        if (!empty($userCredentialModel->password)) {
                            $userCredentialModel->setPassword($userCredentialModel->password);
                        } else {
                            $userCredentialModel->password = $oldPassword;
                        }
                        if ($userCredentialModel->update(false)) {
                            $userProfileModel->emailid = $userCredentialModel->emailid;
                            $userProfileModel->mobilenumber = $userCredentialModel->mobileno;
                            if ($userProfileModel->validate()) {
                                if($userProfileModel->update(false)) {
                                  $this->sessionAddFlashArray('success', 'Successfully updated admin', true);   
                                }elseif(empty($userProfileModel->getErrors())) {
                                    $this->sessionAddFlashArray('success', 'Successfully updated admin', true); 
                                }  
                            } else {
                                $this->sessionAddFlashArray('error', $userProfileModel->getErrors(), true);
                            }
                        } else {
                            $this->sessionAddFlashArray('error', $userCredentialModel->getErrors(), true);
                        }
                    } else {
                        $this->sessionAddFlashArray('error', $userCredentialModel->getErrors(), true);
                    }
                } else {
                    $this->sessionAddFlashArray('error', 'Unable to process you request', true);
                }
            return $this->redirect(['edit-admin-profile', 'id' => $userCredentialModel->id]);
            }
            return $this->render(
                '_update_admin',
                [
                    'userProfileModel' => $userProfileModel,
                    'userCredentialModel' => $userCredentialModel,
                    'institutions' => $institutions
                ]
            );
        }  
    }

    /**
     * Impersonate an existing User.
     * @param integer $id
     * @return mixed
     */
    public function actionImpersonate($id)
    {
        $user = ExtendedUserCredentials::findOne(['id' => $id, 'status' => 1]);
        $session = Yii::$app->session;
        if ($user) {
            //Yii::$app->user->logout();
            $data = ['id' => Yii::$app->user->id, 'email' => yii::$app->user->identity->emailid];
            $session->set('impersonation_user',$data);
            Yii::$app->user->login($user, 3600 * 24 * 30);
        }
        return $this->redirect('/');
    }

    public function actionExitImpersonate()
    {
        if ($impersonationuser = Yii::$app->session->get('impersonation_user')) {
            Yii::$app->session->remove('impersonation_user');
            $user = ExtendedUserCredentials::findOne($impersonationuser['id']);
            if ($user)
                Yii::$app->user->login($user, 3600 * 24 * 30);
        }
        return $this->redirect('/');
    }
    public function actionOldSystemSupport()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'message' => 'There is a new version of the App available.You are  required to update the App before proceeding further.',
                'statusCode' => 600,
                'data' => new \stdClass()
            ];
    }
}
