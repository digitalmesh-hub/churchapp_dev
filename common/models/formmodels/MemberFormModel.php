<?php
namespace common\models\formmodels;

use Yii;
use yii\base\Model;
use common\models\extendedmodels\ExtendedTitle;
use yii\helpers\ArrayHelper;

class MemberFormModel extends Model
{
    public $member_photo;
    public $member_title;
    public $member_first_name;
    public $member_email;
    public $member_last_name;
    public $member_middle_name;
    public $member_nick_name;
    public $member_number_private;
    public $member_mobile_number;
    public $member_country_code;
    public $member_dob;
    public $member_occupation;
    public $member_blood_group;

    public $spouse_photo;
    public $spouse_title;
    public $spouse_first_name;
    public $spouse_email;
    public $spouse_last_name;
    public $spouse_middle_name;
    public $spouse_nick_name;
    public $spouse_number_private;
    public $spouse_mobile_number;
    public $spouse_country_code;
    public $spouse_dob;
    public $spouse_occupation;
    public $spouse_blood_group;

    public $member_since;
    public $membership_type;
    public $member_membership_no;
    public $wedding_anniversary;
    
    public $home_church;
    public $family_unit;
    public $tagCloud;


    public $business_addressline_1;
    public $business_addressline_2;
    public $business_postal_code;
    public $business_district;
    public $business_state;
    public $business_office_phone_number_1;
    public $business_office_phone_number_2;
    public $business_office_phone_1_country_code;
    public $business_office_phone_2_country_code;
    public $business_office_phone_1_area_code;
    public $business_office_phone_2_area_code;
    public $business_email;

    public $residence_addressline_1;
    public $residence_addressline_2;
    public $residence_postal_code;
    public $residence_district;
    public $residence_state;
    public $residence_landline;
    public $residence_phone_area_code;
    public $residence_phone_country_code;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [   'member_first_name', 
                    'member_last_name',
                    'member_title',
                    'member_mobile_number',
                    'membership_type',
                    'member_membership_no'
                ],
            'required'
            ],
            [
                'member_country_code','required','message' =>''
            ],
            [
                [   
                    'spouse_first_name', 
                    'spouse_last_name',
                    'spouse_title'
                ],
            'required',
            'when' => function($model){
                return ($model->spouse_mobile_number) ? true :false;
            },
            'whenClient' => "function (attribute, value) {
                    return $('#memberformmodel-spouse_mobile_number').val() != '';
            }"
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'member_photo' => 'Member photo',
            'member_title'=>'Member Title',
            'member_first_name'=> 'Member First Name',
            'member_email'=> 'Member Email',
            'member_last_name'=> 'Member Last Name',
            'member_middle_name'=> 'Member Middle Name',
            'member_nick_name'=> 'Member Nick Name',
            'member_number_private'=> 'Keep Number Private',
            'member_mobile_number'=> 'Member Mobile Number',
            'member_country_code'=>'test',
            'member_dob'=>'test',
            'member_occupation'=>'test',
            'member_blood_group'=>'test',
            'spouse_photo'=>'test',
            'spouse_title'=>'test',
            'spouse_first_name'=>'test',
            'spouse_email'=>'test',
            'spouse_last_name'=>'test',
            'spouse_middle_name'=>'test',
            'spouse_nick_name'=>'test',
            'spouse_number_private'=>'test',
            'spouse_mobile_number'=>'test',
            'spouse_country_code'=>'test',
            'spouse_dob'=>'test',
            'spouse_occupation'=>'test',
            'spouse_blood_group'=>'test',
            'member_since'=>'test',
            'membership_type'=>'test',
            'member_membership_no'=>'test',
            'wedding_anniversary'=>'test',
            'home_church'=>'test',
            'family_unit'=>'test',
            'tagCloud'=>'test',
            'business_addressline_1'=>'test',
            'business_addressline_2'=>'test',
            'business_postal_code'=>'test',
            'business_district'=>'test',
            'business_state'=>'test',
            'business_office_phone_number_1'=>'test',
            'business_office_phone_number_2'=>'test',
            'business_office_phone_1_country_code'=>'test',
            'business_office_phone_2_country_code'=>'test',
            'business_office_phone_1_area_code'=>'test',
            'business_office_phone_2_area_code'=>'test',
            'business_email'=>'test',
            'residence_addressline_1'=>'test',
            'residence_addressline_2'=>'test',
            'residence_postal_code'=>'test',
            'residence_district'=>'test',
            'residence_state'=>'test',
            'residence_landline'=>'test',
            'residence_phone_area_code'=>'test',
            'residence_phone_country_code'=>'test',
        ];
    }
    public function getTitles() 
    {
        return ArrayHelper::map(
            ExtendedTitle::find()
            ->select(['TitleId', 'Description'])
            ->where(['institutionid' => yii::$app->user->identity->institutionid])
            ->andWhere(['active' => 1])
            ->orderBy('Description')->all(),
            'TitleId',
            'Description'
        );
    }
}
