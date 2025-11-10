<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "country".
 *
 * @property int $countryid
 * @property string $CountryName
 * @property string $countrycode
 * @property string $telephonecode
 *
 * @property Affiliatedinstitution[] $affiliatedinstitutions
 * @property Institution[] $institutions
 * @property Member[] $members
 * @property Member[] $members0
 * @property Member[] $members1
 * @property Tempmember[] $tempmembers
 * @property Tempmember[] $tempmembers0
 * @property Tempmember[] $tempmembers1
 * @property Tempmember[] $tempmembers2
 * @property Tempmember[] $tempmembers3
 * @property Tempmember[] $tempmembers4
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['countryid', 'CountryName', 'countrycode', 'telephonecode'], 'required'],
            [['countryid'], 'integer'],
            [['CountryName'], 'string', 'max' => 200],
            [['countrycode'], 'string', 'max' => 45],
            [['telephonecode'], 'string', 'max' => 5],
            [['countryid'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'countryid' => 'Countryid',
            'CountryName' => 'Country Name',
            'countrycode' => 'Countrycode',
            'telephonecode' => 'Telephonecode',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAffiliatedinstitutions()
    {
        return $this->hasMany(Affiliatedinstitution::className(), ['CountryID' => 'countryid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutions()
    {
        return $this->hasMany(Institution::className(), ['countryid' => 'countryid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembers()
    {
        return $this->hasMany(Member::className(), ['countrycode' => 'countryid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembers0()
    {
        return $this->hasMany(Member::className(), ['member_mobile1_countrycode' => 'countryid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembers1()
    {
        return $this->hasMany(Member::className(), ['spouse_mobile1_countrycode' => 'countryid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmembers()
    {
        return $this->hasMany(Tempmember::className(), ['temp_member_business_phone2_countrycode' => 'countryid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmembers0()
    {
        return $this->hasMany(Tempmember::className(), ['temp_member_business_phone3_countrycode' => 'countryid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmembers1()
    {
        return $this->hasMany(Tempmember::className(), ['temp_member_business_phone1_countrycode' => 'countryid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmembers2()
    {
        return $this->hasMany(Tempmember::className(), ['temp_member_mobile1_countrycode' => 'countryid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmembers3()
    {
        return $this->hasMany(Tempmember::className(), ['temp_member_residence_Phone1_countrycode' => 'countryid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmembers4()
    {
        return $this->hasMany(Tempmember::className(), ['temp_spouse_mobile1_countrycode' => 'countryid']);
    }
}
