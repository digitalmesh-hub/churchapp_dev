<?php
namespace api\modules\v3\models;

use yii;
use yii\base\Model;
use yii\base\ErrorException;
use common\models\extendedmodels\ExtendedPrivilege;
use common\models\extendedmodels\ExtendedOrders;
use common\models\extendedmodels\ExtendedOrderstatus;
use common\models\extendedmodels\ExtendedDevicedetails;
use Exception;

class FoodManager extends Model
{
    /**
     * Cart count
     * @param $memberId int
     * @param $propertyGroupId int
     * @return $cartCount array
    */
    public function getCartCount($memberId, $propertyGroupId){
        try {
            $cartCount = Yii::$app->db->createCommand("SELECT count(cartid) FROM cart  WHERE memberid= :memberid AND propertygroupid= :propertygroupid GROUP BY memberid,propertygroupid")
                ->bindValue(':memberid', $memberId)
                ->bindValue(':propertygroupid', $propertyGroupId)
                ->queryScalar();
            if (!empty($cartCount)) {
                return (int) $cartCount;
            } else{
                return true;
            }
        } catch (Exception $e) {
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Get property deatils
     * @param $propertyId int
     * @return $propertyList array
    */
    public function getPropertyItem($propertyId){
        
        try{
            $propertyList =  Yii::$app->db->createCommand("SELECT propertyid, property,propertycategoryid,description,price,
                thumbnailimage,institutionid FROM property 
                WHERE propertyid= :propertyid")
                ->bindValue(':propertyid', $propertyId)
                ->queryOne();
            if(!empty($propertyList)){
                return $propertyList;
            }
            else{
                return true;
            }
        }
        catch (ErrorException $e) {
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Get property images
     * @param $propertyId int
     * @return $propertyList array
    */
    public function getPropertyImages($propertyId){
        try{
            $propertyImages =  Yii::$app->db->createCommand("SELECT propertyimageid,imageurl,propertyid,imageorder FROM propertyimages WHERE propertyid= :propertyid ORDER BY imageorder")
                ->bindValue(':propertyid', $propertyId)
                ->queryAll();
            if(!empty($propertyImages)){
                return $propertyImages;
            }
            else{
                return true;
            }
        }
        catch (ErrorException $e) {
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Add/Edit cart items
     * @param $cartObject array
     * @return $propertyList array
    */
    public function addEditCartItem($cartObject){
        try{
            if ($cartObject['cartId'] != 0)
            {
                if($cartObject['isRemove'])
                {
                    $this->removeCart($cartObject['cartId']);
                }
                else
                {
                    $this->updateCart($cartObject);
                }
            }
            else{
                $cartItems = $this->getCartItems($cartObject);
                if(!empty($cartItems)){
                    if($cartObject['isRemove'])
                    {
                        $this->removeCartItem($cartObject);
                    }
                    else
                    {
                        $cartObject['cartId'] = $cartItems['cartid'];
                        $cartObject['quantity'] = $cartObject['quantity'] + $cartItems['quantity'];
                        $this->updateCart($cartObject);
                    }
                }
                else{
                    $propertyDetails = $this->getPropertyDetails($cartObject['itemId']);
                    if(!empty($propertyDetails)){
                        $cartObject['price'] = $propertyDetails['price'];
                    }
                    if(!$cartObject['isRemove']){
                        $this->addCart($cartObject);
                    }
                }
            }
            return true;
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    } 

    /**
     * Remove cart
     * @param $cartId int
     * @return boolean
    */
    protected function removeCart($cartId){
        try{
            $propertyImages =  Yii::$app->db->createCommand("DELETE from cart WHERE cartid = :cartid")
                ->bindValue(':cartid', $cartId)
                ->execute();
            return true;
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Update cart item
     * @param $cartId int
     * @return boolean
    */
    protected function updateCart($cartObject){
        try{
            $cart =  Yii::$app->db->createCommand("UPDATE cart SET propertyid = :propertyid, quantity = :quantity,
                memberid = :memberid,institutionid = :institutionid,
                propertygroupid = :propertygroupid, modifiedby = :modifiedby,
                modifieddatetime = :modifieddatetime WHERE cartid = :cartid")
                ->bindValue(':propertyid', $cartObject['itemId'])
                ->bindValue(':quantity', $cartObject['quantity'])
                ->bindValue(':memberid', $cartObject['memberId'])
                ->bindValue(':institutionid', $cartObject['institutionId'])
                ->bindValue(':propertygroupid', $cartObject['propertyGroupId'])
                ->bindValue(':modifiedby', $cartObject['userId'])
                ->bindValue(':modifieddatetime', $cartObject['currentDateTime'])
                ->bindValue(':cartid', $cartObject['cartId'])
                ->execute();
            return true;
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    } 

     /**
     * Get cart item
     * @param $cartId int
     * @return $cartItems array
    */
    protected function getCartItems($cartObject){
        try{
            $cartItems =  Yii::$app->db->createCommand("SELECT * FROM cart WHERE propertyid = :propertyid AND memberid = :memberid AND institutionid= :institutionid AND propertygroupid =:propertygroupid")
                ->bindValue(':propertyid', $cartObject['itemId'])
                ->bindValue(':memberid', $cartObject['memberId'])
                ->bindValue(':institutionid', $cartObject['institutionId'])
                ->bindValue(':propertygroupid', $cartObject['propertyGroupId'])
                ->queryOne();
            
            return $cartItems;
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    } 

    /**
     * Remove cart item
     * @param $cartId int
     * @return boolean
    */
    protected function removeCartItem($cartObject){
        try{
            $propertyImages =  Yii::$app->db->createCommand("DELETE FROM cart WHERE propertyid = :propertyid AND memberid = :memberid AND institutionid = :institutionid AND propertygroupid = :propertygroupid")
                ->bindValue(':propertyid', $cartObject['itemId'])
                ->bindValue(':memberid', $cartObject['memberId'])
                ->bindValue(':institutionid', $cartObject['institutionId'])
                ->bindValue(':propertygroupid', $cartObject['propertyGroupId'])
            ->execute();
            return true;
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    }


    /**
     * Add cart item
     * @param $cartObject array
     * @return boolean
    */
    protected function addCart($cartObject){
        try{
            $cart =  Yii::$app->db->createCommand("INSERT INTO cart(propertyid,
                price,quantity,memberid,institutionid,propertygroupid,createdby,
                createddatetime) VALUES (:propertyid, :price, :quantity,
                :memberid, :institutionid, :propertygroupid,:createdby,
                :createddatetime)")
                ->bindValue(':propertyid', $cartObject['itemId'])
                ->bindValue(':price', $cartObject['price'])
                ->bindValue(':quantity', $cartObject['quantity'])
                ->bindValue(':memberid', $cartObject['memberId'])
                ->bindValue(':institutionid', $cartObject['institutionId'])
                ->bindValue(':propertygroupid', $cartObject['propertyGroupId'])
                ->bindValue(':createdby', $cartObject['userId'])
                ->bindValue(':createddatetime', $cartObject['currentDateTime'])
                ->execute();
            return true;
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Get property details
     * @param $cartObject array
     * @return $propertyItem array
    */
    protected function getPropertyDetails($propertyId){
        try{
            $propertyItem =  Yii::$app->db->createCommand("SELECT propertyid, property,propertycategoryid,description,price,thumbnailimage,
                institutionid FROM property WHERE propertyid = :propertyid")
                ->bindValue(':propertyid', $propertyId)
                ->queryOne();
            
            return $propertyItem;
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    } 

    /**
     * Get items in cart
     * @param $memberId int
     * @param $institutionId int
     * @param $propertyGroupId int
     * @return $propertyItem array
    */
    public function getItemsInCart($memberId, $institutionId, $propertyGroupId){
        try {
            $cartItems = Yii::$app->db->createCommand("
                CALL get_cart_items(:memberid, :institutionid,  :propertygroupid)")
                ->bindValue(':memberid', $memberId)
                ->bindValue(':institutionid', $institutionId)
                ->bindValue(':propertygroupid', $propertyGroupId)  
                ->queryAll();
            if(!empty($cartItems)){
                return $cartItems;
            }
            else{
                return true;
            }
        } catch (Exception $e) {
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Get tax items
     * @param $institutionId int
     * @param $propertyGroupId int
     * @param $isActive boolean
     * @return $propertyItem array
    */
    public function getAllActivePropertyGroupTaxes($institutionId, $propertyGroupId, $isActive)
    {
        try{
            $taxItems =  Yii::$app->db->createCommand("SELECT taxid,
                description,rate,institutionid,propertygroupid,
                isactive FROM tax 
                WHERE institutionid = :institutionid 
                AND propertygroupid = :propertygroupid
                AND (isactive = :isactive OR :isactive is null)")
                ->bindValue(':institutionid', $institutionId)
                ->bindValue(':propertygroupid', $propertyGroupId)
                ->bindValue(':isactive', $isActive)
                ->queryAll();
            if(!empty($taxItems)){
                return $taxItems;
            }
            else{
                return true;
            }
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    }

     /**
     * Add food order
     * @param $orderList array
     * @return $id array
    */
    public function addOrder($orderList){
        
        try{
            $order =  Yii::$app->db->createCommand("INSERT INTO orders(memberid,
                membertype,institutionid,orderdate,ordertime,
                propertygroupid,orderstatus,createdby,
                createddatetime)
                VALUES (:memberid, :membertype, :institutionid, :orderdate,
                :ordertime, :propertygroupid, :orderstatus, :createdby,
                :createddatetime)")
                ->bindValue(':memberid', $orderList['memberId'])
                ->bindValue(':membertype', $orderList['userType'])
                ->bindValue(':institutionid', $orderList['institutionId'])
                ->bindValue(':orderdate', $orderList['preferredDate'])
                ->bindValue(':ordertime', $orderList['preferredTime'])
                ->bindValue(':propertygroupid', $orderList['propertyGroupId'])
                ->bindValue(':orderstatus', $orderList['orderStatus'])
                ->bindValue(':createdby', $orderList['userId'])
                ->bindValue(':createddatetime', $orderList['currentDateTime'])
                ->execute();
            $orderId = Yii::$app->db->getLastInsertID();
            return $orderId;
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    }
    
    /**
     * Add order items
     * @param $itemList array
     * @return boolean 
    */
    public function addOrderItems($itemList){
        try{
            $order = Yii::$app->db->createCommand()->batchInsert('orderitems', ['orderid','propertyid','price','quantity','createdby',
                'createddatetime'], $itemList)
            ->execute();
            return true;
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Add order taxes
     * @param $taxList array
     * @return boolean
    */
    public function addOrderTaxes($taxList){
        
        try{
            $order = Yii::$app->db->createCommand()->batchInsert('ordertax', ['orderid','taxid','taxrate'], $taxList)
            ->execute();
            return true;
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Get previous orders
     * @param $memberId int
     * @param $institutionId int
     * @param $propertyGroupId int
     * @return $previousOrders array
    */
    public function getPreviousOrders($memberId, $institutionId, $propertyGroupId){
        try{
            $previousOrders = Yii::$app->db->createCommand("
                CALL get_previous_orders(:memberid, :institutionid,  :propertygroupid)")
                ->bindValue(':memberid', $memberId)
                ->bindValue(':institutionid', $institutionId)
                ->bindValue(':propertygroupid', $propertyGroupId)  
                ->queryAll();
            if(!empty($previousOrders)){
                return $previousOrders;
            }
            else{
                return true;
            }
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Get order tax details
     * @param $memberId int
     * @param $institutionId int
     * @param $propertyGroupId int
     * @return $previousOrders array
    */
    public function getOrderTaxDetails($orderId){
        try{
            $orderTaxes = Yii::$app->db->createCommand("
                CALL get_order_taxes(:orderid)")
                ->bindValue(':orderid', $orderId)  
                ->queryAll();
            if(!empty($orderTaxes)){
                return $orderTaxes;
            }
            else{
                return true;
            }
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Get food orders
     * @param $memberId int
     * @param $institutionId int
     * @param $propertyGroupId int
     * @return $previousOrders array
    */
    public function getFoodOrders($userId, $propertyGroupId, $privilegeId, $currentDateTime)
    {
        $institutionId = Yii::$app->user->identity->institutionid;
        try{
            $foodOrders = Yii::$app->db->createCommand("
                CALL get_orders(:userid, :propertygroupid, :privilegeid , :currentdate, :institutionid)")
                ->bindValue(':userid', $userId)
                ->bindValue(':propertygroupid', $propertyGroupId)  
                ->bindValue(':privilegeid', $privilegeId)
                ->bindValue(':currentdate', $currentDateTime) 
                ->bindValue(':institutionid',$institutionId)   
                ->queryAll();
            if(!empty($foodOrders)){
                return $foodOrders;
            }
            else{
                return true;
            }
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Get order details
     * @param $orderId int
     * @param $propertyGroupId int
     * @return $previousOrders array
    */
    public function getOrderDetails($orderId){
        try{
            $orderResult = Yii::$app->db->createCommand("
                CALL get_order_details(:orderid)")
                ->bindValue(':orderid', $orderId)  
                ->queryOne();
            if (!empty($orderResult)) {
                return $orderResult;
            } else {
                return true;
            }
        } catch(Exception $e){
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Get order item details
     * @param $orderId int
     * @param $propertyGroupId int
     * @return $previousOrders array
    */
    public function getOrderItemDetails($orderId){
        try{
            $itemResult = Yii::$app->db->createCommand("
                CALL get_orderitems_detail(:orderid)")
                ->bindValue(':orderid', $orderId)  
                ->queryAll();
            if(!empty($itemResult)){
                return $itemResult;
            }
            else{
                return true;
            }
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Get order status
     * @param $orderId int
     * @return $orderStatus array
    */
    public function getOrderStatus($orderId){
        try{
            $orderStatus = Yii::$app->db->createCommand("
                SELECT orderstatus FROM orders WHERE orderid= :orderid")
                ->bindValue(':orderid', $orderId)  
                ->queryOne();
            if(!empty($orderStatus)){
                return $orderStatus;
            }
            else{
                return true;
            }
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Update order status
     * @param $orderId int
     * @return $orderStatus array
    */
    public function updateOrderStatus($orderId,$orderStatus,$reasonForRejection,$userId,$currentDateTime){
        
        try{
            Yii::$app->db->createCommand("
                UPDATE orders SET orderstatus = :orderstatus, note = :note, modifiedby = :modifiedby, modifieddatetime = :modifieddatetime WHERE orderid = :orderid")
                ->bindValue(':orderstatus', $orderStatus) 
                ->bindValue(':note', $reasonForRejection)
                ->bindValue(':modifiedby', $userId) 
                ->bindValue(':modifieddatetime', $currentDateTime)  
                ->bindValue(':orderid', $orderId)
                ->execute();
            return true;
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Remove from cart
     * @param $memberId int
     * @param $propertyGroupId int
     * @param $institutionId int
     * @return boolean value
    */
    public function removeOrderFromCart($memberId, $propertyGroupId, $institutionId){
        
        try{
            Yii::$app->db->createCommand("DELETE FROM cart 
                    WHERE memberid = :memberid AND institutionid =:institutionid 
                    AND propertygroupid = :propertygroupid")
                ->bindValue(':memberid', $memberId) 
                ->bindValue(':institutionid', $institutionId)
                ->bindValue(':propertygroupid', $propertyGroupId) 
                ->execute();
            return true;
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Order notification
     * @param $memberId int
     * @param $propertyGroupId int
     * @param $institutionId int
     * @return boolean value
    */
    public function sentOrderNotification($orderId, $orderStatus, $foodOrderPrivilegeId,$institutionId,$foodType)
    {
        
        try{
            $memberResponse = $this->getOrderDetailsForNotification($orderId);
            $deviceDetailsModel = new ExtendedDevicedetails();
            $deviceDetails = $deviceDetailsModel->getDeviceDetails($institutionId, $foodOrderPrivilegeId);
            $pushNotificationSender = Yii::$app->PushNotificationHandler;
            $notificationHandler = Yii::$app->NotificationHandler;
            if($orderStatus == ExtendedOrderstatus::CANCELLED || $orderStatus == ExtendedOrderstatus::PLACED) {
            	$fields    = ['orderid','orderstatus','memberid','membertype'];
            	$bachInsert = [];
            	$notificationSent =[];
            	foreach ($deviceDetails as $deviceDetail){
            		$userId = $deviceDetail[ 'id'];
            		$deviceid 	= 	$deviceDetail['deviceid'];
            		$devicetype =   $deviceDetail['devicetype'];
            		$memberId	=   $deviceDetail['memberid'];
            		$usertype	=  $deviceDetail['membertype'];
            		$institutionId = $deviceDetail['institutionid'];
            		$institutionName =	$deviceDetail ['institutionname'];
            		$membernotification = $deviceDetail['membernotification'];
            		$spousenotification = $deviceDetail['spousenotification'];
            		 
            		$sentMemberId = null;
            		if (strtolower($usertype) == "m" && $membernotification == 1)
            		{
            			$sentMemberId = $deviceid;
            		}
            		if (strtolower($usertype) == "s" && $spousenotification == 1)
            		{
            			$sentMemberId = $deviceid;
            		}
            		
            		if ($sentMemberId){
            			$bachInsert[] = [$orderId,$orderStatus,$memberId,$usertype];
            			 
            			$memberName = $memberResponse['membername'];
            			
            			if($orderStatus == ExtendedOrderstatus::CANCELLED)
            			{
            				$message    = $memberName . " has cancelled the food order";
            			} 
            			if($orderStatus == ExtendedOrderstatus::PLACED)
            			{
            				$message    = $memberName . " has placed a food order";
            			}
            			if($foodType ==1)
            			{
            				$notificationType = 'food-update';
            			}
            			if($foodType == 2)
            			{
            				$notificationType = 'food-request';
            			}
            			
            			$requestData  = $pushNotificationSender->setPushNotificationRequest($sentMemberId,$message,$notificationType,$institutionId,$orderId,$institutionName,strtolower($devicetype),$userId);
            			$response     = $pushNotificationSender->sendNotification(strtolower($devicetype), $sentMemberId, $requestData);
            			 
            			if($response){
            				$notificationSent[] = [$orderId,$orderStatus,$memberId,$usertype];
            			}
            		}
            		
            	}
            	if (count($bachInsert)>0){
            		 
            		Yii::$app->db->createCommand()->batchInsert('ordernotifications', $fields,$bachInsert )->execute();
            		 
            	}
            	
            	if (count($notificationSent)>0){
            		 
            		Yii::$app->db->createCommand()->batchInsert('ordernotificationsent', $fields,$notificationSent )->execute();
            		 
            	}
            } else {
            	//other status
            	$fields    = ['orderid','orderstatus','memberid','membertype'];
            	$bachInsert = [];
            	$notificationSent =[];
            	$memberDeviceDetails = $this->getMemberDeviceDetails($orderId);
            	if($memberDeviceDetails && is_array($memberDeviceDetails)) {
            		$memberId = $memberDeviceDetails['memberid'];
            		$usertype = $memberDeviceDetails['membertype'];
                    $membernotification = $memberDeviceDetails['membernotification'];
                    $spousenotification = $memberDeviceDetails['spousenotification'];
                    $deviceid   =   $memberDeviceDetails['deviceid'];
                    $devicetype =   $memberDeviceDetails['devicetype'];
                    $institutionId = $memberDeviceDetails['institutionid'];
                    $institutionName =  $memberDeviceDetails ['institutionname'];
                    $userId = $memberDeviceDetails[ 'id'];
            		
            		$sentMemberId = null;
            		if (strtolower($usertype) == "m" && $membernotification == 1) {
            			$sentMemberId = $deviceid;
            		}
            		if (strtolower($usertype) == "s" && $spousenotification == 1) {
            			$sentMemberId = $deviceid;
            		}

            		if($sentMemberId) {
            			$bachInsert[] = [$orderId,$orderStatus,$memberId,$usertype];
            			$institutionName = $memberDeviceDetails['institutionname'];
            			if($orderStatus == ExtendedOrderstatus::READY) {
            				$message = "Your food order from " . $institutionName . " is ready";
            			} elseif ($orderStatus == ExtendedOrderstatus::CONFIRMED) {
            				$message = "Your order has been confirmed by " . $institutionName ;
            			} elseif($orderStatus == ExtendedOrderstatus::REJECTED) {
            				$message = "Your order has been rejected by" . $institutionName ;
            			} elseif ($orderStatus == ExtendedOrderstatus::HANDOVER) {
            				$message = "Your food order has been delivered by" . $institutionName ;
            			}
            			if($foodType ==1) {
            				$notificationType = 'food-update';
            			}
            			if($foodType == 2) {
            				$notificationType = 'food-request';
            			}
            			$requestData  = $pushNotificationSender->setPushNotificationRequest($sentMemberId,$message,$notificationType,$institutionId,$orderId,$institutionName,strtolower($devicetype),$memberDeviceDetails['id']);
            			$response     = $pushNotificationSender->sendNotification(strtolower($devicetype), $sentMemberId, $requestData);
            			
            			if($response){
            				$notificationSent[] = [$orderId,$orderStatus,$memberId,$usertype];
            			}
            			}
            			
            			}
            			if (count($bachInsert)>0){
            				 
            				Yii::$app->db->createCommand()->batchInsert('ordernotifications', $fields,$bachInsert )->execute();
            				 
            			}
            			 
            			if (count($notificationSent)>0){
            				 
            				Yii::$app->db->createCommand()->batchInsert('ordernotificationsent', $fields,$notificationSent )->execute();
            				 
            			}
            		}
        } catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }

    }
    /**
     * Get order details for notification sent
     * @param $memberId int
     * @param $propertyGroupId int
     * @param $institutionId int
     * @return boolean value
    */
    protected function getOrderDetailsForNotification($orderId)
    {
        try {
            $orderDetails = Yii::$app->db->createCommand("
                    CALL getorderdetails(:orderid)")
                    ->bindValue(':orderid', $orderId)
                    ->queryOne();
            if(!empty($orderDetails)){
                return $orderDetails;
            }
            else{
                return true;
            }
            
        } catch (Exception $e) {
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
    * To get the device list to sent the order notification sent
    * @param $institutionId int
    * @param $privilegeId Guid
    * @param $institutionId int
    * @return boolean value
    */
   protected function getOrderNotificationDeviceList($institutionId, $privilegeId)
   { 
        try 
        {
            $deviceDetails = Yii::$app->db->createCommand(
                    "CALL get_managers_deviceid(:institutionid, :privilegeid)")
                    ->bindValue(':institutionid', $institutionId)
                    ->bindValue(':privilegeid', $privilegeId)
                    ->queryAll();
            if(!empty($deviceDetails)){
                return $deviceDetails;
            }
            else{
                return true;
            }   
        } 
        catch(ErrorException $e) {
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
    * Save and sent notifications for placed/cancelled orders
    * @param deviceList array
    * @param $orderId int
    * @param $orderStatus int
    * @return boolean value
    */
   protected function sentNotification($deviceList, $orderId, $orderStatus, $orderDetails)
   { 
        $deviceId = '';
        try 
        {
            foreach($deviceList as $device){
                $this->addOrderNotifications($device['memberid'], $device['membertype'], $orderId, $orderStatus);
                if(strtolower($device['membertype']) == 'm' && 
                    $device['membernotification']){
                    $deviceId = $device['deviceid'];
                }
                else if(strtolower($device['membertype']) == 's' && 
                    $device['spousenotification']){
                    $deviceId = $device['deviceid'];
                }
                if($deviceId){
                    $institutionId = $orderDetails['institutionid'];
                    $institutionName = $orderDetails['institutionname'];
                    $userId = $device['id'];
                    $memberId = $device['memberid'];
                    $userType = $device['membertype'];
                    if($orderStatus == Yii::$app->params['orderStatus']['placed']){
                        $message =  $orderDetails['membername'].'  has placed a food order';

                    }
                    else if ($orderStatus == Yii::$app->params['orderStatus']['cancelled'])
                    {
                        $message = $orderDetails['membername'].
                         ' has cancelled the food order';
                    }
                    if(strtolower($device['devicetype']) == "android")
                    {
                        // notificationresponse = CommunicationManager.SendGCMOrderNotification(registrationids, message, "food-request", orderNotificationViewModel, rm);
                    }
                    else if (strtolower($device['devicetype']) == "ios")
                    {

                        // notificationresponse = CommunicationManager.SendAPNSOrderNotification(registrationids, message, "food-request", orderNotificationViewModel, rm);
                    }
                }
            }   
        } 
        catch(ErrorException $e) {
            yii::error($e->getMessage());
            return false;
        }
    }

    /**
    * Save notifications for placed/cancelled orders
    * @param $memberId int
    * @param $memberType int
    * @param $orderId int
    * @param $orderStatus int
    * @return boolean value
    */
   protected function addOrderNotifications($memberId, $memberType, $orderId, $orderStatus)
   { 
        try{
            Yii::$app->db->createCommand("INSERT INTO ordernotifications (orderid,orderstatus,memberid,membertype) VALUES (:orderid, :orderstatus, :memberid, :membertype)")
                ->bindValue(':orderid', $orderId)
                ->bindValue(':orderstatus', $orderStatus) 
                ->bindValue(':memberid', $memberId) 
                ->bindValue(':membertype', $memberType) 
                ->execute();
            return true;
        }
        catch(ErrorException $e){
            yii::error($e->getMessage());
            return false;
        }
    }



    /**
    * To get member device details
    * @param $orderId int
    * @return $deviceDetails array
    */
   protected function getMemberDeviceDetails($orderId)
   { 
        
        try {
            $deviceDetails = Yii::$app->db->createCommand(
                    "CALL get_member_devicedetails(:orderid)")
                    ->bindValue(':orderid', $orderId)
                    ->queryOne();
            if(!empty($deviceDetails)){
                return $deviceDetails;
            } else{
                return true;
            }   
        } 
        catch(ErrorException $e) {
            yii::error($e->getMessage());
            return false;
        }
    }
}