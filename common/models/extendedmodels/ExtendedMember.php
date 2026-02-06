<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Member;
use common\models\extendedmodels\ExtendedCountry;
use common\models\extendedmodels\ExtendedFamilyunit;
use common\models\extendedmodels\ExtendedZone;
use common\models\extendedmodels\ExtendedTitle;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\extendedmodels\ExtendedDevicedetails;
use common\models\extendedmodels\ExtendedPrivilege;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use Exception;

/**
 * This is the model class for table "member".
 *
 * @property int $memberid
 * @property int $institutionid
 * @property string $memberno
 * @property string $membershiptype
 * @property string $membersince
 * @property string $firstName
 * @property string $middleName
 * @property string $lastName
 * @property string $business_address1
 * @property string $business_address2
 * @property string $business_address3
 * @property string $business_district
 * @property string $business_state
 * @property string $business_pincode
 * @property string $member_dob
 * @property string $member_mobile1
 * @property string $member_mobile2
 * @property string $member_musiness_Phone1
 * @property string $member_business_Phone2
 * @property string $member_residence_Phone1
 * @property string $member_residence_Phone2
 * @property string $member_email
 * @property string $spouse_firstName
 * @property string $spouse_middleName
 * @property string $spouse_lastName
 * @property string $spouse_dob
 * @property string $dom
 * @property string $spouse_mobile1
 * @property string $spouse_mobile2
 * @property string $spouse_email
 * @property string $residence_address1
 * @property string $residence_address2
 * @property string $residence_address3
 * @property string $residence_district
 * @property string $residence_state
 * @property string $residence_pincode
 * @property string $member_pic
 * @property string $spouse_pic
 * @property string $app_reg_member
 * @property string $app_reg_spouse
 * @property int $active
 * @property string $businessemail
 * @property int $membertitle
 * @property int $spousetitle
 * @property string $membernickname
 * @property string $spousenickname
 * @property string $lastupdated
 * @property string $createddate
 * @property string $homechurch
 * @property string $occupation
 * @property string $spouseoccupation
 * @property int $countrycode
 * @property int $areacode
 * @property int $member_mobile1_countrycode
 * @property int $spouse_mobile1_countrycode
 * @property string $member_business_phone1_countrycode
 * @property string $member_business_phone1_areacode
 * @property string $member_business_phone2_countrycode
 * @property string $memberImageThumbnail
 * @property string $spouseImageThumbnail
 * @property int $membertype
 * @property int $staffdesignation
 * @property string $member_business_Phone3
 * @property string $member_business_phone3_countrycode
 * @property string $member_business_phone3_areacode
 * @property string $newmembernum
 * @property int $familyunitid
 * @property string $memberbloodgroup
 * @property string $spousebloodgroup
 * @property string $member_residence_phone1_areacode
 * @property string $member_residence_Phone1_countrycode
 * @property string $member_residence_phone2_areacode
 * @property string $member_residence_Phone2_countrycode
 * @property string $companyname
 * @property string $member_business_phone2_areacode
 *
 * @property Attendance[] $attendances
 * @property Bills[] $bills
 * @property Billseendetails[] $billseendetails
 * @property Cart[] $carts
 * @property Committee[] $committees
 * @property DeleteDependant[] $deleteDependants
 * @property DeleteUsermember[] $deleteUsermembers
 * @property Dependant[] $dependants
 * @property Editmember[] $editmembers
 * @property Country $countrycode0
 * @property Familyunit $familyunit
 * @property Country $memberMobile1Countrycode
 * @property Title $membertitle0
 * @property Country $spouseMobile1Countrycode
 * @property Title $spousetitle0
 * @property Institution $institution
 * @property Memberadditionalinfo[] $memberadditionalinfos
 * @property Memberrole[] $memberroles
 * @property Notificationlog[] $notificationlogs
 * @property Ordernotifications[] $ordernotifications
 * @property Ordernotificationsent[] $ordernotificationsents
 * @property Orders[] $orders
 * @property PaymentTransactions[] $paymentTransactions
 * @property Profileupdatenotification[] $profileupdatenotifications
 * @property Profileupdatenotificationsent[] $profileupdatenotificationsents
 * @property Rsvpdetails[] $rsvpdetails
 * @property Settings[] $settings
 * @property Surveystatus[] $surveystatuses
 * @property Tempmember[] $tempmembers
 * @property Tempmemberadditionalinfo[] $tempmemberadditionalinfos
 * @property Tempmemberadditionalinfomail[] $tempmemberadditionalinfomails
 * @property Tempmembermail[] $tempmembermails
 * @property Testusermember[] $testusermembers
 * @property Usermember[] $usermembers
 */
class ExtendedMember extends Member
{
	const USER_TYPE_MEMBER = "M";
    const USER_TYPE_SPOUSE = "S";
    const SCENARIO_EDUCATION = 'education';

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createddate',
                'updatedAtAttribute' => 'lastupdated',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['institutionid', 'memberno', 'membershiptype', 'firstName', 'lastName'], 'required'],
            [['institutionid', 'membertitle', 'spousetitle', 'countrycode', 'areacode', 'member_mobile1_countrycode', 'spouse_mobile1_countrycode', 'membertype', 'staffdesignation', 'familyunitid', 'zone_id'], 'integer'],
            [['membersince', 'member_dob', 'spouse_dob', 'dom', 'app_reg_member', 'app_reg_spouse', 'lastupdated', 'createddate'], 'safe'],
            [['memberno'], 'string', 'max' => 75],
            [['memberno'], 'match', 'pattern' => '/^(RM-|FM-)\d+$/', 'message' => 'Membership number must start with either RM- or FM- followed by numbers (e.g., RM-1001 or FM-1001)'],
            [['batch'],'string', 'max' => 5],
            [['membershiptype', 'membernickname', 'spousenickname'], 'string', 'max' => 25],
            [['firstName', 'middleName', 'lastName', 'business_district', 'business_state', 'business_pincode', 'spouse_firstName', 'spouse_middleName', 'spouse_lastName', 'residence_address3', 'residence_district', 'residence_state', 'newmembernum'], 'string', 'max' => 45],
            [['business_address1', 'business_address2', 'residence_address1', 'residence_address2', 'homechurch', 'occupation', 'companyname'], 'string', 'max' => 100],
            [['directory_number'], 'string', 'max' => 50],
            [['business_address3'], 'string', 'max' => 50],
            [['member_mobile1', 'member_mobile2', 'member_musiness_Phone1', 'member_business_Phone2', 'member_residence_Phone1', 'member_residence_Phone2', 'spouse_mobile1', 'spouse_mobile2', 'member_business_Phone3'], 'string', 'max' => 13],
            [['member_email', 'spouse_email', 'businessemail'], 'string', 'max' => 150],
            [['residence_pincode', 'memberbloodgroup', 'spousebloodgroup'], 'string', 'max' => 15],
         //   [['member_pic', 'spouse_pic', 'memberImageThumbnail', 'spouseImageThumbnail'], 'string', 'max' => 200],
        		[['member_pic', 'spouse_pic', 'memberImageThumbnail', 'spouseImageThumbnail'], 'file', 'skipOnEmpty' => true,
        		'extensions' => 'png, jpg', 'skipOnEmpty' =>true,
        		'maxSize' => \Yii::$app->params['fileUploadSize']['imageFileSize'],
        		'tooBig' => \Yii::$app->params['fileUploadSize']['imageSizeMsg']],
            [['active', 'member_business_phone1_countrycode', 'member_business_phone2_countrycode', 'member_business_phone3_countrycode', 'member_residence_Phone1_countrycode', 'member_residence_Phone2_countrycode'], 'string', 'max' => 4],
            [['spouseoccupation'], 'string', 'max' => 250],
            [['member_business_phone1_areacode', 'member_business_phone3_areacode', 'member_residence_phone1_areacode', 'member_residence_phone2_areacode', 'member_business_phone2_areacode'], 'string', 'max' => 5],
            [['countrycode'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedCountry::className(), 'targetAttribute' => ['countrycode' => 'countryid']],
            [['familyunitid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedFamilyunit::className(), 'targetAttribute' => ['familyunitid' => 'familyunitid']],
            [['zone_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedZone::className(), 'targetAttribute' => ['zone_id' => 'zoneid']],
         //   [['member_mobile1_countrycode'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedCountry::className(), 'targetAttribute' => ['member_mobile1_countrycode' => 'countryid']],
            [['membertitle'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedTitle::className(), 'targetAttribute' => ['membertitle' => 'TitleId']],
           // [['spouse_mobile1_countrycode'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedCountry::className(), 'targetAttribute' => ['spouse_mobile1_countrycode' => 'countryid']],
            [['spousetitle'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedTitle::className(), 'targetAttribute' => ['spousetitle' => 'TitleId']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedInstitution::className(), 'targetAttribute' => ['institutionid' => 'id']],
        ];
    }
    
    /**
     * Validate membership number format before saving
     * This runs even when save(false) is called
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        
        // Validate membership number format if it's not empty
        if (!empty($this->memberno)) {
            if (!preg_match('/^(RM-|FM-)\d+$/', $this->memberno)) {
                $this->addError('memberno', 'Membership number must start with either RM- or FM- followed by numbers (e.g., RM-1001 or FM-1001)');
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * To check the mobile already exist 
     * @param unknown $institusionId
     * @param unknown $mobileNo
     * @return unknown
     */
    public function isMobleNumberExists($institusionId,$mobileNo){
    	
    	$sql = "SELECT memberid FROM member WHERE (member.member_mobile1=:mobileNo or 
                                                    member.spouse_mobile1=:mobileNo) and member.institutionid=:institutionId";
    	 
    	$result = Yii::$app->db->createCommand($sql)
    	->bindValue(':institutionId' , $institusionId )
    	->bindValue(':mobileNo' , $mobileNo )
    	->queryOne();
    	return $result;
    }
    /**
     * To check email already exit
     * @param unknown $institusionId
     * @param unknown $email
     * @return unknown
     */
    public function isEmailExists($institusionId,$email){
    	 
    	$sql = "SELECT memberid FROM member WHERE (member.member_email=:email or member.spouse_email=:email) 
                                                and member.institutionid=:institutionId";
    
    	$result = Yii::$app->db->createCommand($sql)
    	->bindValue(':institutionId' , $institusionId )
    	->bindValue(':email' , $email )
    	->queryOne();
    	return $result;
    }
    
    /**
     * To check membership alredy exists
     * @param unknown $institusionId
     * @param unknown $membership
     * @return unknown
     */
    public function isMemberShipExists($institusionId,$membership){
    
    	$sql = "SELECT memberid FROM member WHERE memberno = :membership and member.institutionid=:institutionId";
    
    	$result = Yii::$app->db->createCommand($sql)
    	->bindValue(':institutionId' , $institusionId )
    	->bindValue(':membership' , $membership )
    	->queryOne();
    	return $result;
    }
    /**
     * To get the member
     * birthday list
     */
    public static function getMemberBirthday()
    {
    	try {
    		$memberBirthday = Yii::$app->db->createCommand('select m.member_dob,m.institutionid from `member` m
								INNER JOIN usermember um on m.memberid = um.memberid 
								GROUP BY um.userid')
								->queryAll();
    		return $memberBirthday;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To check the mobile number is alredy exist
     * @param unknown $institusionId
     * @param unknown $mobileNo
     * @param unknown $memberId
     * @return unknown
     */
    public function isMobleNumberAlreadyExists($institutionId,$mobileNo,$memberId){
    	 
    	$sql = "SELECT memberid FROM member WHERE (member.member_mobile1=:mobileNo or
                                                    member.spouse_mobile1=:mobileNo) and member.institutionid=:institutionId and memberid != :memberId";
    
    	$result = Yii::$app->db->createCommand($sql)
    	->bindValue(':institutionId' , $institutionId )
    	->bindValue(':mobileNo' , $mobileNo )
    	->bindValue(':memberId' , $memberId )
    	->queryOne();
    	return $result;
    }
    /**
     * To get the member name 
     * @param $memberId int
     */
    public static function getMemberName($memberId)
    {
    	try {
    		$memberName = Yii::$app->db->createCommand('select m.member_email,m.firstName,m.middleName,m.lastName from `member` m 
    						where m.memberid=:memberid')
    						->bindValue(':memberid' , $memberId )
							->queryOne();
    		return $memberName;
    	} catch (Exception $e) {
    		return false;
    	}
    }

    /**
     * getInstitutionProperties
     * @param string $memberName
     * @param string $spouseBit
     * @param integer $institutionId
     * @param integer $memberId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function getCommitteeMemberDetails(
                    $memberName, $spouseBit, $institutionId, $memberId)
    {
        $memberNames = Yii::$app->db->createCommand("CALL get_member_details(:memberno,:isspouse,:institutionId,:memberId)")
        ->bindValue(':memberno', $memberName)
        ->bindValue(':isspouse', $spouseBit)
        ->bindValue(':institutionId', $institutionId)
        ->bindValue(':memberId', $memberId)
        ->queryAll();
        return $memberNames;
    }
     /**
     * getInstitutionProperties
     * @param string $memberName
     * @param string $spouseBit
     * @param integer $institutionId
     * @param integer $memberId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function getCommitteeMemberDetailsForAutoComplete(
                    $memberName, $spouseBit, $institutionId, $memberId)
    {
        $memberNames = Yii::$app->db->createCommand("CALL get_member_details_for_auto(:memberno,:isspouse,:institutionId,:memberId)")
        ->bindValue(':memberno', $memberName)
        ->bindValue(':isspouse', $spouseBit)
        ->bindValue(':institutionId', $institutionId)
        ->bindValue(':memberId', $memberId)
        ->queryAll();
        return $memberNames;
    }
    
   /**
    * To delete the spouse details
    * @param unknown $memberId
    * @return number|boolean
    */
    
    public function deleteSpouseDetails($memberId){
    	 
    	try {
            if(!$this->backupSpouseDetails($memberId)){
                return false;
            }
    		$getAllData= Yii::$app->db->createCommand(
    				"CALL delete_spouse(:memberId,:currentdate)")
    				->bindValue(':memberId', $memberId)
    				->bindValue(':currentdate', gmdate('Y-m-d H:i:s') )
    				->execute();
    		return $getAllData;	 
    	} catch (Exception $e) {
    		return false;
    	}
    	 
    }
    /***
     * 
     * Backup removed spouse details
     */
    protected function backupSpouseDetails($memberId){
        try {
            $sql = "SELECT 
                spouse_firstname,spouse_middleName,spouse_lastName,spouse_dob,spouse_mobile1_countrycode,spouse_mobile1,spouse_mobile2,spouse_email,spouse_pic,spousetitle,spousenickname,spouseoccupation,spousebloodgroup,dom
            FROM member 
            WHERE memberid=:memberid";
            $spouseData= Yii::$app->db->createCommand($sql)
            ->bindValue(':memberid', $memberId)
            ->queryOne();
            $spouseData = json_encode($spouseData);
            $sql = "SELECT usercredentials.*
            FROM usermember
            INNER JOIN usercredentials on usercredentials.id=usermember.userid
            WHERE usermember.memberid=:memberid and usermember.usertype='S'";
            $usercredentials= Yii::$app->db->createCommand($sql)
            ->bindValue(':memberid', $memberId)
            ->queryOne();
            if(empty($usercredentials)){
                $usercredentials = NULL;
            }else{
                $usercredentials = json_encode($usercredentials);
            }
            $sql = "INSERT INTO removed_member_details
            (memberid,
            usertype,
            spouse_data,
            usercredentials)
            VALUES (:memberid,'S',:spouseData,:usercredentials)";
            $getAllData= Yii::$app->db->createCommand($sql)
            ->bindValue(':memberid', $memberId)
            ->bindValue(':spouseData', $spouseData)
            ->bindValue(':usercredentials', $usercredentials)
            ->execute();
            return $getAllData;
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * to remove primary member
     * @param unknown $memberDetails
     */
    public function removePrimaryMemberDetails($memberDetails){
    	try {
            if(!$this->backupMemberDetails($memberDetails['MemberID'])){
                return false;
            }
    		$getAllData= Yii::$app->db->createCommand(
    				"CALL delete_primarymember(:memberId,:memberTitle,:memberFirstName,:memberMiddleName,:lastName,:dob,:mobile1,:email,:occupation,:nickName,:phcode,:memberpic,:bloodGrp,:currentdate)")
    				->bindValue(':memberId', $this->getDetails($memberDetails['MemberID']))
    				->bindValue(':memberTitle', $this->getDetails($memberDetails['MemberTitle']))
    				->bindValue(':memberFirstName', $this->getDetails($memberDetails['FirstName']))
    				->bindValue(':memberMiddleName', $this->getDetails($memberDetails['MiddleName']))
    				->bindValue(':lastName', $this->getDetails($memberDetails['LastName']))
    				->bindValue(':dob', $this->sqlDateConversion($memberDetails['varmemberDOB']))
    				->bindValue(':mobile1', $this->getDetails($memberDetails['MemberMobile1']))
    				->bindValue(':memberpic', $this->getCorrectPic($memberDetails['memberpic']))
    				->bindValue(':nickName', $this->getDetails($memberDetails['MemberNickName']))
    				->bindValue(':email', $this->getDetails($memberDetails['MemberEmail']))
    				->bindValue(':occupation', $this->getDetails($memberDetails['Occupation']))
    				->bindValue(':phcode', $this->getDetails($memberDetails['Member_Mobile1_Countrycode']))
    				->bindValue(':bloodGrp', $this->getDetails($memberDetails['MemberBloodGroup']))
    				->bindValue(':currentdate', gmdate('Y-m-d H:i:s') )
    				->execute();
    		return $getAllData;
    		 
    	} catch (Exception $e) {
            yii::error($e->getMessage());
    		return false;
    	}
    }
    /***
     * 
     * Backup removed spouse details
     */
    protected function backupMemberDetails($memberId){
        try {
            $sql = "SELECT * FROM member WHERE memberid=:memberid";
            $memberData= Yii::$app->db->createCommand($sql)
            ->bindValue(':memberid', $memberId)
            ->queryOne();
            $memberData= json_encode($memberData);
            $sql = "SELECT usercredentials.*
            FROM usermember
            INNER JOIN usercredentials on usercredentials.id=usermember.userid
            WHERE usermember.memberid=:memberid and usermember.usertype='M'";
            $usercredentials= Yii::$app->db->createCommand($sql)
            ->bindValue(':memberid', $memberId)
            ->queryOne();
            if(empty($usercredentials)){
                $usercredentials = NULL;
            }else{
                $usercredentials = json_encode($usercredentials);
            }
            $sql = "INSERT INTO removed_member_details
            (memberid,
            usertype,
            member_data,
            usercredentials)
            VALUES (:memberid,'M',:memberData,:usercredentials)";
            $getAllData= Yii::$app->db->createCommand($sql)
            ->bindValue(':memberid', $memberId)
            ->bindValue(':memberData', $memberData)
            ->bindValue(':usercredentials', $usercredentials)
            ->execute();
            return $getAllData;
    	} catch (Exception $e) {
    		return false;
    	}
    }
    
    
    protected function getDetails($data){
    	 
    	return $data?trim($data):'';
    }
    
    /*
     * to convert the date into sql format
     */
    protected function sqlDateConversion($date){
    
    	return  !empty($date)?date(yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($date)):null;
    
    }
    
    protected function getCorrectPic($data){
    	if (!empty($data)){
    
    		$pic = explode(Yii::$app->params['imagePath'], $data);
    		if (isset($pic[1])){
    			return $pic[1];
    		}else {
    			return null;
    		}
    		 
    	}return null;
    }
    
    /**
     * To delete a member
     * @param unknown $memberId
     * @param unknown $institutionId
     * @return boolean|number
     */
    public function deleteMember($memberId,$institutionId)
    {
    	 
    	try {
    		$getAllData = Yii::$app->db->createCommand(
    				"CALL delete_member_v1(:memberId,:currentdate,:institutionid)")
    				->bindValue(':memberId', $memberId)
    				->bindValue(':currentdate', date('Y-m-d H:i:s'))
                    ->bindValue(':institutionid', $institutionId)
    				->execute();
    	} catch (Exception $e) {
            yii::error($e->getMessage());
    		return false;
    	}
    	return $getAllData;
    }
  /**
   * To get the details 
   * of all staffs in an institution
   * @param unknown $institutionId
   * @return boolean
   */
    public static function getStaffs($institutionId)
    {
    	try {
    		$staffDetails = Yii::$app->db->createCommand("
    				CALL getallstaff(:institutionid)")
    				->bindValue(':institutionid', $institutionId)
    				->queryAll();
    		return $staffDetails;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To get the institution
     * member id
     * @param unknown $memberId
     * @param unknown $institutionId
     * @param unknown $userType
     * @return \yii\db\false|boolean
     */
   /* public static function getInstitutionMemberId($memberId,$institutionId,$userType)
    {
    	try {
    		$institutionMemberId = Yii::$app->db->createCommand('select memberid from usermember where 
    				memberid=:memberid and usertype=:usertype and institutionid=:institutionid')
		    		->bindValue(':memberid', $memberId)
		    		->bindValue(':usertype', $userType)
		    		->bindValue('institutionid', $institutionId)
		    		->queryOne();
    		return $institutionMemberId;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }*/
    public static function getInstitutionMemberId($memberID,$institutionId)
    {  
        
        $institutionMemberId = 0;
        try {
            $userId = self::getUserIdFromMemberID($memberID,$institutionId,self::USER_TYPE_MEMBER);
                if ($userId) {
                    $institutionMemberId = self::getMemberId($userId,$institutionId,self::USER_TYPE_MEMBER);
                    $institutionMemberId = ($institutionMemberId) ? $institutionMemberId['memberid'] : $memberID;
                } else {
                    $institutionMemberId = $memberID;
                }
        }catch (Exception $ex){
            yii::error($ex->getMessage());
        }
        return $institutionMemberId;
    }

   /**
    *  To get the
    * temp member details by
    * using member id
    * @param unknown $memberId
    * @return \yii\db\false|boolean
    */
    public static function getTempMemberBymemberid($memberId)
    {
    	try {
    		$tempMemberDetails = Yii::$app->db->createCommand("
    				CALL gettempmemberbymemberid(:memberid)")
    				->bindValue(':memberid', $memberId)
    				->queryOne();
    	    return $tempMemberDetails;
    	} catch (Exception $e) {
    		return false;
    	}
    }
   /**
    * To get member contact
    * details by type
    * @param unknown $memberId
    * @return \yii\db\false|boolean
    */
    public static function getContactDetailsByType($memberId)
    {
    	try {
    		$contactDetails = Yii::$app->db->createCommand("
    				CALL get_contactdetails_bytype(:memberid)")
    				->bindValue(':memberid', $memberId)
    				->queryOne();
    		if(!empty($contactDetails))
    		{
    			return $contactDetails;
    		}else {
    			return true;
    		}
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To get member 
     * details
     * @param unknown $memberId
     * @return \yii\db\false|boolean
     */
    public static function getMemberById($memberId)
    {
    	try {
    		$memberById = Yii::$app->db->createCommand("
    				CALL get_member(:memberid)")
    				->bindValue(':memberid', $memberId)
    				->queryOne();
    		return $memberById;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To Get member count
     * @param unknown $empid
     * @param unknown $lastUpdatedOn
     * @return \yii\db\false|boolean
     */
    public static function getMemberCount($empid,$lastUpdatedOn)
    {
    	try {
    		$memberCount = Yii::$app->db->createCommand("
    				CALL getnewmebercount(:lastupdatedon,:userid)")
    				->bindValue(':lastupdatedon', $lastUpdatedOn)
    				->bindValue(':userid', $empid)
    				->queryOne();
    		return $memberCount;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
   /**
    * To get the contacts
    * @param unknown $institutionId
    * @param unknown $filterByUpdatedContacts
    * @param unknown $lastUpdatedOn
    * @return mixed
    */
    public static function getContacts($institutionId,$filterByUpdatedContacts,$lastUpdatedOn)
    {
    	try {
    		$contactDetails = Yii::$app->db->createCommand("
    				CALL getcontacts(:institutionid,:filterbyupdatedcontacts,:lastupdatedon)")
    				->bindValue(':institutionid', $institutionId)
    				->bindValue(':filterbyupdatedcontacts', $filterByUpdatedContacts)
    				->bindValue(':lastupdatedon', $lastUpdatedOn)
    				->queryAll();
    		return $contactDetails;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
   /**
    * To Get existing member details
    * @param unknown $memberId
    * @return \yii\db\false|boolean
    */
    public static function getExistingMemberDetails($memberId)
    {
    	try {
    		$memberDetails = Yii::$app->db->createCommand("
    				select * from member where memberid=:memberid AND member.active=1")
    				->bindValue(':memberid', $memberId)
    				->queryOne();
    		return $memberDetails;
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To get user id from member id
     * @param unknown $memberid
     * @param unknown $usertype
     * @return string|NULL|\yii\db\false
     */
    public static function getUserIdFromMemberID($memberid,$institutionId, $usertype) 
    {
        $sql = "SELECT userid from usermember where memberid = :memberid and usertype = :usertype and institutionid = :institutionid";
        $params = [':memberid' => $memberid, ':usertype'=> $usertype,':institutionid' => $institutionId];
        return yii::$app->db->createCommand($sql)->bindValues($params)->queryScalar();
    }
	
    /**
     * To get member name
     * by using member id
     * @param unknown $memberId
     * @return \yii\db\false|boolean
     */
    public static function getMemberNameByMemberId($memberId)
    {
    	try {
    		
    		$memberName = Yii::$app->db->createCommand("
    						CALL member_name_by_memberid(:memberid)")
    		    			->bindValue(':memberid', $memberId)
    		    			->queryOne();
    		return $memberName;
    	} catch (Exception $e) {
    		return false;
    	}
    }
   /**
    * To get member id
    * @param unknown $userId
    * @param unknown $institutionId
    * @param unknown $userType
    * @return \yii\db\false|boolean
    */
    public static function getMemberId($userId, $institutionId, $userType)
    {   
    	try {
    		$sql = 'select memberid from usermember where userid=:userid and institutionid=:institutionid and usertype=:usertype';
    		$memberId = Yii::$app->db->createCommand($sql)
    				->bindValue(':userid', $userId)
    				->bindValue(':institutionid', $institutionId)
    				->bindValue(':usertype', $userType)
    				->queryOne();
    		return $memberId;
    	} catch (Exception $e) {
    		return false;
    	}
    }
	/**
	 * To get the member details for sync
	 * @param unknown $userId
	 * @param unknown $lastUpdatedOn
	 * @return boolean
	 */
    public function getMemberDetailsFileForSync($userId, $lastUpdatedOn)
    {
        try{
            $memberDetails = Yii::$app->db->createCommand("CALL getmember_details_forsync(:userId, :lastUpdatedOn)")
                    ->bindValue(':userId', $userId)
                    ->bindValue(':lastUpdatedOn', $lastUpdatedOn)
                    ->queryAll();

            if(!empty($memberDetails)){
                return $memberDetails;
            }
            else{
                return true;
            }
        }
        catch(ErrorException $e){
            return false;
        }
    }
    public static function getMemberForEventAutoSuggest($institutionId)
    {   
        $response = [];
        try{
            $response = Yii::$app->db->createCommand("CALL getmemberdetailsforevents_auto_suggest(:institutionId)")
                    ->bindValue(':institutionId', $institutionId)
                    ->queryAll();
        } catch(Exception $e){
            yii::error('Error while fetching data for auto suggest in getMemberForEventAutoSuggest');
        }
        return $response;
    }
	/**
	 * To get the deleted contact for sync
	 * @param unknown $userId
	 * @param unknown $lastUpdatedOn
	 * @return boolean
	 */
    public static function getDeletedContactsForSync($userId, $lastUpdatedOn){
        try{
            $memberDetails = Yii::$app->db->createCommand("CALL get_deleted_members_for_sync(:userId, :lastUpdatedOn)")
                    ->bindValue(':userId', $userId)
                    ->bindValue(':lastUpdatedOn', $lastUpdatedOn)
                    ->queryAll();
            if(!empty($memberDetails)){
                return $memberDetails;
            }
            else{
                return true;
            }
        }
        catch(ErrorException $e){
            return false;
        }
    }
    /**
     * 
     * @param unknown $categoryId
     * @param unknown $subcategoryId
     * @param unknown $data
     * @param unknown $memberId
     * @return boolean
     */
    
    public static function approveMemberDetails($categoryId,$subcategoryId,$data,$memberId)
    {
    	try {
    		if($categoryId == 4 || $categoryId == 20 || $categoryId == 21)
    		{
    			$data = date(yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($data));
    		}
    		
    		$approveMember = Yii::$app->db->createCommand("CALL approvedetails(:categoryid,:subcategoryid,:data,:memberid)")
    						->bindValue(':categoryid', $categoryId)
    						->bindValue(':subcategoryid', $subcategoryId)
    						->bindValue(':data', $data)
    						->bindValue(':memberid', $memberId)
    						->execute();
    		return true;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To update Member Image
     */
    public static function updateMemberImage($memberId,$thumbnailImage)
    {
    	try {
    		$sql = "update member set memberImageThumbnail=:memberImageThumbnail , member_pic =:memberImageThumbnail  where memberid=:memberid";
    		$updateImage = Yii::$app->db->createCommand($sql)
							->bindValue(':memberImageThumbnail', $thumbnailImage)    
							->bindValue(':memberid', $memberId)
							->execute();
    		if($updateImage)
    		{
    			return true;
    		}
    		else{
    			return false;
    		}
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * update spouse image
     */
    public static function updateSpouseImage($memberId,$spouseImage)
    {
    	$sql = "update member set spouseImageThumbnail=:spouseImageThumbnail , spouse_pic =:spouseImageThumbnail where memberid=:memberid";
    	$updateSpouseImage = Yii::$app->db->createCommand($sql)
				    	->bindValue(':spouseImageThumbnail', $spouseImage)
				    	->bindValue(':memberid', $memberId)
				    	->execute();
    	if($updateSpouseImage)
    	{
    		return true;
    	}
    	else{
    		return false;
    	}
    }
  
    /**
     * To send profile approved notification
     */
    public static function profileApprovedNotification($memberId,$userType,$institutionId,$institutionName)
    {
    	try {
    	    $pushNotificationSender = Yii::$app->PushNotificationHandler;
    		if($memberId)
    		{
    			$memberNumbers = ExtendedMember::getMemberSpouseMobileNumberByMemberId($memberId);
    		}
    		if($userType == ExtendedMember::USER_TYPE_MEMBER)
    		{
    			if($memberNumbers['member_mobile1'])
    			{
    				$memberMobileNo = (int)$memberNumbers['member_mobile1'];
    			}
    		}
    		elseif ($userType == ExtendedMember::USER_TYPE_SPOUSE)
    		{
    			if($memberNumbers['spouse_mobile1'])
    			{
    				$memberMobileNo = (int)$memberNumbers['spouse_mobile1'];
    			}
    		}
    		if($memberMobileNo)
    		{
    			$userDetails = ExtendedUserCredentials::getUserIdByMemberMobile($memberMobileNo);
    			$userId = $userDetails['id'];
    		}
    		if($userId)
    		{
    			$deviceList = ExtendedDevicedetails::getUserDevices($userId);
    			if($deviceList && count($deviceList) > 0)
    			{
    				$message = "Your request for data updation has been processed. Please check your email.";
    				$notificationType = 'profile-approved';
    				foreach ($deviceList as $deviceItem)
    				{
    					$requestData  = $pushNotificationSender->setPushNotificationRequest($deviceItem['deviceid'],$message,$notificationType,$institutionId,$memberId,$institutionName,strtolower($deviceItem['devicetype']),$userId);
    					$response     = $pushNotificationSender->sendNotification(strtolower($deviceItem['devicetype']), $deviceItem['deviceid'], $requestData);
    				}
    			}
    		}
    		
    	} catch (\ErrorException $e) {
    			yii::error($e->getMessage());
    	}
    }
    /**
     * To get mobile numbers of
     * member and spouse
     */
    public static function getMemberSpouseMobileNumberByMemberId($memberId)
    {
    	try {
    		$sql ="select memberid,member_mobile1,spouse_mobile1 from member where memberid=:memberid";
    		$mobileNumbers = Yii::$app->db->createCommand($sql)
    						->bindValue(':memberid', $memberId)
    						->queryOne();
    		return $mobileNumbers;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }

	/**
     * 
     */
    public static function getMemberData($userId,$institutionId)
    {
    	try {
    		$memberDetails = Yii::$app->db->createCommand("CALL get_member_email(:userId, :institutionid)")
			    		->bindValue(':userId', $userId)
			    		->bindValue(':institutionid', $institutionId)
			    		->queryOne();
    		return $memberDetails;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }

     /**
     * To get memberName
     */
    public static function getUserName($userId, $institutionId)
    {
        try {
            $sql ="select m.firstName,m.middleName,m.lastName from member m inner join usermember u on m.memberid = u.memberid where u.userid=:userid and 
                u.institutionid=:institutionid";
            $userDetails = Yii::$app->db->createCommand($sql)
                            ->bindValue(':userid', $userId)
                            ->bindValue(':institutionid', $institutionId)
                            ->queryOne();
            if(!empty($userDetails)){
                return $userDetails;
            }
            else{
                return false;
            }
            
        } catch (Exception $e) {
            return false;
        }
    }

     /**
     * To get the spouse name 
     * @param $memberId int
     */
    public static function getSpouseName($memberId)
    {
        try {
            $memberName = Yii::$app->db->createCommand('select m.spouse_firstName as firstName, m.spouse_middleName as middleName, m.spouse_lastName as lastName from `member` m 
                            where m.memberid=:memberid')
                            ->bindValue(':memberid' , $memberId )
                            ->queryOne();
            return $memberName;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * To get the member email  
     */
    public static function getMemberEmail($userId, $institutionId)
    {
        try {
            $memberDetails = Yii::$app->db->createCommand("CALL get_emailid(:userId, :institutionId)")
                        ->bindValue(':userId', $userId)
                        ->bindValue(':institutionId', $institutionId)
                        ->queryOne();
            return $memberDetails;
            
        } catch (Exception $e) {
            return false;
        }
    }

     /**
     * To get memberName by institutionId
     */
    public static function getMemberNameByInstitutionId($userId, $institutionId)
    {
        try {
            $sql =  "SELECT m.firstName, 
                        m.middleName, 
                        m.lastName, 
                        i.institutionlogo, 
                        t.Description as title 
                    FROM member m 
                    INNER JOIN usermember u ON m.memberid = u.memberid 
                    INNER JOIN title t ON  t.TitleId = m.membertitle
                    INNER JOIN institution i ON u.institutionid = i.id 
                    WHERE u.userid = :userid and u.institutionid = :institutionid";
            $userDetails = Yii::$app->db->createCommand($sql)
                            ->bindValue(':userid', $userId)
                            ->bindValue(':institutionid', $institutionId)
                            ->queryOne();
            if(!empty($userDetails)){
                return $userDetails;
            }
            else{
                return false;
            }
            
        } catch (Exception $e) {
            return false;
        }
    }
}
