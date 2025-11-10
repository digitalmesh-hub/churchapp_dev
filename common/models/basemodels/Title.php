<?php

namespace common\models\basemodels;

use Yii;
use common\models\basemodels\Institution;

/**
 * This is the model class for table "title".
 *
 * @property int $TitleId
 * @property string $Description
 * @property int $institutionid
 * @property int $active
 *
 * @property DeleteMember[] $deleteMembers
 * @property DeleteMember[] $deleteMembers0
 * @property Dependant[] $dependants
 * @property Member[] $members
 * @property Member[] $members0
 * @property Tempdependant[] $tempdependants
 * @property Tempdependantmail[] $tempdependantmails
 * @property Tempmembermail[] $tempmembermails
 * @property Tempmembermail[] $tempmembermails0
 * @property Institution $institution
 */
class Title extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'title';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['institutionid'], 'integer'],
            [['Description'], 'string', 'max' => 75],
            [['active'], 'string', 'max' => 4],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['Description'],'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'TitleId' => 'Title ID',
            'Description' => 'Title',
            'institutionid' => 'Institutionid',
            'active' => 'Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeleteMembers()
    {
        return $this->hasMany(DeleteMember::className(), ['membertitle' => 'TitleId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeleteMembers0()
    {
        return $this->hasMany(DeleteMember::className(), ['spousetitle' => 'TitleId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDependants()
    {
        return $this->hasMany(Dependant::className(), ['titleid' => 'TitleId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembers()
    {
        return $this->hasMany(Member::className(), ['membertitle' => 'TitleId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembers0()
    {
        return $this->hasMany(Member::className(), ['spousetitle' => 'TitleId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempdependants()
    {
        return $this->hasMany(Tempdependant::className(), ['titleid' => 'TitleId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempdependantmails()
    {
        return $this->hasMany(Tempdependantmail::className(), ['titleid' => 'TitleId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmembermails()
    {
        return $this->hasMany(Tempmembermail::className(), ['temp_membertitle' => 'TitleId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmembermails0()
    {
        return $this->hasMany(Tempmembermail::className(), ['temp_spousetitle' => 'TitleId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionid']);
    }
}
