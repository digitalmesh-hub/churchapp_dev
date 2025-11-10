<?php 

namespace api\modules\v3\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use api\modules\v3\models\FoodManager;
use common\components\RestaurantComponent;
use api\modules\v3\controllers\BaseController;
use api\modules\v3\models\responses\ApiResponse;
use common\models\extendedmodels\ExtendedPrivilege;
use common\models\extendedmodels\ExtendedMember;
use yii\web\UnauthorizedHttpException;
use yii\base\ActionEvent;
use Exception;

//FoodManagent 
class FoodManagementController extends BaseController

{
	public $statusCode;
	public $message = "";
	public $data;
	public $code;
	
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return ArrayHelper::merge(
			parent::behaviors(),
			[
				'verbs' => [
						'class' => \yii\filters\VerbFilter::className(),
						'actions' => [
							'get-food-menu' => ['GET'],
                            'get-food-item-details' => ['GET'],
                            'add-food-item-to-cart' => ['POST'],
                            'modify-food-items-in-cart' => ['POST'],
                            'get-food-items-in-cart' => ['GET'],
                            'place-food-order' => ['POST'],
                            'get-previous-food-orders' => ['GET'],
                            'get-food-orders' => ['GET'],
                            'get-food-order-details' => ['GET'],
                            'update-food-order-status' => ['POST'],
						]
				],
			]
		);
	}

    function beforeAction($action)
    {
        $this->on(self::EVENT_BEFORE_ACTION,function(ActionEvent $event)
        {
           $auth = Yii::$app->authManager;
           $user = Yii::$app->user->identity;
           if (in_array($event->action->id,['get-food-orders' , 'get-food-order-details'])) {
                $permissionName = "fcb852d5-0005-11e7-b48e-000c2990e707";    
                $userMemberId = $user->getUserMember();
               if(!$auth->checkAccess ($userMemberId, $permissionName)){
                    throw new UnauthorizedHttpException;
               }       
           }
        });
        return parent::beforeAction($action);
    }

	/**
	 * Index action
	 * @return $statusCode int
	*/
	public function actionIndex()
	{
		$this->statusCode = 404;
		throw new \yii\web\HttpException($this->statusCode);
	}

	/**
	 * Fetch available menu items 
	 * @return $statusCode int
	 * @return $data array
	 * @return $message string
	*/
	public function actionGetFoodMenu()
	{
		$request = Yii::$app->request;
        $user = Yii::$app->user->identity;
    	$userId = $user->id;
    	$institutionId = $user->institutionid;
        $userType = $user->usertype;
    	$memberDetails = ExtendedMember::getMemberId($userId, $institutionId, $userType);
        $memberId = $memberDetails['memberid'];
        $propertyGroup = yii::$app->params['propertyGroup']['restaurant'];
    	$flag = 0;
    	$items = [];
    	$menuList = [];
    	$foodList = [];
    	$foodManagerObject =new FoodManager();
    	$restaurantObject = new RestaurantComponent();

        try {
            if ($userId) {
        		$propertyList = $restaurantObject->getInstitutionProperties($institutionId, true, true);

        		if($propertyList){
        			$data = new \stdClass();
        			if(is_array($propertyList)){
        				ArrayHelper::multisort($propertyList, ['propertycategoryid'], [SORT_ASC]);

	        			$categoryList = [];
	        			foreach($propertyList as $model){

        					if(in_array($model['propertycategoryid'], $categoryList)){
        						$itemList = [];
        						$itemList = [
        							'itemId' => (!empty($model['propertyid']) ? (int)$model['propertyid'] : 0), 
        							'itemName' => (!empty($model['property']) ? $model['property'] : ''),
        							'itemImage' => (!empty($model['thumbnailimage']) ? (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].
        							$model['thumbnailimage']) : ''),
        							'unitPrice' => (!empty($model['price']) ? (float)$model['price'] : 0.00)
        						];
                                array_push($items, $itemList);
        					} else {
        						if($flag != 0){
        							$menuList['items']= $items;
        							array_push($foodList, $menuList);
        							$items = [];
        							$menuList = [];
        						}

        						$menuList = [
        							'categoryId' => (!empty($model['propertycategoryid']) ? 
        								(int)$model['propertycategoryid']:0), 
        							'categoryName' => (!empty($model['category']) ? $model['category'] :'')
	                            ];

	                            $itemList =  [
	        						'itemId' => (!empty($model['propertyid']) ? (int)$model['propertyid'] : 0), 
        							'itemName' => (!empty($model['property']) ? $model['property'] : ''),
        							'itemImage' => (!empty($model['thumbnailimage']) ? (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].
        							$model['thumbnailimage'] ): ''),
        							'unitPrice' => (!empty($model['price']) ? (float)$model['price'] : 0.00)
	                            ];
                                array_push($items, $itemList);
        						array_push($categoryList, $model['propertycategoryid']);
        						$flag = 1;
        					}
	        			}
		        		$menuList['items'] = $items;
		        		array_push($foodList, $menuList);
		        		$data->categories = $foodList;
        			} else {
        				$data->categories = [];
        			}
        			$cartCount = $foodManagerObject->getCartCount($memberId, $propertyGroup);
        			if ($cartCount) {
                        $data->cartItemCount = is_int($cartCount) ? $cartCount : 0;
                    } else {
                        $this->statusCode = 500;
                        $this->message = 'An error occurred while processing the request';
                        $this->data = new \stdClass();
                        return new ApiResponse($this->statusCode,$this->data,$this->message);
                    }
        			$this->statusCode = 200;
					$this->message = '';
					$this->data = $data;
					return new ApiResponse($this->statusCode, $this->data,$this->message);
        		} else {
        			$this->statusCode = 500;
					$this->message = 'An error occurred while processing the request';
					$this->data = new \stdClass();
					return new ApiResponse($this->statusCode,$this->data,$this->message);
        		}
        	} else {
        		$this->statusCode = 498;
				$this->message = 'Session invalid';
				$this->data = new \stdClass();
				return new ApiResponse($this->statusCode,$this->data,$this->message);
        	}
        } catch(Exception $e) {
        	yii::error($e->getMessage());
			$this->statusCode = 500;
			$this->message = 'An error occurred while processing the request';
			$this->data = new \stdClass();
			return new ApiResponse($this->statusCode,$this->data,$this->message);
		}
	}

    /**
     * Fetch available menu items 
     * @param itemId int 
     * @return $statusCode int
     * @return $data array
     * @return $message string
    */
    public function actionGetFoodItemDetails()
    {
        $request = Yii::$app->request;
        $itemId = $request->get('itemId');
        $user = Yii::$app->user->identity;
        $userId = $user->id;
        $institutionId = $user->institutionid;
        $userType = $user->usertype;
        $memberDetails =  ExtendedMember::getMemberId($userId, $institutionId, $userType);;
        $memberId = $memberDetails['memberid'];
        $propertyGroup = yii::$app->params['propertyGroup']['restaurant'];
        $foodManagerObject = new FoodManager();
        $imageList = [];

        try{
            $itemId = filter_var($itemId, FILTER_SANITIZE_NUMBER_INT);
            if($userId) {
                //Property items
                $propertyList = $foodManagerObject->getPropertyItem($itemId);
                if($propertyList){
                    $data = new \stdClass();
                    $cartCount = $foodManagerObject->getCartCount($memberId, $propertyGroup);
                    if($cartCount){
                        $data->cartItemCount = is_int($cartCount) ? $cartCount : 0;
                    } else {
                        $this->statusCode = 500;
                        $this->message = 'An error occurred while processing the request';
                        $this->data = new \stdClass();
                        return new ApiResponse($this->statusCode,$this->data,$this->message);
                    }

                    //Property images
                    $propertyImages = $foodManagerObject->getPropertyImages($itemId);
                    if($propertyImages){
                        if(is_array($propertyImages)){
                            foreach($propertyImages as $image){
                                if(!empty($image['imageurl'])){
                                    array_push($imageList, (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$image['imageurl']));
                                }
                            }
                        }
                    }
                    if (is_array($propertyList)) {
                        $data->itemId = (!empty($propertyList['propertyid']) ? 
                            (int)$propertyList['propertyid'] : 0);
                        $data->itemName = (!empty($propertyList['property']) ?
                            $propertyList['property'] : '');
                        $data->itemDescription = (!empty($propertyList['description']) ? $propertyList['description'] : '');
                        $data->itemImages = $imageList;
                        $data->unitPrice = (!empty($propertyList['price']) ? 
                            (float)$propertyList['price'] : 0.00);
                        $data->availableStock = 0;
                    } else {
                        $data->itemId = 0;
                        $data->itemName = '';
                        $data->itemDescription = '';
                        $data->itemImages = $imageList;
                        $data->unitPrice = 0.00;
                        $data->availableStock = 0;
                    }

                    $this->statusCode = 200;
                    $this->message = '';
                    $this->data = $data;
                    return new ApiResponse($this->statusCode, $this->data,$this->message);
                } else {
                    $this->statusCode = 500;
                    $this->message = 'An error occurred while processing the request';
                    $this->data = new \stdClass();
                    return new ApiResponse($this->statusCode,$this->data,$this->message);
                }
            } else {
                $this->statusCode = 498;
                $this->message = 'Session invalid';
                $this->data = new \stdClass();
                return new ApiResponse($this->statusCode,$this->data,$this->message);
            }
        } catch(Exception $e){
            yii::error($e->getMessage());
            $this->statusCode = 500;
            $this->message = 'An error occurred while processing the request';
            $this->data = new \stdClass();
            return new ApiResponse($this->statusCode, $this->data, $this->message);
        }
    }

    /**
     * Add selected food items to cart
     * @param itemId int
     * @param quantity int 
     * @param memberId int 
     * @param institutionId int 
     * @return $statusCode int
     * @return $data array
     * @return $message string
    */
    public function actionAddFoodItemToCart()
    {
        $request = Yii::$app->request;
        $itemId = $request->getBodyParam('itemId');
        $quantity = $request->getBodyParam('quantity');
        $memberId = $request->getBodyParam('memberId');
        $institutionId = $request->getBodyParam('institutionId');

        $userId = Yii::$app->user->identity->id;
        $propertyGroupId = yii::$app->params['propertyGroup']['restaurant'];
        $foodManagerObject = new FoodManager();

        try{
            $itemId = filter_var($itemId, FILTER_SANITIZE_NUMBER_INT);
            $quantity = filter_var($quantity, FILTER_SANITIZE_NUMBER_INT);
            $memberId = filter_var($memberId, FILTER_SANITIZE_NUMBER_INT);
            $institutionId = filter_var($institutionId, FILTER_SANITIZE_NUMBER_INT);

            $cartObject = ['itemId' => $itemId, 'quantity' => $quantity, 'memberId' => $memberId, 'institutionId' => $institutionId, 'propertyGroupId' => $propertyGroupId, 'cartId' => 0, 'isRemove' => false, 'price' => 0, 'userId' => $userId, 'currentDateTime' => gmdate("Y-m-d H:i:s")];

            if($userId) {
                $response = $foodManagerObject->addEditCartItem($cartObject);
                if($response){
                    $cartCount = $foodManagerObject->getCartCount($memberId, $propertyGroupId);
                    if($cartCount){
                        $cartCount = is_int($cartCount) ? $cartCount : 0;
                        $this->statusCode = 200;
                        $this->message = 'The selected item has been successfully added to your cart.';
                        $this->data = ['cartItemCount' => $cartCount];
                        return new ApiResponse($this->statusCode, $this->data,$this->message);
                    } else {
                        $this->statusCode = 500;
                        $this->message = 'An error occurred while processing the request';
                        $this->data = new \stdClass();
                        return new ApiResponse($this->statusCode,$this->data,$this->message);
                    } 
                } else {
                    $this->statusCode = 500;
                    $this->message = 'An error occurred while processing the request';
                    $this->data = new \stdClass();
                    return new ApiResponse($this->statusCode,$this->data,$this->message);
                } 
            } else {
                $this->statusCode = 498;
                $this->message = 'Session invalid';
                $this->data = new \stdClass();
                return new ApiResponse($this->statusCode,$this->data,$this->message);
            }
        } catch(Exception $e) {
            yii::error($e->getMessage());
            $this->statusCode = 500;
            $this->message = 'An error occurred while processing the request';
            $this->data = new \stdClass();
            return new ApiResponse($this->statusCode,$this->data,$this->message);
        }
    }

    /**
     * Update quantity or remove the selected food item from cart. 
     * @param itemId int
     * @param quantity int 
     * @param memberId int 
     * @param institutionId int 
     * @param isRemove boolean
     * @return $statusCode int
     * @return $data array
     * @return $message string
    */
    public function actionModifyFoodItemsInCart()
    {
        $request = Yii::$app->request;
        $itemId = $request->getBodyParam('itemId');
        $quantity = $request->getBodyParam('quantity');
        $isRemove = $request->getBodyParam('isRemove');
        $memberId = $request->getBodyParam('memberId');
        $institutionId = $request->getBodyParam('institutionId');

        $userId = Yii::$app->user->identity->id;
        $propertyGroupId = yii::$app->params['propertyGroup']['restaurant'];
        $foodManagerObject = new FoodManager();

        try{
            $itemId = filter_var($itemId, FILTER_SANITIZE_NUMBER_INT);
            $quantity = filter_var($quantity, FILTER_SANITIZE_NUMBER_INT);
            $memberId = filter_var($memberId, FILTER_SANITIZE_NUMBER_INT);
            $isRemove = filter_var($isRemove, FILTER_VALIDATE_BOOLEAN);
            $institutionId = filter_var($institutionId, FILTER_SANITIZE_NUMBER_INT);

            $cartObject = ['itemId' => $itemId, 'quantity' => $quantity, 'memberId' => $memberId, 'institutionId' => $institutionId, 'propertyGroupId' => $propertyGroupId, 'cartId' => 0, 'isRemove' => $isRemove, 'price' => 0, 'userId' => $userId, 'currentDateTime' => gmdate("Y-m-d H:i:s")];

            if($userId){
                $response = $foodManagerObject->addEditCartItem($cartObject);
                if($response){
                    $cartCount = $foodManagerObject->getCartCount($memberId, $propertyGroupId);
                    if($cartCount){
                        $cartCount = is_int($cartCount) ? $cartCount : 0;
                        $this->statusCode = 200;
                        $this->message = '';
                        $this->data = ['cartItemCount' => $cartCount];
                        return new ApiResponse($this->statusCode, $this->data,$this->message);
                    } else {
                        $this->statusCode = 500;
                        $this->message = 'An error occurred while processing the request';
                        $this->data = new \stdClass();
                        return new ApiResponse($this->statusCode,$this->data,$this->message);
                    } 
                } else {
                    $this->statusCode = 500;
                    $this->message = 'An error occurred while processing the request';
                    $this->data = new \stdClass();
                    return new ApiResponse($this->statusCode,$this->data,$this->message);
                } 
            } else {
                $this->statusCode = 498;
                $this->message = 'Session invalid';
                $this->data = new \stdClass();
                return new ApiResponse($this->statusCode,$this->data,$this->message);
            }
        } catch(Exception $e){
            yii::error($e->getMessage());
            $this->statusCode = 500;
            $this->message = 'An error occurred while processing the request';
            $this->data = new \stdClass();
            return new ApiResponse($this->statusCode,$this->data,$this->message);
        }
    }

    /**
     * Add selected food items to cart 
     * @param memberId int 
     * @param institutionId int 
     * @return $statusCode int
     * @return $data array
     * @return $message string
    */
    public function actionGetFoodItemsInCart()
    {
        $request = Yii::$app->request;
        $memberId = $request->get('memberId');
        $institutionId = $request->get('institutionId');
        $cartList = [];
        $taxList = [];
        $userInfo = '';

        $userId = Yii::$app->user->identity->id;
        $propertyGroupId = yii::$app->params['propertyGroup']['restaurant'];
        $foodManagerObject = new FoodManager();
        $data = new \stdClass();

        try{
            if($userId){
                $memberId = filter_var($memberId, FILTER_SANITIZE_NUMBER_INT);
                $institutionId = filter_var($institutionId, FILTER_SANITIZE_NUMBER_INT);

                //Cart item
                $cartItems = $foodManagerObject->getItemsInCart($memberId, $institutionId, $propertyGroupId);

                //TaxItems
                $taxItems = $foodManagerObject->getAllActivePropertyGroupTaxes($institutionId, $propertyGroupId, true); 

                if($cartItems){
                    if(is_array($cartItems)){
                        foreach($cartItems as $object){
                            if(!(bool)$object['IsItemAvailable']){
                                $userInfo = 'This item is currently not available.';
                            } else if($object['price'] == $object['cartprice']){
                                $userInfo = 'There has been a change in the price of this item.';
                            }

                            $result = [
                                'itemId' => (!empty($object['propertyid'])) ? (int)$object['propertyid'] : 0,
                                'itemName' => (!empty($object['property'])) ? $object['property'] : '',
                                'itemImage' => (!empty($object['thumbnailimage'])) ? (string)preg_replace('/\s/', "%20",yii::$app->params['imagePath'].$object['thumbnailimage'] ): '',
                                'quantity' => (!empty($object['quantity'])) ? (int)$object['quantity'] : '',
                                'unitPrice' => (!empty($object['price'])) ? (float)$object['price'] : 0.00,
                                'unitPriceWhenAddedToCart' => (!empty($object['cartprice'])) ? (float)$object['cartprice'] : 0.00,
                                'isItemAvailable' => (!empty($object['IsItemAvailable'])) ? (bool)$object['IsItemAvailable'] : false,
                                'userInfo' => $userInfo
                            ];
                            array_push($cartList, $result);
                        }
                    }
                    $data->items = $cartList; 

                    if($taxItems){
                        if(is_array($taxItems)){
                            foreach($taxItems as $object){
                                $result = [
                                    'taxId' => (!empty($object['taxid'])) ? (int)$object['taxid'] : 0,
                                    'taxName' => (!empty($object['description'])) ? $object['description'] : '',
                                    'taxPercentage' => (!empty($object['rate'])) ? (float)$object['rate'] : 0.00
                                ];
                                array_push($taxList, $result);
                            }
                        }
                        $data->taxes = $taxList;

                        $cartCount = $foodManagerObject->getCartCount($memberId, $propertyGroupId);
                        if($cartCount){
                            $cartCount = is_int($cartCount) ? $cartCount : 0;
                        } else {
                            $this->statusCode = 500;
                            $this->message = 'An error occurred while processing the request';
                            $this->data = new \stdClass();
                            return new ApiResponse($this->statusCode,$this->data,$this->message);
                        }
                        $data->cartItemCount = $cartCount;
                        $this->statusCode = 200;
                        $this->message = '';
                        $this->data = $data;
                        return new ApiResponse($this->statusCode, $this->data,$this->message); 
                    } else {
                        $this->statusCode = 500;
                        $this->message = 'An error occurred while processing the request';
                        $this->data = new \stdClass();
                        return new ApiResponse($this->statusCode,$this->data,$this->message);
                    }
                } else {
                    $this->statusCode = 500;
                    $this->message = 'An error occurred while processing the request';
                    $this->data = new \stdClass();
                    return new ApiResponse($this->statusCode,$this->data,$this->message);
                }
            } else {
                $this->statusCode = 498;
                $this->message = 'Session invalid';
                $this->data = new \stdClass();
                return new ApiResponse($this->statusCode,$this->data,$this->message);
            }
        } catch(Exception $e) {
            yii::error($e->getMessage());
            $this->statusCode = 500;
            $this->message = 'An error occurred while processing the request';
            $this->data = new \stdClass();
            return new ApiResponse($this->statusCode,$this->data,$this->message);  
        }
    }

     /**
     * Make an order for the food items added on the cart 
     * @param memberId int 
     * @param institutionId int 
     * @param preferredDate date
     * @param preferredTime time
     * @param taxes array
     * @param items array
     * @return $statusCode int
     * @return $data array
     * @return $message string
    */
    public function actionPlaceFoodOrder()
    {
        $request = Yii::$app->request;
        $memberId = $request->getBodyParam('memberId');
        $institutionId = $request->getBodyParam('institutionId');
        $preferredDate = $request->getBodyParam('preferredDate');
        $preferredTime = $request->getBodyParam('preferredTime');
        $taxes = $request->getBodyParam('taxes');
        $items = $request->getBodyParam('items');
        
        $user = Yii::$app->user->identity;
        $userId = $user->id;
        $userType = $user->usertype;
        $propertyGroupId = yii::$app->params['propertyGroup']['restaurant'];

        $foodManagerObject = new FoodManager();
        $data = new \stdClass();
        $itemList = [];
        $taxList = [];
        $orderStatus = 0;
        
        try{
            if($userId){
                $currentDateTime = gmdate("Y-m-d H:i:s");
                $preferredDate = date_format(date_create_from_format(Yii::$app->params['dateFormat']['dateOfBrithFormat'],$preferredDate),Yii::$app->params['dateFormat']['sqlDateFormat']);

                //Passing parameters
                $orderList = [
                    'memberId' => $memberId, 
                    'institutionId' => $institutionId, 
                    'preferredDate' => $preferredDate, 
                    'preferredTime' => $preferredTime,
                    'userId' => $userId, 
                    'currentDateTime' => $currentDateTime, 
                    'userType' => $userType, 
                    'orderStatus' => $orderStatus, 
                    'propertyGroupId' => $propertyGroupId
                ];

                $orderId = $foodManagerObject->addOrder($orderList);
                if ($orderId) {
                    //items
                    if(!empty($items)){
                        foreach($items as $object){
                            $result = [
                                $orderId, 
                                $object['itemId'], 
                                $object['unitPrice'],
                                $object['quantity'],  
                                $userId, 
                                $currentDateTime
                            ];
                            array_push($itemList, $result);
                        }
                        $itemStatus = $foodManagerObject->addOrderItems($itemList);
                        if (!$itemStatus) {
                            $this->statusCode = 500;
                            $this->message = 'An error occurred while processing the request';
                            $this->data = new \stdClass();
                            return new ApiResponse($this->statusCode,$this->data,$this->message);  
                        }   
                    }

                    //taxes
                    if (!empty($taxes)) {
                        foreach($taxes as $object) {
                            $result = [
                                $orderId, $object['taxId'], $object['taxPercentage']
                            ];
                            array_push($taxList, $result);
                        }
                        $taxStatus = $foodManagerObject->addOrderTaxes($taxList);
                        if (!$taxStatus) {
                            $this->statusCode = 500;
                            $this->message = 'An error occurred while processing the request';
                            $this->data = new \stdClass();
                            return new ApiResponse($this->statusCode,$this->data,$this->message);  
                        }   
                    }
                } else {
                    $this->statusCode = 500;
                    $this->message = 'An error occurred while processing the request';
                    $this->data = new \stdClass();
                    return new ApiResponse($this->statusCode,$this->data,$this->message);  
                }

                $cartCount = $foodManagerObject->getCartCount($memberId, $propertyGroupId);
                if ($cartCount) {
                    $cartCount = is_int($cartCount) ? $cartCount : 0;
                } else {
                    $this->statusCode = 500;
                    $this->message = 'An error occurred while processing the request';
                    $this->data = new \stdClass();
                    return new ApiResponse($this->statusCode,$this->data,$this->message);
                }

                //Remove cart
                $foodType = 2;
                $removeCartStatus = $foodManagerObject->removeOrderFromCart($memberId, $propertyGroupId, $institutionId);

                $privilegeId = Yii::$app->params['clubAppPrivileges']['manageFoodOrders'];
                $foodManagerObject->sentOrderNotification($orderId, $orderStatus, $privilegeId,$institutionId,$foodType);

                $data->cartItemCount = $cartCount;
                $this->statusCode = 200;
                $this->message = 'Your order has been placed. We will contact you shortly.';
                $this->data = $data;
                return new ApiResponse($this->statusCode, $this->data,$this->message); 
            } else {
                $this->statusCode = 498;
                $this->message = 'Session invalid';
                $this->data = new \stdClass();
                return new ApiResponse($this->statusCode,$this->data,$this->message);
            } 
        }
        catch(Exception $e){
            yii::error($e->getMessage());
            $this->statusCode = 500;
            $this->message = 'An error occurred while processing the request';
            $this->data = new \stdClass();
            return new ApiResponse($this->statusCode,$this->data,$this->message);  
        }
    }


    /**
     * Get previous food order details
     * @param memberId int 
     * @param institutionId int 
     * @return $statusCode int
     * @return $data array
     * @return $message string
    */
    public function actionGetPreviousFoodOrders()
    {
        $request = Yii::$app->request;
        $memberId = $request->get('memberId');
        $institutionId = $request->get('institutionId');

        $userId = Yii::$app->user->identity->id;
        $propertyGroupId = yii::$app->params['propertyGroup']['restaurant'];
        $foodManagerObject = new FoodManager();
        $data = new \stdClass();
        $orderIdList = [];
        $itemList =[];
        $finalList = [];
        $flag = 0;
        $totalAmount = 0.00;
        $priceTax = 0.00;
        $itemPrice = 0;
        $cartCount = 0;

        try{
            if ($userId) {
                $memberId = filter_var($memberId, FILTER_SANITIZE_NUMBER_INT);
                $institutionId = filter_var($institutionId, FILTER_SANITIZE_NUMBER_INT);
                $previousOrders = $foodManagerObject->getPreviousOrders($memberId, $institutionId, $propertyGroupId);
                if ($previousOrders) {
                    if(is_array($previousOrders)){
                        ArrayHelper::multisort($previousOrders, ['orderid'], [SORT_ASC]);
                        foreach($previousOrders as $object){
                            if(in_array($object['orderid'], $orderIdList)){

                                if(!empty($object['quantity']) && !empty($object['price'])){
                                    $itemPrice = $object['quantity'] * $object['price'];
                                }

                                $result = [
                                    'itemId' => (!empty($object['propertyid']) ? (int)$object['propertyid'] : 0),
                                    'itemName' => (!empty($object['property']) ? $object['property'] : ''),
                                    'quantity' =>  (!empty($object['quantity']) ? (int)$object['quantity'] : 0),
                                    'itemPrice' => $itemPrice
                                ];
                                array_push($itemList, $result);
                                $totalAmount += $itemPrice;
                            } else {
                                if($flag != 0){
                                    //Find tax amount
                                    $orderResult = $foodManagerObject->getOrderTaxDetails(
                                        $previousOrderId);

                                    if($orderResult){
                                        if(is_array($orderResult)){
                                            foreach($orderResult as $item){
                                                $priceTax += (($totalAmount * $item['taxrate']) / 100);
                                            }
                                        }
                                        $orderList['totalAmount'] = $priceTax + $totalAmount;
                                    }

                                    $orderList['items']= $itemList;
                                    array_push($finalList, $orderList);
                                    $itemList = [];
                                    $orderList = [];
                                    $totalAmount = 0.00;
                                    $priceTax = 0.00;
                                }

                                $orderDate = (!empty($object['orderdate']) ? 
                                        date(Yii::$app->params['dateFormat']['viewDateFormat'],strtotimeNew($object['orderdate'])): '');
                                $orderTime = (!empty($object['ordertime']) ? 
                                    $object['ordertime']: '');

                                if(!empty($object['quantity']) && !empty($object['price'])){
                                    $itemPrice = $object['quantity'] * $object['price'];
                                }
                                $totalAmount += $itemPrice;

                                $orderList = [
                                    'orderId' => (!empty($object['orderid']) ? 
                                        (int)$object['orderid'] : 0), 
                                    'orderDateTime' => trim($orderDate . ' ' . $orderTime),
                                    'orderStatus' => (!empty($object['orderstatus']) ? 
                                        (int)$object['orderstatus'] : 0),
                                    'reasonForRejection' => (!empty($object['note']) ? $object['note'] : '')
                                ];

                                $result = [
                                    'itemId' => (!empty($object['propertyid']) ? (int)$object['propertyid'] : 0),
                                    'itemName' => (!empty($object['property']) ? $object['property'] : ''),
                                    'quantity' =>  (!empty($object['quantity']) ? (int)$object['quantity'] : 0),
                                    'itemPrice' => $itemPrice
                                ];

                                array_push($itemList, $result);
                                array_push($orderIdList, $object['orderid']);
                                $flag = 1;
                                $previousOrderId = $object['orderid'];
                            }
                        }
                        $orderResult = $foodManagerObject->getOrderTaxDetails(
                                        $previousOrderId);

                        if($orderResult){
                            if(is_array($orderResult)){
                                foreach($orderResult as $item){
                                    $priceTax += (($totalAmount * $item['taxrate']) / 100);
                                }
                            }
                            $orderList['totalAmount'] = $priceTax + $totalAmount;
                        }
                        $orderList['items']= $itemList;
                        array_push($finalList, $orderList);

                        //Cart count
                        $cartCount = $foodManagerObject->getCartCount($memberId, $propertyGroupId);
                        if($cartCount){
                            $cartCount = is_int($cartCount) ? $cartCount : 0;
                        } else {
                            $this->statusCode = 500;
                            $this->message = 'An error occurred while processing the request';
                            $this->data = new \stdClass();
                            return new ApiResponse($this->statusCode,$this->data,$this->message);
                        }
                        $data->orders = $finalList;
                        $data->cartItemCount = $cartCount;
                        $message = 'Your order has been placed. We will contact you shortly.';

                    } else {
                        $data->orders = [];
                        $data->cartItemCount = $cartCount;
                        $message = '';
                    }
                    $this->statusCode = 200;
                    $this->message = $message;
                    $this->data = $data;
                    return new ApiResponse($this->statusCode,$this->data,$this->message);
                } else {
                    $this->statusCode = 500;
                    $this->message = 'An error occurred while processing the request';
                    $this->data = new \stdClass();
                    return new ApiResponse($this->statusCode,$this->data,$this->message); 
                }
            } else {
                $this->statusCode = 498;
                $this->message = 'Session invalid';
                $this->data = new \stdClass();
                return new ApiResponse($this->statusCode,$this->data,$this->message);
            }
        } catch(Exception $e) {
            yii::error($e->getMessage());
            $this->statusCode = 500;
            $this->message = 'An error occurred while processing the request';
            $this->data = new \stdClass();
            return new ApiResponse($this->statusCode,$this->data,$this->message);  
        }
    }

    
    /**
     * Get food orders
     * @return $statusCode int
     * @return $data array
     * @return $message string
    */
    public function actionGetFoodOrders()
    {
        $userId = Yii::$app->user->identity->id;
        $data = new \stdClass();
        $foodManagerObject = new FoodManager();
        $propertyGroupId = yii::$app->params['propertyGroup']['restaurant'];
        $previlegeId = yii::$app->params['clubAppPrivileges']['manageFoodOrders'];
        $currentDateTime = gmdate("Y-m-d H:i:s");
        $orderList = [];

        try{
            if($userId != 0){
                $foodOrders = $foodManagerObject->getFoodOrders($userId, $propertyGroupId, $previlegeId, $currentDateTime);
                if($foodOrders){
                    if(is_array($foodOrders)){
                        foreach($foodOrders as $object){
                            $result = [
                                'orderId' => (!empty($object['orderid']) ? (int)$object['orderid'] : 0),
                                'orderDate' => (!empty($object['orderdate']) ? date(Yii::$app->params['dateFormat']['viewDateFormat'],strtotimeNew($object['orderdate'])) : ''),
                                'orderTime' => (!empty($object['ordertime']) ? $object['ordertime'] : ''),
                                'orderStatus' => (!empty($object['orderstatus']) ? (int)$object['orderstatus'] : 0),
                                'memberId' => (!empty($object['memberid']) ? $object['memberid'] : ''),
                                'memberTitle' => (!empty($object['title']) ? $object['title'] : ''),
                                'memberName' => (!empty($object['membername']) ? $object['membername'] : ''),
                                'memberImage' => (!empty($object['memberimage']) ? (string)preg_replace('/\s/', "%20",Yii::$app->params['imagePath'].$object['memberimage']) : ''),
                                'institutionId' => (!empty($object['institutionid']) ? (int)$object['institutionid'] : 0),
                                'institutionName' => (!empty($object['institutionname']) ? $object['institutionname'] : ''),
                            ];
                            array_push($orderList, $result);
                        }
                    }
                    $data->orders = $orderList;
                    $this->statusCode = 200;
                    $this->message = '';
                    $this->data = $data;
                    return new ApiResponse($this->statusCode,$this->data,$this->message);
                }
                else{
                    $this->statusCode = 500;
                    $this->message = 'An error occurred while processing the request';
                    $this->data = new \stdClass();
                    return new ApiResponse($this->statusCode,$this->data,$this->message);
                }
            }
            else{
                $this->statusCode = 498;
                $this->message = 'Session invalid';
                $this->data = new \stdClass();
                return new ApiResponse($this->statusCode,$this->data,$this->message);
            }
        }
        catch(Exception $e){
            yii::error($e->getMessage());
            $this->statusCode = 500;
            $this->message = 'An error occurred while processing the request';
            $this->data = new \stdClass();
            return new ApiResponse($this->statusCode,$this->data,$this->message);  
        }
    }

    /**
     * Get the details of a food order
     * @param $orderId int
     * @return $statusCode int
     * @return $data array
     * @return $message string
    */
    public function actionGetFoodOrderDetails()
    {
        $request = Yii::$app->request;
        $orderId =  $request->get('orderId');
        $userId = Yii::$app->user->identity->id;
        $data = new \stdClass();
        $foodManagerObject = new FoodManager();
        $totalAmount = 0; 
        $taxList = [];
        $itemList = [];

        try{
            if ($userId) {
                $orderId = filter_var($orderId, FILTER_SANITIZE_NUMBER_INT);
                $orderResult = $foodManagerObject->getOrderDetails($orderId);
                $itemResult = $foodManagerObject->getOrderItemDetails($orderId);
                $taxResult = $foodManagerObject->getOrderTaxDetails($orderId);
                if ($orderResult) {
                    if (is_array($orderResult)) {
                        //Order items
                        if ($itemResult) {
                            if (is_array($itemResult)) {
                                foreach($itemResult as $item) {
                                   $result = [
                                        'itemId' => (!empty($item['propertyid']) ? (int)$item['propertyid'] : 0),
                                        'itemName' => (!empty($item['property']) ? (int)$item['property'] : ''),
                                        'itemImage' => (!empty($item['image']) ? (string)preg_replace('/\s/', "%20",Yii::$app->params['imagePath'].
                                            $item['image']) : ''),
                                        'quantity' => (!empty($item['quantity']) ? (int)$item['quantity'] : 0),
                                        'unitPrice' => (!empty($item['price']) ? (float)$item['price'] : 0.00),

                                    ];
                                    if(!empty($item['price']) && !empty($item['price'])){
                                        $totalAmount += ($item['price'] * $item['quantity']);
                                    }
                                    array_push($itemList, $result);
                                }
                            }
                        }
                        //Tax 
                        if ($taxResult) {
                            if (is_array($taxResult)) {
                                foreach($taxResult as $item) {
                                   $result = [
                                        'taxId' => (!empty($item['taxid']) ? (int)$item['taxid'] : 0),
                                        'taxName' => (!empty($item['description']) ? $item['description'] : ''),
                                        'taxPercentage' => (!empty($item['taxrate']) ? $item['taxrate'] : ''),
                                    ];
                                    array_push($taxList, $result);
                                }
                            }
                        }
                        $data->orderId = (!empty($orderResult['orderid']) ? (int)$orderResult['orderid'] : 0);
                        $data->orderDate = (!empty($orderResult['orderdate']) ? date(Yii::$app->params['dateFormat']['viewDateFormat'],strtotimeNew($orderResult['orderdate'])) : '');
                        $data->orderTime = (!empty($orderResult['ordertime']) ? $orderResult['ordertime'] : '');
                        $data->orderStatus = (!empty($orderResult['orderstatus']) ? (int)$orderResult['orderstatus'] : 0);
                        $data->memberId = (!empty($orderResult['memberid']) ? $orderResult['memberid'] : '');
                        $data->memberTitle = (!empty($orderResult['title']) ? $orderResult['title'] : '');
                        $data->memberName = (!empty($orderResult['membername']) ? $orderResult['membername'] : '');
                        $data->memberImage = (!empty($orderResult['memberimage']) ? 
                            (string)preg_replace('/\s/', "%20",Yii::$app->params['imagePath'].$orderResult['memberimage']) : '');
                        $data->memberPhone = (!empty($orderResult['mobilenumber']) ? $orderResult['mobilenumber'] : '');
                        $data->memberEmail = (!empty($orderResult['memberemail']) ? $orderResult['memberemail'] : '');
                        $data->userGroup = (!empty($orderResult['usergroup']) ? (int)$orderResult['usergroup'] : 0);
                        $data->totalAmount = $totalAmount;
                        $data->taxes = $taxList;
                        $data->items = $itemList;
                    }
                } else {

                        $data->orderId = 0;
                        $data->orderDate =  '';
                        $data->orderTime = '';
                        $data->orderStatus =  0;
                        $data->memberId =  '';
                        $data->memberTitle = '';
                        $data->memberName = '';
                        $data->memberImage = '';
                        $data->memberPhone =  '';
                        $data->memberEmail =  '';
                        $data->userGroup = 0;
                        $data->totalAmount = $totalAmount;
                        $data->taxes = $taxList;
                        $data->items = $itemList;
                }
                $this->statusCode = 200;
                $this->message = '';
                $this->data = $data;
                return new ApiResponse($this->statusCode, $this->data, $this->message);
            } else {
                $this->statusCode = 498;
                $this->message = 'Session invalid';
                $this->data = new \stdClass();
                return new ApiResponse($this->statusCode,$this->data,$this->message);
            }

        } catch(Exception $e){
            yii::error($e->getMessage());
            $this->statusCode = 500;
            $this->message = 'An error occurred while processing the request';
            $this->data = new \stdClass();
            return new ApiResponse($this->statusCode,$this->data,$this->message);  
        }
    }

    /**
     * Update food order status
     * @param $orderId int
     * @return $statusCode int
     * @return $data array
     * @return $message string
    */
    public function actionUpdateFoodOrderStatus()
    {
        $request = Yii::$app->request;
        
        $orderId =  $request->getBodyParam('orderId');
        $orderStatus =  $request->getBodyParam('orderStatus');
        $reasonForRejection =  $request->getBodyParam('reasonForRejection');

        $data = new \stdClass();
        $userId = Yii::$app->user->identity->id;
        $foodManagerObject = new FoodManager();
        $currentDateTime = gmdate("Y-m-d H:i:s");
        $institutionId = Yii::$app->user->identity->institutionid;
        try{
            $orderId = filter_var($orderId, FILTER_SANITIZE_NUMBER_INT);
            if($userId){
                $orderResult = $foodManagerObject->getOrderStatus($orderId);
                if ($orderResult) {
                    if (is_array($orderResult)) {
                        if($orderResult['orderstatus'] != Yii::$app->params['orderStatus']['handover'] && $orderResult['orderstatus'] != Yii::$app->params['orderStatus']['cancelled'] && $orderResult['orderstatus'] != Yii::$app->params['orderStatus']['rejected'] && 
                            $orderResult['orderstatus'] != Yii::$app->params['orderStatus']['removeFromMyOrder']){

                            $responseStatus = $foodManagerObject->updateOrderStatus($orderId,$orderStatus,$reasonForRejection,$userId,$currentDateTime);
                            if ($responseStatus) {
                                if ($orderStatus != Yii::$app->params['orderStatus']['removeFromMyOrder']) {
                                    //Notification
                                        $privilegeId = ExtendedPrivilege::MANAGE_FOOD_ORDERS;
                                        $foodType = 1;
                                        $foodManagerObject->sentOrderNotification($orderId, $orderStatus, $privilegeId,$institutionId,$foodType);
                                }
                                $this->statusCode = 200;
                                $this->message = 'Your request has been processed successfully.';
                                $this->data = $data;
                                return new ApiResponse($this->statusCode,$this->data,$this->message);
                            } else {
                                $this->statusCode = 500;
                                $this->message = 'An error occurred while processing the request';
                                $this->data = new \stdClass();
                                return new ApiResponse($this->statusCode,$this->data,$this->message);
                            }
                        } else {
                            $responseStatus = $foodManagerObject->updateOrderStatus($orderId, $orderStatus, null, $userId, $currentDateTime);
                            
                            if ($responseStatus) {
                                if($orderStatus != Yii::$app->params['orderStatus']['removeFromMyOrder']) {
                                    //Notification
                                	$privilegeId = ExtendedPrivilege::MANAGE_FOOD_ORDERS;
                                    $foodType = 1;
                                    $foodManagerObject->sentOrderNotification($orderId, $orderStatus, $privilegeId,$institutionId,$foodType);
                                       
                                }

                                $this->statusCode = 200;
                                $this->message = 'Your request has been processed successfully.';
                                $this->data = $data;
                                return new ApiResponse($this->statusCode,$this->data,$this->message);
                            } else {
                                $this->statusCode = 500;
                                $this->message = 'An error occurred while processing the request';
                                $this->data = new \stdClass();
                                return new ApiResponse($this->statusCode,$this->data,$this->message);
                            }
                        }
                    } else {
                        $this->statusCode = 500;
                        $this->message = 'An error occurred while processing the request';
                        $this->data = new \stdClass();
                        return new ApiResponse($this->statusCode,$this->data,$this->message);
                    }
                }
            } else {
                $this->statusCode = 498;
                $this->message = 'Session expired.please login again.';
                $this->data = new \stdClass();
                return new ApiResponse($this->statusCode,$this->data,$this->message);
            }
        } catch(Exception $e) {
            yii::error($e->getMessage());
            $this->statusCode = 500;
            $this->message = 'An error occurred while processing the request';
            $this->data = new \stdClass();
            return new ApiResponse($this->statusCode,$this->data,$this->message);  
        }
    }
}