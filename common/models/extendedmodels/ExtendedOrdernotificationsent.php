<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Ordernotificationsent;
use Exception;

/**
 * This is the model class for table "ordernotificationsent".
 *
 * @property int $ordernotificationid
 * @property int $orderid
 * @property int $orderstatus
 * @property int $memberid
 * @property string $membertype
 *
 * @property Member $member
 * @property Orders $order
 */
class ExtendedOrdernotificationsent extends Ordernotificationsent
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ordernotificationsent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['orderid', 'orderstatus', 'memberid', 'membertype'], 'required'],
            [['orderid', 'orderstatus', 'memberid'], 'integer'],
            [['membertype'], 'string', 'max' => 1],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
            [['orderid'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::className(), 'targetAttribute' => ['orderid' => 'orderid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ordernotificationid' => 'Ordernotificationid',
            'orderid' => 'Orderid',
            'orderstatus' => 'Orderstatus',
            'memberid' => 'Memberid',
            'membertype' => 'Membertype',
        ];
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
    public function getOrder()
    {
        return $this->hasOne(Orders::className(), ['orderid' => 'orderid']);
    }
    /**
     * to get order id by created user id
     * @param unknown $memberUserId
     * @return mixed
     */
    public static function getOrderIdByCreatedBy($memberUserId)
    {
    	try {
    		$orderDetails = Yii::$app->db->createCommand("select orderid from orders where createdby=:userid")
    						->bindValue(':userid', $memberUserId)
    						->queryAll();
    		return $orderDetails;
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * to delete order
     * @param unknown $orderId
     * @return boolean
     */
    public static function deleteOrderSentByOrderId($orderId)
    {
    	try {
    		$deleteOrder = Yii::$app->db->createCommand("delete from ordernotificationsent where orderid=:orderid")
    						->bindValue(':orderid', $orderId)
    						->execute();
    		return true;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    	}
    }
