<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "tax".
 *
 * @property int $taxid
 * @property string $description
 * @property string $rate
 * @property int $institutionid
 * @property int $propertygroupid
 * @property int $isactive
 * @property int $createdby
 * @property string $createddatetime
 * @property int $modifiedby
 * @property string $modifieddatetime
 *
 * @property Ordertax[] $ordertaxes
 * @property Usercredentials $createdby0
 * @property Institution $institution
 * @property Usercredentials $modifiedby0
 * @property Propertygroup $propertygroup
 */
class Tax extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tax';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'rate', 'institutionid', 'propertygroupid', 'createdby', 'createddatetime'], 'required'],
            [['rate'], 'number'],
            [['institutionid', 'propertygroupid', 'createdby', 'modifiedby'], 'integer'],
            [['createddatetime', 'modifieddatetime'], 'safe'],
            [['description'], 'string', 'max' => 100],
            [['isactive'], 'string', 'max' => 4],
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
            'taxid' => 'Taxid',
            'description' => 'Description',
            'rate' => 'Rate',
            'institutionid' => 'Institutionid',
            'propertygroupid' => 'Propertygroupid',
            'isactive' => 'Isactive',
            'createdby' => 'Createdby',
            'createddatetime' => 'Createddatetime',
            'modifiedby' => 'Modifiedby',
            'modifieddatetime' => 'Modifieddatetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdertaxes()
    {
        return $this->hasMany(Ordertax::className(), ['taxid' => 'taxid']);
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
