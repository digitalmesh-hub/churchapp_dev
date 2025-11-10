<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "delete_member".
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
 *
 * @property Institution $institution
 * @property Title $membertitle0
 * @property Title $spousetitle0
 */
class DeleteMember extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'delete_member';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['institutionid', 'memberno', 'membershiptype', 'firstName', 'lastName'], 'required'],
            [['institutionid', 'membertitle', 'spousetitle'], 'integer'],
            [['membersince', 'member_dob', 'spouse_dob', 'dom', 'app_reg_member', 'app_reg_spouse', 'lastupdated', 'createddate'], 'safe'],
            [['memberno', 'business_address1', 'business_address2', 'residence_address1', 'residence_address2'], 'string', 'max' => 75],
            [['membershiptype'], 'string', 'max' => 25],
            [['firstName', 'middleName', 'lastName', 'business_district', 'business_state', 'business_pincode', 'spouse_firstName', 'spouse_middleName', 'spouse_lastName', 'residence_address3', 'residence_district', 'residence_state'], 'string', 'max' => 45],
            [['business_address3'], 'string', 'max' => 50],
            [['member_mobile1', 'member_mobile2', 'member_musiness_Phone1', 'member_business_Phone2', 'member_residence_Phone1', 'member_residence_Phone2', 'spouse_mobile1', 'spouse_mobile2'], 'string', 'max' => 13],
            [['member_email', 'spouse_email', 'businessemail'], 'string', 'max' => 150],
            [['residence_pincode'], 'string', 'max' => 15],
            [['member_pic', 'spouse_pic'], 'string', 'max' => 200],
            [['active'], 'string', 'max' => 4],
            [['membernickname', 'spousenickname'], 'string', 'max' => 10],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['membertitle'], 'exist', 'skipOnError' => true, 'targetClass' => Title::className(), 'targetAttribute' => ['membertitle' => 'TitleId']],
            [['spousetitle'], 'exist', 'skipOnError' => true, 'targetClass' => Title::className(), 'targetAttribute' => ['spousetitle' => 'TitleId']],
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
        ];
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
    public function getMembertitle0()
    {
        return $this->hasOne(Title::className(), ['TitleId' => 'membertitle']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpousetitle0()
    {
        return $this->hasOne(Title::className(), ['TitleId' => 'spousetitle']);
    }
}
