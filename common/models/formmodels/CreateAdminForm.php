<?php

namespace common\models\formmodels;

use yii;
use yii\base\Model;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedUserProfile;

class CreateAdminForm extends ExtendedUserCredentials
{

    public $first_name;
    public $last_name;
    public $middle_name;
    public $email_id;
    public $password;
    public $confirm_password;
    public $phone_number;
    public $institution_name;
    public $role_category;
    public $role;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
            'first_name',
            'last_name',
            'middle_name',
            'email_id',
            'role_category',
            'role',
            'phone_number'
            ], 'trim'],
            [[
            'email_id',
            'first_name',
            'password',
            'institution_name',
            'role_category',
            'role' ],
            'required'],
            [['institution_name'],'integer'],
            ['email_id', 'email'],
            ['email_id', 'string', 'max' => 255],
            ['password', 'string', 'min' => 4],
            [['phone_number'], 'string', 'max' => 20],
            [['phone_number'], 'match',
            'pattern' => '/^(?=.*[0-9])[- +()0-9]+$/'],
            [['confirm_password','last_name'], 'required'],
            ['confirm_password', 'compare', 'compareAttribute'=>'password', 'message'=>"Password don't match" ],
            ['email_id', 'unique', 
                'targetClass' => 'common\models\extendedmodels\ExtendedUserCredentials;', 
                'targetAttribute' => ['emailid'], 
                'message' => Yii::t('backend', 'This email address has already been taken.')],
            ['phone_number', 'unique', 
                'targetClass' => 'common\models\extendedmodels\ExtendedUserCredentials;', 
                'targetAttribute' => ['mobileno'], 
                'message' => Yii::t('backend', 'This number has already been taken.')],
        ];
    }

    public function createAdmin()
    {
        $userCredentialModel = new ExtendedUserCredentials();
        $userProfileModel = new ExtendedUserProfile();

        $userCredentialModel->password = $this->password;
        $userCredentialModel->emailid = $this->email_id;
        $userCredentialModel->mobileno = $this->phone_number;
        $userCredentialModel->institutionid = $this->institution_name;
        $userCredentialModel->setPassword($userCredentialModel->password);
        $userCredentialModel->generateAuthKey();
        $userCredentialModel->created_at = date('Y-m-d H:i:s');

        if ($userCredentialModel->save()) {
                $userProfileModel->firstname = $this->first_name;
                $userProfileModel->lastname = $this->last_name;
                $userProfileModel->middlename = $this->middle_name;
                $userProfileModel->userid = $userCredentialModel->id;
                $userProfileModel->emailid = $userCredentialModel->emailid;
                $userProfileModel->institutionid = $userCredentialModel->institutionid;
            if ($userProfileModel->save()) {
                $auth = Yii::$app->authManager;
                $role = $auth->getRole($this->role);
                if (!empty($role)) {
                    $auth->assign($role, $userCredentialModel->id);
                    return['success' => true];
                } else {
                    $userProfileModel->delete();
                    $userCredentialModel->delete();
                    return ['success' => false, 'errors' => 'Unable to fetch role details'];
                }
            } else {
                $userCredentialModel->delete();
                return ['success' => false, 'errors' => $userProfileModel->getErrors()];
            }

        } else {
                return ['success' => false, 'errors' => $userCredentialModel->getErrors()];
        }
    }
}
