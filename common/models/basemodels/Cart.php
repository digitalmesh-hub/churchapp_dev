<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "cart".
 *
 * @property int $cartid
 * @property int $propertyid
 * @property string $price
 * @property int $quantity
 * @property int $memberid
 * @property int $institutionid
 * @property int $propertygroupid
 * @property int $createdby
 * @property string $createddatetime
 * @property int $modifiedby
 * @property string $modifieddatetime
 *
 * @property Usercredentials $createdby0
 * @property Institution $institution
 * @property Member $member
 * @property Usercredentials $modifiedby0
 * @property Property $property
 * @property Propertygroup $propertygroup
 */
class Cart extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cart';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['propertyid', 'price', 'quantity', 'memberid', 'institutionid', 'propertygroupid', 'createdby', 'createddatetime'], 'required'],
            [['propertyid', 'quantity', 'memberid', 'institutionid', 'propertygroupid', 'createdby', 'modifiedby'], 'integer'],
            [['price'], 'number'],
            [['createddatetime', 'modifieddatetime'], 'safe'],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
            [['modifiedby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['modifiedby' => 'id']],
            [['propertyid'], 'exist', 'skipOnError' => true, 'targetClass' => Property::className(), 'targetAttribute' => ['propertyid' => 'propertyid']],
            [['propertygroupid'], 'exist', 'skipOnError' => true, 'targetClass' => Propertygroup::className(), 'targetAttribute' => ['propertygroupid' => 'propertygroupid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cartid' => 'Cartid',
            'propertyid' => 'Propertyid',
            'price' => 'Price',
            'quantity' => 'Quantity',
            'memberid' => 'Memberid',
            'institutionid' => 'Institutionid',
            'propertygroupid' => 'Propertygroupid',
            'createdby' => 'Createdby',
            'createddatetime' => 'Createddatetime',
            'modifiedby' => 'Modifiedby',
            'modifieddatetime' => 'Modifieddatetime',
        ];
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
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'memberid']);
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
    public function getProperty()
    {
        return $this->hasOne(Property::className(), ['propertyid' => 'propertyid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertygroup()
    {
        return $this->hasOne(Propertygroup::className(), ['propertygroupid' => 'propertygroupid']);
    }
}
