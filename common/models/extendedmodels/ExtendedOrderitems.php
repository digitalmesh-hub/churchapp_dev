<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Orderitems;

/**
 * This is the model class for table "orderitems".
 *
 * @property int $orderitemsid
 * @property int $orderid
 * @property int $propertyid
 * @property string $price
 * @property int $quantity
 * @property int $createdby
 * @property string $createddatetime
 * @property int $modifiedby
 * @property string $modifieddatetime
 *
 * @property Usercredentials $createdby0
 * @property Usercredentials $modifiedby0
 * @property Orders $order
 * @property Property $property
 */
class ExtendedOrderitems extends Orderitems
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orderitems';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['orderid', 'propertyid', 'price', 'quantity', 'createdby', 'createddatetime'], 'required'],
            [['orderid', 'propertyid', 'quantity', 'createdby', 'modifiedby'], 'integer'],
            [['price'], 'number'],
            [['createddatetime', 'modifieddatetime'], 'safe'],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
            [['modifiedby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['modifiedby' => 'id']],
            [['orderid'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::className(), 'targetAttribute' => ['orderid' => 'orderid']],
            [['propertyid'], 'exist', 'skipOnError' => true, 'targetClass' => Property::className(), 'targetAttribute' => ['propertyid' => 'propertyid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'orderitemsid' => 'Orderitemsid',
            'orderid' => 'Orderid',
            'propertyid' => 'Propertyid',
            'price' => 'Price',
            'quantity' => 'Quantity',
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
    public function getModifiedby0()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'modifiedby']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Orders::className(), ['orderid' => 'orderid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperty()
    {
        return $this->hasOne(Property::className(), ['propertyid' => 'propertyid']);
    }
    /**
     * To Delete order items
     * @param unknown $memberUserId
     * @return boolean
     */
    public static function deleteOrderItems($memberUserId)
    {
    	try {
    		$deleteItem = Yii::$app->db->createCommand("delete from orderitems where createdby=:userid")
    						->bindValue(':userid', $memberUserId)
    						->execute();
    		return true;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
}
