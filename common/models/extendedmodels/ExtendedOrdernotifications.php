<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Ordernotifications;

/**
 * This is the model class for table "ordernotifications".
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
class ExtendedOrdernotifications extends Ordernotifications
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ordernotifications';
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
    * To Send food booking notification
    * @param unknown $orderId
    * @return \yii\db\false|boolean
    */
    public static function sendNotifications($orderId)
    {
    	try {
    		$orderDetails = Yii::$app->db->createCommand("
    				CALL getorderdetails(:orderid)")
    				->bindValue(':orderid', $orderId)
    				->queryOne();
    		return $orderDetails;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To delete order notification
     * @param unknown $orderId
     * @return boolean
     */
    public static function deleteOrderNotificationsByOrderId($orderId)
    {
    	try {
    		$delteNotification = Yii::$app->db->createCommand("delete from ordernotifications where orderid=:orderid")
    							->bindValue(':orderid', $orderId)
    							->execute();
    		return true;
    							
    	} catch (Exception $e) {
    		return false;
    	}
    }
}
