<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Settings;
use common\models\extendedmodels\ExtendedAddresstype; 
use common\models\extendedmodels\ExtendedMember;
use Exception;
/**
 * This is the model class for table "settings".
 *
 * @property int $id
 * @property int $memberid
 * @property int $addresstypeid
 * @property int $membernotification
 * @property int $birthday
 * @property int $anniversary
 * @property int $memberemail
 * @property int $membersms
 * @property int $spouseemail
 * @property int $spousesms
 * @property int $spousenotification
 * @property int $spousebirthday
 * @property int $spouseanniversary
 * @property int $synccontactinterval
 * @property int $membermobilePrivacyEnabled
 * @property int $spousemobilePrivacyEnabled
 *
 * @property Addresstype $addresstype
 * @property Member $member
 */
class ExtendedSettings extends Settings
{   

    public $role_category;
    public $spouse_role_category;
    public $member_role;
    public $spouse_role;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['memberid', 'membernotification', 'birthday', 'anniversary', 'memberemail', 'membersms', 'spouseemail', 'spousesms', 'spousenotification', 'spousebirthday', 'spouseanniversary'], 'required'],
            [['memberid', 'addresstypeid', 'synccontactinterval'], 'integer'],
            [['membernotification', 'birthday', 'anniversary', 'memberemail', 'membersms', 'spouseemail', 'spousesms', 'spousenotification', 'spousebirthday', 'spouseanniversary', 'membermobilePrivacyEnabled', 'spousemobilePrivacyEnabled'], 'integer'],
            [['addresstypeid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedAddresstype::className(), 'targetAttribute' => ['addresstypeid' => 'id']],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedMember::className(), 'targetAttribute' => ['memberid' => 'memberid']],
            [['role_category', 'spouse_role_category'],'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'memberid' => 'Memberid',
            'addresstypeid' => 'Addresstypeid',
            'membernotification' => 'Membernotification',
            'birthday' => 'Birthday',
            'anniversary' => 'Anniversary',
            'memberemail' => 'Memberemail',
            'membersms' => 'Membersms',
            'spouseemail' => 'Spouseemail',
            'spousesms' => 'Spousesms',
            'spousenotification' => 'Spousenotification',
            'spousebirthday' => 'Spousebirthday',
            'spouseanniversary' => 'Spouseanniversary',
            'synccontactinterval' => 'Synccontactinterval',
            'membermobilePrivacyEnabled' => 'Membermobile Privacy Enabled',
            'spousemobilePrivacyEnabled' => 'Spousemobile Privacy Enabled',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddresstype()
    {
        return $this->hasOne(ExtendedAddresstype::className(), ['id' => 'addresstypeid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(ExtendedMember::className(), ['memberid' => 'memberid']);
    }
    /**
     * to get the details of
     * the settings that user has enabled
     */
    public static function getUserSettings($userId, $institutionId)
    {
	    try {
	    		$settingsData = Yii::$app->db->createCommand(
	    				"CALL getusersettings(:userid,:institutionid)")
	    				->bindValue(':userid', $userId)
	    				->bindValue(':institutionid' , $institutionId )
	    				->queryOne();
	    		return $settingsData;
	    		
	    	} catch (Exception $e) {
	    	return false;
	    }	
    }
    /**
     * to update the institution settings
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
    public static function updateUserSettings($id,$addressTypeId,$memberNotification,$birthday,$anniversary,
									    		$memberEmail,$memberSms,$spouseEmail,$spouseSms,$spouseNotification,$spouseBirthday,
									    		$spouseAnniversary,$syncContactInterval)
    {
    	try {
    		
    		$sql = "CALL updateusersettings(".(int)$id.",".(int)$addressTypeId.",".(int)$memberNotification.",
    				".(int)$birthday.",".(int)$anniversary.",".(int)$memberEmail.",".(int)$memberSms.",".(int)$spouseEmail.",
    				".(int)$spouseSms.",".(int)$spouseNotification.",".(int)$spouseBirthday.",".(int)$spouseAnniversary.",".(int)$syncContactInterval.")";
    		$updateData = Yii::$app->db->createCommand($sql)
    				->execute();
    				return $updateData;
    	
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * 
     * @param unknown $memberId
     * @param unknown $userType
     * @param unknown $mobilePrivacyEnabled
     */
    public static function updatePrivacy($memberId,$userType,$mobilePrivacyEnabled)
    {
    	try {
    		if($userType == "M") {
    			return  Yii::$app->db->createCommand("update settings set membermobilePrivacyEnabled=:membermobilePrivacyEnabled
    														WHERE settings.memberid=:memberid")
    							->bindValue(':membermobilePrivacyEnabled', (int)$mobilePrivacyEnabled)
    							->bindValue(':memberid', $memberId)
    							->execute();
    		} elseif ($userType == "S") {
    			return  Yii::$app->db->createCommand("update settings set spousemobilePrivacyEnabled=:membermobilePrivacyEnabled
    														WHERE settings.memberid=:memberid")
    			    			->bindValue(':membermobilePrivacyEnabled', (int)$mobilePrivacyEnabled)
    			    			->bindValue(':memberid', $memberId)
    			    			->execute();
    		}	
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To get the 
     * member settings
     * @param unknown $memberId
     * @return \yii\db\false|boolean
     */
    public static function getMemberSettings($memberId)
    {
    	try {
    		$memberSettings = Yii::$app->db->createCommand("
    				SELECT * FROM settings WHERE memberid = :memberid")
    				->bindValue(':memberid', $memberId)
    				->queryOne();
    		return $memberSettings;
    	} catch (Exception $e) {
            yii::error($e->getMessage());
    		return false;
    	}
    }
}

