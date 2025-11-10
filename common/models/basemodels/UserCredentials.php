<?php

namespace common\models\basemodels;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "usercredentials".
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
 * @property string $auth_key
 * @property string $password_reset_token
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
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
class UserCredentials extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'usercredentials';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['institutionid', 'otp'], 'integer'],
            [['password', 'auth_key'], 'required'],
            [['lastlogin', 'otpcreateddatetime', 'created_at', 'updated_at'], 'safe'],
            [['emailid'], 'string', 'max' => 150],
            [['password', 'password_reset_token'], 'string', 'max' => 255],
            [['initiallogin'], 'string', 'max' => 4],
            [['usertype'], 'string', 'max' => 1],
            [['mobileno'], 'string', 'max' => 45],
            [['membershipno'], 'string', 'max' => 100],
            [['userpin'], 'string', 'max' => 10],
            [['auth_key'], 'string', 'max' => 32],
            [['status'], 'string', 'max' => 2],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'institutionid' => 'Institution Name',
            'emailid' => 'Email Id',
            'password' => 'Password',
            'initiallogin' => 'Initiallogin',
            'usertype' => 'Usertype',
            'lastlogin' => 'Lastlogin',
            'mobileno' => 'Phone Number',
            'membershipno' => 'Membershipno',
            'userpin' => 'Userpin',
            'otp' => 'Otp',
            'otpcreateddatetime' => 'Otpcreateddatetime',
            'auth_key' => 'Auth Key',
            'password_reset_token' => 'Password Reset Token',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAffiliatedinstitutions()
    {
        return $this->hasMany(Affiliatedinstitution::className(), ['createduser' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAffiliatedinstitutions0()
    {
        return $this->hasMany(Affiliatedinstitution::className(), ['modifieduser' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlbums()
    {
        return $this->hasMany(Album::className(), ['createdby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlbums0()
    {
        return $this->hasMany(Album::className(), ['modifiedby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlbumimages()
    {
        return $this->hasMany(Albumimage::className(), ['createdby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlbumimages0()
    {
        return $this->hasMany(Albumimage::className(), ['modifiedby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttendances()
    {
        return $this->hasMany(Attendance::className(), ['createdby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttendances0()
    {
        return $this->hasMany(Attendance::className(), ['modifiedby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBills()
    {
        return $this->hasMany(Bills::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBirthdayAnniversarySeendetails()
    {
        return $this->hasMany(BirthdayAnniversarySeendetails::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarts()
    {
        return $this->hasMany(Cart::className(), ['createdby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarts0()
    {
        return $this->hasMany(Cart::className(), ['modifiedby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommittees()
    {
        return $this->hasMany(Committee::className(), ['createdby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommittees0()
    {
        return $this->hasMany(Committee::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConversations()
    {
        return $this->hasMany(Conversation::className(), ['createdby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConversationtopics()
    {
        return $this->hasMany(Conversationtopic::className(), ['createdby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConversationtopics0()
    {
        return $this->hasMany(Conversationtopic::className(), ['modifiedby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeleteUsermembers()
    {
        return $this->hasMany(DeleteUsermember::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevicedetails()
    {
        return $this->hasMany(Devicedetails::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(Events::className(), ['createduser' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents0()
    {
        return $this->hasMany(Events::className(), ['modifiedby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventseendetails()
    {
        return $this->hasMany(Eventseendetails::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventsentdetails()
    {
        return $this->hasMany(Eventsentdetails::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbacks()
    {
        return $this->hasMany(Feedback::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbacknotifications()
    {
        return $this->hasMany(Feedbacknotification::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbacknotificationsents()
    {
        return $this->hasMany(Feedbacknotificationsent::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutions()
    {
        return $this->hasMany(Institution::className(), ['createduser' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutions0()
    {
        return $this->hasMany(Institution::className(), ['modifieduser' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationlogs()
    {
        return $this->hasMany(Notificationlog::className(), ['CreatedBy' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationlogs0()
    {
        return $this->hasMany(Notificationlog::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderitems()
    {
        return $this->hasMany(Orderitems::className(), ['createdby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderitems0()
    {
        return $this->hasMany(Orderitems::className(), ['modifiedby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['createdby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders0()
    {
        return $this->hasMany(Orders::className(), ['modifiedby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendingAlbumImages()
    {
        return $this->hasMany(PendingAlbumImage::className(), ['createdby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendingimagenotifications()
    {
        return $this->hasMany(Pendingimagenotification::className(), ['uploadedby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendingimagenotifications0()
    {
        return $this->hasMany(Pendingimagenotification::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendingimagenotificationsents()
    {
        return $this->hasMany(Pendingimagenotificationsent::className(), ['uploadedby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendingimagenotificationsents0()
    {
        return $this->hasMany(Pendingimagenotificationsent::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerrequests()
    {
        return $this->hasMany(Prayerrequest::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerrequestnotifications()
    {
        return $this->hasMany(Prayerrequestnotification::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerrequestnotificationsents()
    {
        return $this->hasMany(Prayerrequestnotificationsent::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfileupdatenotifications()
    {
        return $this->hasMany(Profileupdatenotification::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfileupdatenotificationsents()
    {
        return $this->hasMany(Profileupdatenotificationsent::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertycategories()
    {
        return $this->hasMany(Propertycategory::className(), ['createdby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertycategories0()
    {
        return $this->hasMany(Propertycategory::className(), ['modifiedby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRsvpdetails()
    {
        return $this->hasMany(Rsvpdetails::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRsvpnotifications()
    {
        return $this->hasMany(Rsvpnotification::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRsvpnotificationsents()
    {
        return $this->hasMany(Rsvpnotificationsent::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuccessfullalbumsents()
    {
        return $this->hasMany(Successfullalbumsent::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuccessfulleventsents()
    {
        return $this->hasMany(Successfulleventsent::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveys()
    {
        return $this->hasMany(Survey::className(), ['createdby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveys0()
    {
        return $this->hasMany(Survey::className(), ['modifiedby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveycredentials()
    {
        return $this->hasMany(Surveycredentials::className(), ['modifiedby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveycredentials0()
    {
        return $this->hasMany(Surveycredentials::className(), ['createdby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxes()
    {
        return $this->hasMany(Tax::className(), ['createdby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxes0()
    {
        return $this->hasMany(Tax::className(), ['modifiedby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempAlbumImages()
    {
        return $this->hasMany(TempAlbumImage::className(), ['createdby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmembers()
    {
        return $this->hasMany(Tempmember::className(), ['temp_createdby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmembers0()
    {
        return $this->hasMany(Tempmember::className(), ['temp_modifiedby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmembermails()
    {
        return $this->hasMany(Tempmembermail::className(), ['temp_createdby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmembermails0()
    {
        return $this->hasMany(Tempmembermail::className(), ['temp_modifiedby' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestusermembers()
    {
        return $this->hasMany(Testusermember::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserconversations()
    {
        return $this->hasMany(Userconversation::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserconversationtopics()
    {
        return $this->hasMany(Userconversationtopic::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserlocation()
    {
        return $this->hasOne(Userlocation::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsermembers()
    {
        return $this->hasMany(Usermember::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserprofiles()
    {
        return $this->hasMany(Userprofile::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserroles()
    {
        return $this->hasMany(Userrole::className(), ['userid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsertokens()
    {
        return $this->hasMany(Usertoken::className(), ['userid' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
       
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
       
    }
}
