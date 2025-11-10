<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Orders;
use function foo\func;

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
class ExtendedOrders extends Orders
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
            [['memberid', 'membertype', 'institutionid', 'orderdate', 'ordertime', 'propertygroupid', 'createdby', 'createddatetime'], 'required'],
            [['memberid', 'institutionid', 'propertygroupid', 'orderstatus', 'createdby', 'modifiedby'], 'integer'],
            [['orderdate', 'createddatetime', 'modifieddatetime'], 'safe'],
            [['membertype'], 'string', 'max' => 1],
            [['ordertime'], 'string', 'max' => 15],
            [['note'], 'string', 'max' => 200],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedUsercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedInstitution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedMember::className(), 'targetAttribute' => ['memberid' => 'memberid']],
            [['modifiedby'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedUsercredentials::className(), 'targetAttribute' => ['modifiedby' => 'id']],
            [['propertygroupid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedPropertygroup::className(), 'targetAttribute' => ['propertygroupid' => 'propertygroupid']],
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
        return $this->hasMany(ExtendedOrderitems::className(), ['orderid' => 'orderid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdernotifications()
    {
        return $this->hasMany(ExtendedOrdernotifications::className(), ['orderid' => 'orderid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdernotificationsents()
    {
        return $this->hasMany(ExtendedOrdernotificationsent::className(), ['orderid' => 'orderid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedby0()
    {
        return $this->hasOne(ExtendedUsercredentials::className(), ['id' => 'createdby']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(ExtendedInstitution::className(), ['id' => 'institutionid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(ExtendedMember::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModifiedby0()
    {
        return $this->hasOne(ExtendedUsercredentials::className(), ['id' => 'modifiedby']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertygroup()
    {
        return $this->hasOne(ExtendedPropertygroup::className(), ['propertygroupid' => 'propertygroupid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdertaxes()
    {
        return $this->hasMany(ExtendedOrdertax::className(), ['orderid' => 'orderid']);
    }
   /**
    * to get the order count
    * @param unknown $userId
    * @param unknown $foodId
    * @param unknown $propertyId
    * @param unknown $currentDate
    * @return string|NULL|\yii\db\false|boolean
    */
    public static function getOrdersCount($userId,$foodId,$propertyId,$currentDate)
    { 
      $institutionId = Yii::$app->user->identity->institutionid;
    	try {
    		$ordersCount= Yii::$app->db->createCommand(
    				"CALL get_order_count(:userid,:propertyid,:privilegeid,:currentdate,:institutionid)")
    				->bindValue(':userid', $userId)
    				->bindValue(':propertyid', $propertyId)
    				->bindValue(':privilegeid', $foodId)
    				->bindValue(':currentdate', $currentDate)
            ->bindValue(':institutionid',$institutionId)
    				->queryScalar();
    		return $ordersCount;
    	} catch (Exception $e) {
    		return false;
    	}
   }
   /**
    * To get the order details
    * @param unknown $propertyGroupId
    * @param unknown $orderId
    * @return boolean
    */
   public static function getOrderDetails($propertyGroupId,$orderId)
   {
   	try {
   		$orderData = Yii::$app->db->createCommand("
   				CALL getorderamount(:propertygroupid,:orderid)")
   				->bindValue(':propertygroupid', $propertyGroupId)
   				->bindValue(':orderid', $orderId)
   				->queryAll();
   		return $orderData;
   	} catch (Exception $e) {
   		return false;
   	}
   }
 	/**
 	 * To get the user details of an order
 	 * @param unknown $orderId
 	 * @param unknown $memberId
 	 * @return \yii\db\false|boolean
 	 */
   public static function getUserDetailsOfOrder($orderId,$memberId)
   {
   	try {
   		$userDetails = Yii::$app->db->createCommand("select o.orderstatus,o.memberid,o.orderdate,o.ordertime,m.firstName,
						m.middleName,m.lastName from orders o 
						INNER join member m on o.memberid = m.memberid 
						where m.memberid = :memberid and o.orderid = :orderid")
						->bindValue(':memberid', $memberId)
						->bindValue(':orderid', $orderId)
						->queryOne();
   		return $userDetails;
   		
   	} catch (Exception $e) {
   		return false;
   	}
   }
   /**
    * To Get tax rate of order
    * @param unknown $orderId
    * @return boolean
    */
   public static function getTaxRate($orderId)
   {
   	try {
   		$taxRate = Yii::$app->db->createCommand("select taxrate from ordertax where orderid = :orderid")
   					->bindValue(':orderid', $orderId)
   					->queryOne();
   	} catch (Exception $e) {
   		return false;
   	}
   }
   /**
    * To get the order status
    * @param unknown $orderId
    * @return \yii\db\false|boolean
    */
   public static function getOrderStatus($orderId)
   {
   	try {
   		$orderStatus = Yii::$app->db->createCommand("select orderid,orderstatus from orders where orderid = :orderid")
   					->bindValue(':orderid', $orderId)
   					->queryOne();
   		return $orderStatus;
   		
   	} catch (Exception $e) {
   		return false;
   	}
   }
   /**
    * To reject an order
    * @param unknown $orderId
    * @param unknown $rejectStatus
    * @param unknown $note
    * @param unknown $modifiedBy
    * @param unknown $modifiedDate
    * @return boolean
    */
   public static function rejectOrder($orderId,$rejectStatus,$note,$modifiedBy,$modifiedDate)
   {
   	try {
   		$rejectOrder = Yii::$app->db->createCommand("update orders set orderstatus=:orderstatus,note=:note,modifiedby=:modifiedby,
   				modifieddatetime=:modifieddatetime where orderid=:orderid")
   				->bindValue(':orderstatus', $rejectStatus)
				->bindValue(':note', $note)   		
				->bindValue(':modifiedby', $modifiedBy)
				->bindValue(':modifieddatetime', $modifiedDate)
				->bindValue(':orderid', $orderId)
				->execute();
   		return true;
   	} catch (Exception $e) {
   		return false;
   	}
   }
   /**
    * To update order status
    * @param unknown $orderStatus
    * @param unknown $modifiedBy
    * @param unknown $modifiedDate
    * @param unknown $orderId
    * @return boolean
    */
   public static function updateToReady($orderStatus,$modifiedBy,$modifiedDate,$orderId)
   {
   	try {
   		$updateStatus = Yii::$app->db->createCommand("update orders set orderstatus=:orderstatus,modifiedby=:modifiedby,
   				modifieddatetime=:modifieddatetime where orderid=:orderid")
   				->bindValue(':orderstatus', $orderStatus)
				->bindValue(':modifiedby', $modifiedBy)
				->bindValue(':modifieddatetime', $modifiedDate)
				->bindValue(':orderid', $orderId)
				->execute();
   		return true;
   		
   	} catch (Exception $e) {
   		return false;
   	}
   }
   /**
    * To get the device details
    * @param unknown $institutionId
    */
   public static function getOrderNotificationDevices($institutionId){
   	
   	   try {
   	   $deviceDetails = Yii::$app->db->createCommand(
    				"CALL getordernotifications(:institutionId)")
    				->bindValue(':institutionId', $institutionId)
    				->queryAll();
    		return $deviceDetails;
    	} catch (Exception $e) {
    		return false;
    	}
   	
   }
   /**
    * To get the device details
    * @param unknown $institutionId
    */
   public static function getOrderData($orderId){
   
   	try {
   		$deviceDetails = Yii::$app->db->createCommand(
   				"CALL getorderdetails(:orderId)")
   				->bindValue(':orderId', $orderId)
   				->queryAll();
   		return $deviceDetails;
   	} catch (Exception $e) {
   		return false;
   	}
   }
   
  /**
   *  To get the device list to sent the order notification sent
   * @param unknown $institutionId
   * @param unknown $privilegeId
   * @return boolean
   */
   public static function getOrderNotificationDeviceList($institutionId,$privilegeId){
   	try {
   		$deviceDetails = Yii::$app->db->createCommand(
   				"CALL get_managers_deviceid(:institutionId,:privilegeId)")
   				->bindValue(':institutionId', $institutionId)
   				->bindValue(':privilegeId', $privilegeId)
   				->queryAll();
   		return $deviceDetails;
   	} catch (Exception $e) {
   		return false;
   	}
   	
   }
   /**
    * To delete an order
    * @param unknown $memberUserId
    * @return boolean
    */
   public static function deleteOrder($memberUserId)
   {
   	try {
   		$deleteOrder = Yii::$app->db->createCommand("delete from orders where createdby=:userid")
   						->bindValue(':userid', $memberUserId)
   						->execute();
   		return true;
   	} catch (Exception $e) {
   		return false;
   	}
   }
}
