<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "affiliatedinstitution".
 *
 * @property int $affiliatedinstitutionid
 * @property int $institutionid
 * @property string $name
 * @property string $address1
 * @property string $address2
 * @property string $district
 * @property string $state
 * @property int $CountryID
 * @property string $pin
 * @property string $phone1_countrycode
 * @property string $phone1_areacode
 * @property string $phone1
 * @property string $location
 * @property string $mobilenocountrycode
 * @property string $phone2
 * @property string $email
 * @property int $active
 * @property int $createduser
 * @property int $modifieduser
 * @property string $phone3_countrycode
 * @property string $phone3_areacode
 * @property string $phone3
 * @property string $url
 * @property string $institutionlogo
 * @property string $presidentname
 * @property string $presidentmobile
 * @property string $presidentmobile_countrycode
 * @property string $secretaryname
 * @property string $secretarymobile
 * @property string $secretarymobile_countrycode
 * @property string $meetingvenue
 * @property string $meetingday
 * @property string $meetingtime
 * @property string $remarks
 *
 * @property Country $country
 * @property Usercredentials $createduser0
 * @property Institution $institution
 * @property Usercredentials $modifieduser0
 */
class Affiliatedinstitution extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'affiliatedinstitution';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['institutionid'], 'required'],
            [['institutionid', 'CountryID', 'createduser', 'modifieduser'], 'integer'],
            [['name', 'address1', 'address2', 'email', 'url'], 'string', 'max' => 100],
            [['district', 'state', 'pin', 'phone1', 'location', 'phone2', 'presidentname', 'secretaryname', 'meetingvenue', 'meetingday'], 'string', 'max' => 45],
            [['phone1_countrycode', 'mobilenocountrycode', 'phone3_countrycode', 'presidentmobile_countrycode', 'secretarymobile_countrycode'], 'string', 'max' => 5],
            [['phone1_areacode'], 'string', 'max' => 20],
            [['active', 'phone3_areacode'], 'string', 'max' => 4],
            [['phone3', 'presidentmobile', 'secretarymobile'], 'string', 'max' => 13],
            [['institutionlogo'], 'string', 'max' => 200],
            [['meetingtime'], 'string', 'max' => 15],
            [['remarks'], 'string', 'max' => 150],
            [['CountryID'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['CountryID' => 'countryid']],
            [['createduser'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createduser' => 'id']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['modifieduser'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['modifieduser' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'affiliatedinstitutionid' => 'Affiliatedinstitutionid',
            'institutionid' => 'Institutionid',
            'name' => 'Name',
            'address1' => 'Address1',
            'address2' => 'Address2',
            'district' => 'District',
            'state' => 'State',
            'CountryID' => 'Country',
            'pin' => 'Pin',
            'phone1_countrycode' => 'Phone1 Countrycode',
            'phone1_areacode' => 'Phone1 Areacode',
            'phone1' => 'Phone1',
            'location' => 'Location',
            'mobilenocountrycode' => 'Mobilenocountrycode',
            'phone2' => 'Phone2',
            'email' => 'Email',
            'active' => 'Active',
            'createduser' => 'Createduser',
            'modifieduser' => 'Modifieduser',
            'phone3_countrycode' => 'Phone3 Countrycode',
            'phone3_areacode' => 'Phone3 Areacode',
            'phone3' => 'Phone3',
            'url' => 'Url',
            'institutionlogo' => 'Institutionlogo',
            'presidentname' => 'Presidentname',
            'presidentmobile' => 'Presidentmobile',
            'presidentmobile_countrycode' => 'Presidentmobile Countrycode',
            'secretaryname' => 'Secretaryname',
            'secretarymobile' => 'Secretarymobile',
            'secretarymobile_countrycode' => 'Secretarymobile Countrycode',
            'meetingvenue' => 'Meetingvenue',
            'meetingday' => 'Meetingday',
            'meetingtime' => 'Meetingtime',
            'remarks' => 'Remarks',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['countryid' => 'CountryID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateduser0()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'createduser']);
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
    public function getModifieduser0()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'modifieduser']);
    }
}
