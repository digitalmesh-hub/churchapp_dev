<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\UserCredentials;
use common\models\extendedmodels\ExtendedUserMember;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\extendedmodels\ExtendedUserToken;
use Exception;

/**
 * This is the extended model class for table "usercredentials".
 *
 * @property int $id
 * @property int $institutionid
 * @property string $emailid
 * @property string $password
 * @property int $initiallogin
 * @property string $usertype
 * @property string $lastlogin
 * @property string $mobileno
 * @property string $membershipno
 * @property string $userpin
 * @property int $otp
 * @property string $otpcreateddatetime
 *
 * @property Affiliatedinstitution[] $affiliatedinstitutions
 * @property Affiliatedinstitution[] $affiliatedinstitutions0
 * @property Album[] $albums
 * @property Album[] $albums0
 * @property Albumimage[] $albumimages
 * @property Albumimage[] $albumimages0
 * @property Attendance[] $attendances
 * @property Attendance[] $attendances0
 * @property Bills[] $bills
 * @property BirthdayAnniversarySeendetails[] $birthdayAnniversarySeendetails
 * @property Cart[] $carts
 * @property Cart[] $carts0
 * @property Committee[] $committees
 * @property Committee[] $committees0
 * @property Conversation[] $conversations
 * @property Conversationtopic[] $conversationtopics
 * @property Conversationtopic[] $conversationtopics0
 * @property DeleteUsermember[] $deleteUsermembers
 * @property Devicedetails[] $devicedetails
 * @property Events[] $events
 * @property Events[] $events0
 * @property Eventseendetails[] $eventseendetails
 * @property Eventsentdetails[] $eventsentdetails
 * @property Feedback[] $feedbacks
 * @property Feedbacknotification[] $feedbacknotifications
 * @property Feedbacknotificationsent[] $feedbacknotificationsents
 * @property Institution[] $institutions
 * @property Institution[] $institutions0
 * @property Notificationlog[] $notificationlogs
 * @property Notificationlog[] $notificationlogs0
 * @property Orderitems[] $orderitems
 * @property Orderitems[] $orderitems0
 * @property Orders[] $orders
 * @property Orders[] $orders0
 * @property PendingAlbumImage[] $pendingAlbumImages
 * @property Pendingimagenotification[] $pendingimagenotifications
 * @property Pendingimagenotification[] $pendingimagenotifications0
 * @property Pendingimagenotificationsent[] $pendingimagenotificationsents
 * @property Pendingimagenotificationsent[] $pendingimagenotificationsents0
 * @property Prayerrequest[] $prayerrequests
 * @property Prayerrequestnotification[] $prayerrequestnotifications
 * @property Prayerrequestnotificationsent[] $prayerrequestnotificationsents
 * @property Profileupdatenotification[] $profileupdatenotifications
 * @property Profileupdatenotificationsent[] $profileupdatenotificationsents
 * @property Propertycategory[] $propertycategories
 * @property Propertycategory[] $propertycategories0
 * @property Rsvpdetails[] $rsvpdetails
 * @property Rsvpnotification[] $rsvpnotifications
 * @property Rsvpnotificationsent[] $rsvpnotificationsents
 * @property Successfullalbumsent[] $successfullalbumsents
 * @property Successfulleventsent[] $successfulleventsents
 * @property Survey[] $surveys
 * @property Survey[] $surveys0
 * @property Surveycredentials[] $surveycredentials
 * @property Surveycredentials[] $surveycredentials0
 * @property Tax[] $taxes
 * @property Tax[] $taxes0
 * @property TempAlbumImage[] $tempAlbumImages
 * @property Tempmember[] $tempmembers
 * @property Tempmember[] $tempmembers0
 * @property Tempmembermail[] $tempmembermails
 * @property Tempmembermail[] $tempmembermails0
 * @property Testusermember[] $testusermembers
 * @property Userconversation[] $userconversations
 * @property Userconversationtopic[] $userconversationtopics
 * @property Institution $institution
 * @property Userlocation $userlocation
 * @property Usermember[] $usermembers
 * @property Userprofile[] $userprofiles
 * @property Userrole[] $userroles
 * @property Usertoken[] $usertokens
 */
class ExtendedUserCredentials extends UserCredentials
{

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_CREATE = 'create';
    const SCENARIO_DEACTIVE = 'deactivate';
    const SCENARIO_RESET_PASSCODE = 'resetpasscode';
    const ROLE_ADMIN = 'admin';
    const ROLE_MEMBER = 'member';
    const ROLE_STAFF = 'staff';
    const SCENARIO_ADMIN_UPDATE = 'updateadmin';
  
    public $role_category;
    public $role;
    public $confirm_password;
    public $editpassword;
    public $old_password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['institutionid'], 'integer'],
            [['auth_key', 'institutionid','emailid'], 'required'],
            [['lastlogin', 'otpcreateddatetime', 'created_at', 'updated_at'], 'safe'],
            [['emailid'], 'string', 'max' => 150],
            [['emailid'],'email'],
            // emailid and usertype need to be unique together, only a1 will receive error message
            ['emailid', 'unique', 'targetAttribute' => ['emailid', 'usertype'], 'message' => 'Email already exists'],
            [['password', 'password_reset_token'], 'string', 'max' => 255],
            [['initiallogin'], 'string', 'max' => 4],
            [['usertype'], 'string', 'max' => 1],
            [['mobileno'], 'string', 'max' => 20],
            ['mobileno', 'unique', 'targetAttribute' => ['mobileno', 'usertype'], 'message' => 'Phone number already exists'],
            [['mobileno'], 'match',
            'pattern' => '/^(?=.*[0-9])[- +()0-9]+$/'],
            [['membershipno'], 'string', 'max' => 100],
            [['userpin'], 'string', 'max' => 255],
            [['otp'],'string','max' => 10],
            [['auth_key'], 'string', 'max' => 32],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedInstitution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['emailid', 'mobileno'], 'trim'],
            [['confirm_password', 'password'], 'required', 'on' => self::SCENARIO_CREATE ],
            ['confirm_password', 'compare', 'compareAttribute'=>'password', 'message'=>"Password don't match" ],
            [[
            'role_category',
            'role' ],
            'required'],
            [['confirm_password'],'required','when' => function($model) {
                return $model->password;
            }, 'whenClient' => "function (attribute, value) {
                    return $('#extendedusercredentials-password').val() != '';
            }"],
            [['userpin','initiallogin'], 'required' , 'on' => self::SCENARIO_RESET_PASSCODE],
            ['editpassword','boolean'],
            [['password','confirm_password','old_password'],'required', 
            'when' => function($model) {
                return $model->editpassword == true;
            }, 'enableClientValidation' => false],
            ['old_password','findPasswords']
        ];
    }
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPDATE] = ['auth_key', 'emailid', 'institutionid', 'role_category','role','mobileno','password','confirm_password'];
        $scenarios[self::SCENARIO_CREATE] = ['password', 'confirm_password', 'auth_key','emailid','institutionid', 'role_category', 'role', 'mobileno'];
        $scenarios[self::SCENARIO_DEACTIVE] = ['status', 'updated_at'];
        $scenarios[self::SCENARIO_RESET_PASSCODE] = ['initiallogin', 'userpin'];
        $scenarios[self::SCENARIO_ADMIN_UPDATE] = ['emailid','password','old_password','confirm_password', 'institutionid','mobileno','editpassword'];
        return $scenarios;
    }
    //matching the old password with your existing password.
    public function findPasswords($attribute, $params)
    {   
        $user = self::findIdentity(Yii::$app->user->id);
        if (!Yii::$app->getSecurity()->validatePassword($this->old_password, $user->password)) {
            $this->addError($attribute, 'Old password is incorrect.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(ExtendedInstitution::className(), ['id' => 'institutionid']);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['emailid' => $username, 'status' => self::STATUS_ACTIVE, 'usertype' => 'A']);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserprofile()
    {
        return $this->hasOne(ExtendedUserProfile::className(), ['userid' => 'id']);
    }
    /**
     * check member credential exist
     * @param unknown $mobileNo
     * @param unknown $email
     * @return unknown
     */
    public function memberCredentialExist($mobileNo,$email)
    {

    	$sql = "SELECT usercredentials.id , usercredentials.institutionid from usercredentials
    			Where usercredentials.mobileno=:mobile";
    	
    	$result = Yii::$app->db->createCommand($sql)
    	   ->bindValue(':mobile' , $mobileNo )
    	   ->queryOne();
    	return $result;
    	
    }
      /**
     * to get user privileges
     */
   /* public static function getAllAppUserPrivileges($userId)
    {
    	try {
    		$appUserPrivileges = Yii::$app->db->createCommand(
    				"CALL getall_app_user_privileges(:userid)")
    				->bindValue(':userid' , $userId )
    				->queryAll();
    	return $appUserPrivileges;
    	} catch (Exception $e) {
    		return false;
    	}
    }*/
    /**
     * To get user institution
     */
    public static function getUserInstitution($userId)
    {
    	try {
    		$institution = Yii::$app->db->createCommand("select institutionid from usercredentials where id = :userid")
    						->bindValue(':userid', $userId)
    						->queryOne();
    		return $institution;
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To get user type
     */
    public static function getUserType($userId)
    {
    	try {
    		$userType = Yii::$app->db->createCommand("select usertype from usercredentials where id = :userid")->bindValue(':userid', $userId)->queryOne();
    		return $userType;	
    	} catch (Exception $e) {
    		return false;
    	}
    }
    public static function addUserToken($userId, $deviceKey)
    { 
        $token = ExtendedUserToken::find()
            ->where(['userid' => $userId])
            ->andWhere(['device_id' => $deviceKey])
            ->one();
        if (!$token) {
            $token = new ExtendedUserToken();
            $token->userid = $userId;
            $token->tokenid = base64_encode(self::getGUID());
            $token->device_id = $deviceKey;
            if ($token->save()) {
                return $token->tokenid;
            } else {
                yii::error($token->getErrors());
                return null;
            }
        } else {
            return $token->tokenid;
         }

    }
    /**
     * Function to get unique user_token_id
     * @return [type] [description]
     */
    public static function getGUID()
    {
        return Yii::$app->db->createCommand('SELECT uuid()')->queryScalar();
    }

    /**
     * Finds user by mobile no
     * Used for login purpose
     * @return array
     */
    public static function findByMobileNo($mobileNo)
    {
        try {
            $user = Yii::$app->db->createCommand(
                    "CALL getusercredentials(:mobileNo)")
                    ->bindValue(':mobileNo' , $mobileNo)
                    ->queryOne(); 
        } catch (\Exception $e) {
            yii::error($e->getMessage());
            return false;
        }
        return $user;
    }
    /**
     * Finds user by mobileno
     *
     * @param string $username
     * @return static|null
     */
    public static function findByMobileNumber($mobileNo)
    {
        return static::findOne(['mobileno' => $mobileNo, 'status' => self::STATUS_ACTIVE]);
    }
    /**
     * To unauthenticate user.
     * @param  [type] $user_token_id [description]
     * @return [type]                [description]
     */
    public static function unauthenticate($tokenid)
    {
        if ($userToken = ExtendedUserToken::findOne($tokenid)) {
            return $userToken->delete();
        }
    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->joinWith('userToken')
            ->where(['usertoken.tokenid'=> $token])
            ->andwhere(['usercredentials.status' => self::STATUS_ACTIVE ])
            ->one();
    }
    public function getUserToken()
    {
        //FIXME:Multiple devices - WIP
        return $this->hasOne(ExtendedUserToken::className(), ['userid' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeviceDetails()
    {
        return $this->hasOne(ExtendedDevicedetails::className(), ['userid' => 'id']);
    }

    /**
     * Get member details
     * @return \yii\db\ActiveQuery
     */
    public function getMemberDetails()
    {
        return $this->hasMany(ExtendedUserMember::className(), ['userid' => 'id']);
    }
    
    /**
     * To get  credential details and count
     * @param unknown $member
     * @return boolean
     */
    public static function getCredential($member){
    
    	try {
    		$credential = Yii::$app->db->createCommand(
    				"CALL get_user_credentials_id (:memberid,:institutionid,:member_email,:member_mobile1,:spouse_mobile1,:spouse_email)")
    				->bindValue( ":memberid", $member->memberid)
    				->bindValue( ":institutionid", $member->institutionid)
    				->bindValue( ":member_email", $member->member_email)
    				->bindValue( ":member_mobile1", $member->member_mobile1)
    				->bindValue( ":spouse_mobile1", $member->spouse_mobile1)
    				->bindValue( ":spouse_email", $member->spouse_email)
    				->queryOne();
    	} catch (\Exception $e) {
    		yii::error($e->getMessage());
    		return false;
    	}
    	return $credential;
    }
    /**
     * To delete user credentials
     * @param unknown $memberUserId
     * @return boolean
     */
    public static function deleteUserCredentials($memberUserId)
    {
    	try {
    		$deleteCredentials = Yii::$app->db->createCommand("delete from usercredentials where id = :memberuserid")
    							->bindValue(':memberuserid', $memberUserId)
    							->execute();
    		return true;
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To update user credentials
     * @param unknown $memberEmail
     * @param unknown $memberMobile1
     * @param unknown $memberUserId
     * @return boolean
     */
    public static function updateUserCredentials($memberEmail,$memberMobile1,$memberUserId)
    {
    	try {
    		$updateDetails = Yii::$app->db->createCommand("UPDATE usercredentials SET emailid=:memberemail,mobileno=:membermobile WHERE id=:memberuserid")
    						->bindValue(':memberemail', $memberEmail)
    						->bindValue(':membermobile', $memberMobile1)
    						->bindValue(':memberuserid', $memberUserId)
    						->execute();
    		return true;
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To add data to user credential
     * @param unknown $userCredentials
     * @return boolean
     */
    public static function addUserDetails($userCredentials)
    {
    	try {
    		$passwordHash = Yii::$app->getSecurity()->generatePasswordHash($userCredentials->password);
    		$addDetails = Yii::$app->db->createCommand("
    				CALL createusercredetials(:institutionid,:emailid,:password,:initiallogin,:usertype,:mobilenumber)")
    				->bindValue(':institutionid', $userCredentials->institutionid)
    				->bindValue(':emailid', $userCredentials->emailid)
    				->bindValue(':password', $passwordHash)
    				->bindValue(':initiallogin', $userCredentials->initiallogin)
    				->bindValue(':usertype', $userCredentials->usertype)
    				->bindValue(':mobilenumber', $userCredentials->mobileno)
    				->execute();
    		return true;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To get user current institution
     * @param unknown $memberUserId
     * @return \yii\db\false|boolean
     */
    public static function getUserCurrentInstitution($memberUserId)
    {
    	try {
    		$currInstitution = Yii::$app->db->createCommand("SELECT institutionid FROM usercredentials where id=:userid")
    			->bindValue(':userid', $memberUserId)
    			->queryOne();
    		return $currInstitution;
    	} catch (Exception $e) {
    		return false;
    	}
    }
   /**
    * To update user institutionid
    * @param unknown $institutionId
    * @param unknown $memberUserId
    * @return boolean
    */
    public static function updateInstitutionId($institutionId,$memberUserId)
    {
    	try {
    		return  Yii::$app->db->createCommand("UPDATE usercredentials set institutionid=:institutionid where id=:memberuserid")
    							->bindValue(':institutionid', $institutionId)
    							->bindValue(':memberuserid', $memberUserId)
    							->execute();
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * 
     */
    public static function getUserIdByMemberMobile($memberMobileNo)
    {
    	try {
    		$sql = "select id  from usercredentials where mobileno = :p_member_mobile1 limit 1";
    		$userId = Yii::$app->db->createCommand($sql)
    				->bindValue(':p_member_mobile1', $memberMobileNo)
    				->queryOne();
    		return $userId;
    	} catch (Exception $e) {
    		return false;
    	}
    }
    public function getUserMember()
    {
       $sql = "SELECT id from usermember WHERE userid =:userid and usertype =:usertype and institutionid =:institutionid";
       try {
         return yii::$app->db->createCommand($sql)->bindValue(':userid', $this->id)
                ->bindValue(':usertype', $this->usertype)->bindValue(':institutionid', $this->institutionid)->queryScalar();
       } catch (Exception $e) {
         yii::error($e->getMessage());
       }
       return false;
    }
    public function getUserEmail($institutionId){
        try {
            $emailList = Yii::$app->db->createCommand(
                    "CALL get_admin_email(:institutionid)")
                    ->bindValue( ":institutionid", $institutionId)
                    ->queryAll();
            if(!empty($emailList)){
                return $emailList;
            }
            else{
                return false;
            }
        } catch (\Exception $e) {
            yii::error($e->getMessage());
            return false;
        }
    }
}
