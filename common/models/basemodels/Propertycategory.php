<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "propertycategory".
 *
 * @property int $propertycategoryid
 * @property string $category
 * @property int $institutionid
 * @property int $propertygroupid
 * @property int $active
 * @property int $createdby
 * @property string $createddatetime
 * @property int $modifiedby
 * @property string $modifieddatetime
 *
 * @property Property[] $properties
 * @property Usercredentials $createdby0
 * @property Institution $institution
 * @property Usercredentials $modifiedby0
 * @property Propertygroup $propertygroup
 */
class Propertycategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'propertycategory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category', 'institutionid', 'propertygroupid', 'createdby', 'createddatetime'], 'required'],
            [['institutionid', 'propertygroupid', 'createdby', 'modifiedby'], 'integer'],
            [['createddatetime', 'modifieddatetime'], 'safe'],
            [['category'], 'string', 'max' => 100],
            [['active'], 'string', 'max' => 4],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['modifiedby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['modifiedby' => 'id']],
            [['propertygroupid'], 'exist', 'skipOnError' => true, 'targetClass' => Propertygroup::className(), 'targetAttribute' => ['propertygroupid' => 'propertygroupid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'propertycategoryid' => 'Propertycategoryid',
            'category' => 'Category',
            'institutionid' => 'Institutionid',
            'propertygroupid' => 'Propertygroupid',
            'active' => 'Active',
            'createdby' => 'Createdby',
            'createddatetime' => 'Createddatetime',
            'modifiedby' => 'Modifiedby',
            'modifieddatetime' => 'Modifieddatetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperties()
    {
        return $this->hasMany(Property::className(), ['propertycategoryid' => 'propertycategoryid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedby0()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'createdby']);
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
    public function getModifiedby0()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'modifiedby']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertygroup()
    {
        return $this->hasOne(Propertygroup::className(), ['propertygroupid' => 'propertygroupid']);
    }
}
