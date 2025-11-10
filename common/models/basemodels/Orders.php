<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property int $orderid
 * @property int $memberid
 * @property string $membertype
 * @property int $institutionid
 * @property string $orderdate
 * @property string $ordertime
 * @property int $propertygroupid
 * @property int $orderstatus
 * @property string $note
 * @property int $createdby
 * @property string $createddatetime
 * @property int $modifiedby
 * @property string $modifieddatetime
 *
 * @property Orderitems[] $orderitems
 * @property Ordernotifications[] $ordernotifications
 * @property Ordernotificationsent[] $ordernotificationsents
 * @property Usercredentials $createdby0
 * @property Institution $institution
 * @property Member $member
 * @property Usercredentials $modifiedby0
 * @property Propertygroup $propertygroup
 * @property Ordertax[] $ordertaxes
 */
class Orders extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['memberid', 'membertype', 'institutionid', 'orderdate', 'ordertime', 'propertygroupid', 'orderstatus', 'createdby', 'createddatetime'], 'required'],
            [['memberid', 'institutionid', 'propertygroupid', 'orderstatus', 'createdby', 'modifiedby'], 'integer'],
            [['orderdate', 'createddatetime', 'modifieddatetime'], 'safe'],
            [['membertype'], 'string', 'max' => 1],
            [['ordertime'], 'string', 'max' => 15],
            [['note'], 'string', 'max' => 200],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
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
            'orderid' => 'Orderid',
            'memberid' => 'Memberid',
            'membertype' => 'Membertype',
            'institutionid' => 'Institutionid',
            'orderdate' => 'Orderdate',
            'ordertime' => 'Ordertime',
            'propertygroupid' => 'Propertygroupid',
            'orderstatus' => 'Orderstatus',
            'note' => 'Note',
            'createdby' => 'Createdby',
            'createddatetime' => 'Createddatetime',
            'modifiedby' => 'Modifiedby',
            'modifieddatetime' => 'Modifieddatetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderitems()
    {
        return $this->hasMany(Orderitems::className(), ['orderid' => 'orderid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdernotifications()
    {
        return $this->hasMany(Ordernotifications::className(), ['orderid' => 'orderid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdernotificationsents()
    {
        return $this->hasMany(Ordernotificationsent::className(), ['orderid' => 'orderid']);
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
    public function getPropertygroup()
    {
        return $this->hasOne(Propertygroup::className(), ['propertygroupid' => 'propertygroupid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdertaxes()
    {
        return $this->hasMany(Ordertax::className(), ['orderid' => 'orderid']);
    }
}
