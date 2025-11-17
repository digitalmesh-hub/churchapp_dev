<?php

namespace common\models\basemodels;

use Yii;

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
 * @property int $zone_id
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
class Member extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['institutionid', 'memberno', 'membershiptype', 'firstName', 'lastName'], 'required'],
            [['institutionid', 'membertitle', 'spousetitle', 'countrycode', 'areacode', 'member_mobile1_countrycode', 'spouse_mobile1_countrycode', 'membertype', 'staffdesignation', 'familyunitid', 'zone_id'], 'integer'],
            [['membersince', 'member_dob', 'spouse_dob', 'dom', 'app_reg_member', 'app_reg_spouse', 'lastupdated', 'createddate', 'location'], 'safe'],
            [['memberno'], 'string', 'max' => 75],
            [['membershiptype', 'membernickname', 'spousenickname'], 'string', 'max' => 25],
            [['firstName', 'middleName', 'lastName', 'business_district', 'business_state', 'business_pincode', 'spouse_firstName', 'spouse_middleName', 'spouse_lastName', 'residence_address3', 'residence_district', 'residence_state', 'newmembernum'], 'string', 'max' => 45],
            [['business_address1', 'business_address2', 'residence_address1', 'residence_address2', 'homechurch', 'occupation', 'companyname'], 'string', 'max' => 100],
            [['business_address3'], 'string', 'max' => 50],
            [['member_mobile1', 'member_mobile2', 'member_musiness_Phone1', 'member_business_Phone2', 'member_residence_Phone1', 'member_residence_Phone2', 'spouse_mobile1', 'spouse_mobile2', 'member_business_Phone3'], 'string', 'max' => 13],
            [['member_email', 'spouse_email', 'businessemail'], 'string', 'max' => 150],
            [['residence_pincode', 'memberbloodgroup', 'spousebloodgroup'], 'string', 'max' => 15],
            [['member_pic', 'spouse_pic', 'memberImageThumbnail', 'spouseImageThumbnail'], 'string', 'max' => 200],
            [['active', 'member_business_phone1_countrycode', 'member_business_phone2_countrycode', 'member_business_phone3_countrycode', 'member_residence_Phone1_countrycode', 'member_residence_Phone2_countrycode'], 'string', 'max' => 4],
            [['spouseoccupation'], 'string', 'max' => 250],
            [['member_business_phone1_areacode', 'member_business_phone3_areacode', 'member_residence_phone1_areacode', 'member_residence_phone2_areacode', 'member_business_phone2_areacode'], 'string', 'max' => 5],
            [['active_spouse', 'confirmed', 'confirmed_spouse'], 'boolean'],
            [['head_of_family'], 'string', 'max' => 1],
            [['head_of_family'], 'in', 'range' => ['m', 's']],
            [['countrycode'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['countrycode' => 'countryid']],
            [['familyunitid'], 'exist', 'skipOnError' => true, 'targetClass' => Familyunit::className(), 'targetAttribute' => ['familyunitid' => 'familyunitid']],
            [['zone_id'], 'exist', 'skipOnError' => true, 'targetClass' => Zone::className(), 'targetAttribute' => ['zone_id' => 'zoneid']],
            [['member_mobile1_countrycode'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['member_mobile1_countrycode' => 'countryid']],
            [['membertitle'], 'exist', 'skipOnError' => true, 'targetClass' => Title::className(), 'targetAttribute' => ['membertitle' => 'TitleId']],
            [['spouse_mobile1_countrycode'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['spouse_mobile1_countrycode' => 'countryid']],
            [['spousetitle'], 'exist', 'skipOnError' => true, 'targetClass' => Title::className(), 'targetAttribute' => ['spousetitle' => 'TitleId']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['batch'],'string', 'max' => 5]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'memberid' => 'Memberid',
            'institutionid' => 'Institutionid',
            'memberno' => 'Memberno',
            'membershiptype' => 'Membershiptype',
            'membersince' => 'Membersince',
            'firstName' => 'First Name',
            'middleName' => 'Middle Name',
            'lastName' => 'Last Name',
            'business_address1' => 'Business Address1',
            'business_address2' => 'Business Address2',
            'business_address3' => 'Business Address3',
            'business_district' => 'Business District',
            'business_state' => 'Business State',
            'business_pincode' => 'Business Pincode',
            'member_dob' => 'Member Dob',
            'member_mobile1' => 'Member Mobile1',
            'member_mobile2' => 'Member Mobile2',
            'member_musiness_Phone1' => 'Member Musiness  Phone1',
            'member_business_Phone2' => 'Member Business  Phone2',
            'member_residence_Phone1' => 'Member Residence  Phone1',
            'member_residence_Phone2' => 'Member Residence  Phone2',
            'member_email' => 'Member Email',
            'spouse_firstName' => 'Spouse First Name',
            'spouse_middleName' => 'Spouse Middle Name',
            'spouse_lastName' => 'Spouse Last Name',
            'spouse_dob' => 'Spouse Dob',
            'dom' => 'Dom',
            'spouse_mobile1' => 'Spouse Mobile1',
            'spouse_mobile2' => 'Spouse Mobile2',
            'spouse_email' => 'Spouse Email',
            'residence_address1' => 'Residence Address1',
            'residence_address2' => 'Residence Address2',
            'residence_address3' => 'Residence Address3',
            'residence_district' => 'Residence District',
            'residence_state' => 'Residence State',
            'residence_pincode' => 'Residence Pincode',
            'member_pic' => 'Member Pic',
            'spouse_pic' => 'Spouse Pic',
            'app_reg_member' => 'App Reg Member',
            'app_reg_spouse' => 'App Reg Spouse',
            'active' => 'Active',
            'businessemail' => 'Businessemail',
            'membertitle' => 'Membertitle',
            'spousetitle' => 'Spousetitle',
            'membernickname' => 'Membernickname',
            'spousenickname' => 'Spousenickname',
            'lastupdated' => 'Lastupdated',
            'createddate' => 'Createddate',
            'homechurch' => 'Homechurch',
            'occupation' => 'Occupation',
            'spouseoccupation' => 'Spouseoccupation',
            'countrycode' => 'Countrycode',
            'areacode' => 'Areacode',
            'member_mobile1_countrycode' => 'Member Mobile1 Countrycode',
            'spouse_mobile1_countrycode' => 'Spouse Mobile1 Countrycode',
            'member_business_phone1_countrycode' => 'Member Business Phone1 Countrycode',
            'member_business_phone1_areacode' => 'Member Business Phone1 Areacode',
            'member_business_phone2_countrycode' => 'Member Business Phone2 Countrycode',
            'memberImageThumbnail' => 'Member Image Thumbnail',
            'spouseImageThumbnail' => 'Spouse Image Thumbnail',
            'membertype' => 'Membertype',
            'staffdesignation' => 'Staffdesignation',
            'member_business_Phone3' => 'Member Business  Phone3',
            'member_business_phone3_countrycode' => 'Member Business Phone3 Countrycode',
            'member_business_phone3_areacode' => 'Member Business Phone3 Areacode',
            'newmembernum' => 'Newmembernum',
            'familyunitid' => 'Familyunitid',
            'zone_id' => 'Zone',
            'memberbloodgroup' => 'Memberbloodgroup',
            'spousebloodgroup' => 'Spousebloodgroup',
            'member_residence_phone1_areacode' => 'Member Residence Phone1 Areacode',
            'member_residence_Phone1_countrycode' => 'Member Residence  Phone1 Countrycode',
            'member_residence_phone2_areacode' => 'Member Residence Phone2 Areacode',
            'member_residence_Phone2_countrycode' => 'Member Residence  Phone2 Countrycode',
            'companyname' => 'Companyname',
            'member_business_phone2_areacode' => 'Member Business Phone2 Areacode',
            'batch' => 'Batch',
            'location' => 'Location',
            'active_spouse' => 'Active Spouse',
            'confirmed' => 'Confirmed',
            'confirmed_spouse' => 'Confirmed Spouse',
            'head_of_family' => 'Head of Family'
        ];
    }
    /**
     * Get the latitude from the location.
     *
     * @return string|null The latitude value or null if not set.
     */
    public function getLatitude()
    {
        $location = $this->location ? $this->location : [];
        return $location['latitude'] ?? null; 
    }

    /**
     * Set the latitude in the location.
     *
     * @param string $value The latitude value to set.
     * @return void
     */
    public function setLatitude($value)
    {
        $location = $this->location ? $this->location : [];
        $location['latitude'] = $value;
        $this->location = $location;
    }


    /**
     * Get the longitude from the location.
     *
     * @return string|null The latitude value or null if not set.
     */
    public function getLongitude()
    {
        $location = $this->location ? $this->location : [];
        return $location['longitude'] ?? null; 
    }

    /**
     * Set the longitude in the location.
     *
     * @param string $value The longitude value to set.
     * @return void
     */
    public function setLongitude($value)
    {
        $location = $this->location ? $this->location : [];
        $location['longitude'] = $value;
        $this->location = $location;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttendances()
    {
        return $this->hasMany(Attendance::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBills()
    {
        return $this->hasMany(Bills::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillseendetails()
    {
        return $this->hasMany(Billseendetails::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarts()
    {
        return $this->hasMany(Cart::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommittees()
    {
        return $this->hasMany(Committee::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeleteDependants()
    {
        return $this->hasMany(DeleteDependant::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeleteUsermembers()
    {
        return $this->hasMany(DeleteUsermember::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDependants()
    {
        return $this->hasMany(Dependant::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEditmembers()
    {
        return $this->hasMany(Editmember::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountrycode0()
    {
        return $this->hasOne(Country::className(), ['countryid' => 'countrycode']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFamilyunit()
    {
        return $this->hasOne(Familyunit::className(), ['familyunitid' => 'familyunitid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZone()
    {
        return $this->hasOne(Zone::className(), ['zoneid' => 'zone_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMemberMobile1Countrycode()
    {
        return $this->hasOne(Country::className(), ['countryid' => 'member_mobile1_countrycode']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembertitle0()
    {
        return $this->hasOne(Title::className(), ['TitleId' => 'membertitle']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpouseMobile1Countrycode()
    {
        return $this->hasOne(Country::className(), ['countryid' => 'spouse_mobile1_countrycode']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpousetitle0()
    {
        return $this->hasOne(Title::className(), ['TitleId' => 'spousetitle']);
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
    public function getMemberadditionalinfos()
    {
        return $this->hasMany(Memberadditionalinfo::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMemberroles()
    {
        return $this->hasMany(Memberrole::className(), ['MemberID' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationlogs()
    {
        return $this->hasMany(Notificationlog::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdernotifications()
    {
        return $this->hasMany(Ordernotifications::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdernotificationsents()
    {
        return $this->hasMany(Ordernotificationsent::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentTransactions()
    {
        return $this->hasMany(PaymentTransactions::className(), ['memberId' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfileupdatenotifications()
    {
        return $this->hasMany(Profileupdatenotification::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfileupdatenotificationsents()
    {
        return $this->hasMany(Profileupdatenotificationsent::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRsvpdetails()
    {
        return $this->hasMany(Rsvpdetails::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSettings()
    {
        return $this->hasMany(Settings::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveystatuses()
    {
        return $this->hasMany(Surveystatus::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmembers()
    {
        return $this->hasMany(Tempmember::className(), ['temp_memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmemberadditionalinfos()
    {
        return $this->hasMany(Tempmemberadditionalinfo::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmemberadditionalinfomails()
    {
        return $this->hasMany(Tempmemberadditionalinfomail::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmembermails()
    {
        return $this->hasMany(Tempmembermail::className(), ['temp_memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestusermembers()
    {
        return $this->hasMany(Testusermember::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsermembers()
    {
        return $this->hasMany(Usermember::className(), ['memberid' => 'memberid']);
    }
    
    public function getFullNameWithMemberNo()
    {
        $name = $this->firstName;
        
        if($this->middleName)
            $name .= ' '.$this->middleName;

        if ($this->lastName)
            $name .= ' '.$this->lastName;

        return $name."(".$this->memberno.")";
    }

    /**
     * Get the full name with title of member
     */
    public function getFullNameWithTitle()
    {
        $title = $this->membertitle0 ? $this->membertitle0->Description : ''; 

        $name = $this->firstName;
        
        if($this->middleName)
            $name .= ' '.$this->middleName;

        if ($this->lastName)
            $name .= ' '.$this->lastName;

        return $title . ' '. $name;
    }

    /**
     * Get the location details based on memberId
     *
     * @param int $memberId
     * @return array
     */
    public static function getLocationByMemberId($memberId)
    {
        $locationDetails = ['latitude' => '', 'longitude' => ''];
        $location = self::find()
            ->select(['location']) 
            ->where(['memberid' => $memberId]) 
            ->scalar();

        if (empty($location)) {
            return $locationDetails;
        }else{
            $locationArray = json_decode($location, true);
            if (!empty($locationArray)) {
				$locationDetails = ['latitude' => $locationArray['latitude'] ?? '', 'longitude' => $locationArray['longitude'] ?? ''];
			}
        }

        return $locationDetails;
    }
}
