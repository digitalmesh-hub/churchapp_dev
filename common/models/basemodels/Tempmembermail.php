<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "tempmembermail".
 *
 * @property int $id
 * @property int $temp_memberid
 * @property int $temp_institutionid
 * @property string $temp_memberno
 * @property string $temp_membershiptype
 * @property string $temp_membersince
 * @property string $temp_firstName
 * @property string $temp_middleName
 * @property string $temp_lastName
 * @property string $temp_business_address1
 * @property string $temp_business_address2
 * @property string $temp_business_address3
 * @property string $temp_business_district
 * @property string $temp_business_state
 * @property string $temp_business_pincode
 * @property string $temp_member_dob
 * @property string $temp_member_mobile1
 * @property string $temp_member_mobile2
 * @property string $temp_member_business_Phone1
 * @property string $temp_member_business_Phone2
 * @property string $temp_member_residence_Phone1
 * @property string $temp_member_residence_Phone2
 * @property string $temp_member_email
 * @property string $temp_spouse_firstName
 * @property string $temp_spouse_middleName
 * @property string $temp_spouse_lastName
 * @property string $temp_spouse_dob
 * @property string $temp_dom
 * @property string $temp_spouse_mobile1
 * @property string $temp_spouse_mobile2
 * @property string $temp_spouse_email
 * @property string $temp_residence_address1
 * @property string $temp_residence_address2
 * @property string $temp_residence_address3
 * @property string $temp_residence_district
 * @property string $temp_residence_state
 * @property string $temp_residence_pincode
 * @property string $temp_member_pic
 * @property string $temp_spouse_pic
 * @property string $temp_app_reg_member
 * @property string $temp_app_reg_spouse
 * @property int $temp_active
 * @property string $temp_businessemail
 * @property int $temp_membertitle
 * @property int $temp_spousetitle
 * @property string $temp_membernickname
 * @property string $temp_spousenickname
 * @property string $temp_lastupdated
 * @property string $temp_homechurch
 * @property string $temp_occupation
 * @property string $temp_createddate
 * @property int $temp_createdby
 * @property string $temp_modifieddate
 * @property int $temp_modifiedby
 * @property int $temp_approved
 * @property string $temp_spouseoccupation
 * @property string $temp_member_mobile1_countrycode
 * @property string $temp_spouse_mobile1_countrycode
 * @property string $temp_member_business_phone1_countrycode
 * @property string $temp_member_business_phone1_areacode
 * @property string $temp_member_business_phone2_countrycode
 * @property string $temp_memberImageThumbnail
 * @property string $temp_spouseImageThumbnail
 * @property string $tempusertype
 * @property string $temp_member_business_Phone3
 * @property string $temp_member_business_phone3_countrycode
 * @property string $temp_member_business_phone3_areacode
 * @property string $tempmemberBloodGroup
 * @property string $tempspouseBloodGroup
 * @property string $temp_member_residence_Phone1_countrycode
 * @property string $temp_member_residence_Phone1_areacode
 *
 * @property Usercredentials $tempCreatedby
 * @property Institution $tempInstitution
 * @property Member $tempMember
 * @property Title $tempMembertitle
 * @property Usercredentials $tempModifiedby
 * @property Title $tempSpousetitle
 */
class Tempmembermail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tempmembermail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['temp_memberid', 'temp_institutionid'], 'required'],
            [['temp_memberid', 'temp_institutionid', 'temp_membertitle', 'temp_spousetitle', 'temp_createdby', 'temp_modifiedby'], 'integer'],
            [['temp_membersince', 'temp_member_dob', 'temp_spouse_dob', 'temp_dom', 'temp_app_reg_member', 'temp_app_reg_spouse', 'temp_lastupdated', 'temp_createddate', 'location'], 'safe'],
            [['temp_memberno', 'temp_membershiptype', 'temp_membernickname', 'temp_spousenickname'], 'string', 'max' => 25],
            [['temp_firstName', 'temp_middleName', 'temp_lastName', 'temp_business_district', 'temp_business_state', 'temp_business_pincode', 'temp_spouse_firstName', 'temp_spouse_middleName', 'temp_spouse_lastName', 'temp_residence_address3', 'temp_residence_district', 'temp_residence_state', 'temp_modifieddate', 'temp_spouseoccupation'], 'string', 'max' => 45],
            [['temp_business_address1', 'temp_business_address2', 'temp_residence_address1', 'temp_residence_address2'], 'string', 'max' => 75],
            [['temp_business_address3'], 'string', 'max' => 50],
            [['temp_member_mobile1', 'temp_member_mobile2', 'temp_member_business_Phone1', 'temp_member_business_Phone2', 'temp_member_residence_Phone1', 'temp_member_residence_Phone2', 'temp_spouse_mobile1', 'temp_spouse_mobile2', 'temp_member_business_Phone3'], 'string', 'max' => 13],
            [['temp_member_email', 'temp_spouse_email', 'temp_businessemail'], 'string', 'max' => 150],
            [['temp_residence_pincode', 'tempmemberBloodGroup', 'tempspouseBloodGroup'], 'string', 'max' => 15],
            [['temp_member_pic', 'temp_spouse_pic', 'temp_memberImageThumbnail', 'temp_spouseImageThumbnail'], 'string', 'max' => 200],
            [['temp_active', 'temp_approved', 'temp_member_mobile1_countrycode', 'temp_spouse_mobile1_countrycode', 'temp_member_business_phone1_countrycode', 'temp_member_business_phone2_countrycode', 'temp_member_business_phone3_countrycode', 'temp_member_residence_Phone1_countrycode'], 'string', 'max' => 4],
            [['temp_homechurch', 'temp_occupation'], 'string', 'max' => 250],
            [['temp_member_business_phone1_areacode', 'temp_member_business_phone3_areacode', 'temp_member_residence_Phone1_areacode'], 'string', 'max' => 5],
            [['tempusertype'], 'string', 'max' => 1],
            [['temp_createdby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['temp_createdby' => 'id']],
            [['temp_institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['temp_institutionid' => 'id']],
            [['temp_memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['temp_memberid' => 'memberid']],
            [['temp_membertitle'], 'exist', 'skipOnError' => true, 'targetClass' => Title::className(), 'targetAttribute' => ['temp_membertitle' => 'TitleId']],
            [['temp_modifiedby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['temp_modifiedby' => 'id']],
            [['temp_spousetitle'], 'exist', 'skipOnError' => true, 'targetClass' => Title::className(), 'targetAttribute' => ['temp_spousetitle' => 'TitleId']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'temp_memberid' => 'Temp Memberid',
            'temp_institutionid' => 'Temp Institutionid',
            'temp_memberno' => 'Temp Memberno',
            'temp_membershiptype' => 'Temp Membershiptype',
            'temp_membersince' => 'Temp Membersince',
            'temp_firstName' => 'Temp First Name',
            'temp_middleName' => 'Temp Middle Name',
            'temp_lastName' => 'Temp Last Name',
            'temp_business_address1' => 'Temp Business Address1',
            'temp_business_address2' => 'Temp Business Address2',
            'temp_business_address3' => 'Temp Business Address3',
            'temp_business_district' => 'Temp Business District',
            'temp_business_state' => 'Temp Business State',
            'temp_business_pincode' => 'Temp Business Pincode',
            'temp_member_dob' => 'Temp Member Dob',
            'temp_member_mobile1' => 'Temp Member Mobile1',
            'temp_member_mobile2' => 'Temp Member Mobile2',
            'temp_member_business_Phone1' => 'Temp Member Business  Phone1',
            'temp_member_business_Phone2' => 'Temp Member Business  Phone2',
            'temp_member_residence_Phone1' => 'Temp Member Residence  Phone1',
            'temp_member_residence_Phone2' => 'Temp Member Residence  Phone2',
            'temp_member_email' => 'Temp Member Email',
            'temp_spouse_firstName' => 'Temp Spouse First Name',
            'temp_spouse_middleName' => 'Temp Spouse Middle Name',
            'temp_spouse_lastName' => 'Temp Spouse Last Name',
            'temp_spouse_dob' => 'Temp Spouse Dob',
            'temp_dom' => 'Temp Dom',
            'temp_spouse_mobile1' => 'Temp Spouse Mobile1',
            'temp_spouse_mobile2' => 'Temp Spouse Mobile2',
            'temp_spouse_email' => 'Temp Spouse Email',
            'temp_residence_address1' => 'Temp Residence Address1',
            'temp_residence_address2' => 'Temp Residence Address2',
            'temp_residence_address3' => 'Temp Residence Address3',
            'temp_residence_district' => 'Temp Residence District',
            'temp_residence_state' => 'Temp Residence State',
            'temp_residence_pincode' => 'Temp Residence Pincode',
            'temp_member_pic' => 'Temp Member Pic',
            'temp_spouse_pic' => 'Temp Spouse Pic',
            'temp_app_reg_member' => 'Temp App Reg Member',
            'temp_app_reg_spouse' => 'Temp App Reg Spouse',
            'temp_active' => 'Temp Active',
            'temp_businessemail' => 'Temp Businessemail',
            'temp_membertitle' => 'Temp Membertitle',
            'temp_spousetitle' => 'Temp Spousetitle',
            'temp_membernickname' => 'Temp Membernickname',
            'temp_spousenickname' => 'Temp Spousenickname',
            'temp_lastupdated' => 'Temp Lastupdated',
            'temp_homechurch' => 'Temp Homechurch',
            'temp_occupation' => 'Temp Occupation',
            'temp_createddate' => 'Temp Createddate',
            'temp_createdby' => 'Temp Createdby',
            'temp_modifieddate' => 'Temp Modifieddate',
            'temp_modifiedby' => 'Temp Modifiedby',
            'temp_approved' => 'Temp Approved',
            'temp_spouseoccupation' => 'Temp Spouseoccupation',
            'temp_member_mobile1_countrycode' => 'Temp Member Mobile1 Countrycode',
            'temp_spouse_mobile1_countrycode' => 'Temp Spouse Mobile1 Countrycode',
            'temp_member_business_phone1_countrycode' => 'Temp Member Business Phone1 Countrycode',
            'temp_member_business_phone1_areacode' => 'Temp Member Business Phone1 Areacode',
            'temp_member_business_phone2_countrycode' => 'Temp Member Business Phone2 Countrycode',
            'temp_memberImageThumbnail' => 'Temp Member Image Thumbnail',
            'temp_spouseImageThumbnail' => 'Temp Spouse Image Thumbnail',
            'tempusertype' => 'Tempusertype',
            'temp_member_business_Phone3' => 'Temp Member Business  Phone3',
            'temp_member_business_phone3_countrycode' => 'Temp Member Business Phone3 Countrycode',
            'temp_member_business_phone3_areacode' => 'Temp Member Business Phone3 Areacode',
            'tempmemberBloodGroup' => 'Tempmember Blood Group',
            'tempspouseBloodGroup' => 'Tempspouse Blood Group',
            'temp_member_residence_Phone1_countrycode' => 'Temp Member Residence  Phone1 Countrycode',
            'temp_member_residence_Phone1_areacode' => 'Temp Member Residence  Phone1 Areacode',
            'location' => 'Location'
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
    public function getTempCreatedby()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'temp_createdby']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'temp_institutionid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'temp_memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempMembertitle()
    {
        return $this->hasOne(Title::className(), ['TitleId' => 'temp_membertitle']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempModifiedby()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'temp_modifiedby']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempSpousetitle()
    {
        return $this->hasOne(Title::className(), ['TitleId' => 'temp_spousetitle']);
    }
}
