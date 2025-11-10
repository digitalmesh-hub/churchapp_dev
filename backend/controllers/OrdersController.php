<?php

namespace backend\controllers;

use Yii;
use common\models\extendedmodels\ExtendedOrders;
use common\models\searchmodels\ExtendedOrdersSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\extendedmodels\ExtendedOrderstatus;
use yii\helpers\ArrayHelper;
use common\models\extendedmodels\ExtendedOrdernotifications;
use common\models\extendedmodels\ExtendedPrivilege;
use common\models\extendedmodels\ExtendedDevicedetails;


/**
 * OrdersController implements the CRUD actions for ExtendedOrders model.
 */
class OrdersController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    function beforeAction($action)
    {
    	//if manage food orders permission is enabled user can access.
    	if (!Yii::$app->user->can('fcb852d5-0005-11e7-b48e-000c2990e707')) {
    		throw new \yii\web\ForbiddenHttpException('You are not allowed to perform the requested action');
    	}
    	return parent::beforeAction($action);
    }

    /**
     * Lists all ExtendedOrders models.
     * @return mixed
     */
    public function actionIndex()
    {
    	
        $searchModel = new ExtendedOrdersSearch();
        $model = new ExtendedOrderstatus();
        $params = Yii::$app->request->queryParams;
        $searchModel->propertygroupid = 1;
        $searchModel->institutionid = Yii::$app->user->identity->institutionid;
        $searchModel->start_date = isset($params['ExtendedOrdersSearch']['start_date']) ? $params['ExtendedOrdersSearch']['start_date'] : date('d-M-Y');
        $searchModel->end_date = isset($params['ExtendedOrdersSearch']['end_date']) ? $params['ExtendedOrdersSearch']['end_date']: date('d-M-Y',strtotimeNew('+7days'));
        $dataProvider = $searchModel->search($params);
        $orderStatusArray =	ArrayHelper::map(ExtendedOrderstatus::getAllOrderstatus(),'statusid','status');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'orderModel' => $dataProvider,
        	'orderStatusArray' =>  $orderStatusArray,
        	'model' => $model,
        ]);
    }

    /**
     * Displays a single ExtendedOrders model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ExtendedOrders model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ExtendedOrders();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->orderid]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ExtendedOrders model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->orderid]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ExtendedOrders model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the ExtendedOrders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExtendedOrders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExtendedOrders::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    /**
     * Complete food order details
     */
    public function actionFoodOrders($id)
    {
    	$propertyGroupId = 1;
    	$model = new ExtendedOrders();
    	$orderDetails = ExtendedOrders::getOrderDetails($propertyGroupId, $id);
    	$orders = ExtendedOrders::getOrderStatus($id);
    	$orderStatus = $orders['orderstatus'];
 		$orderId = $orders['orderid'];
    	foreach ($orderDetails as $key => $value) {
    		$memberId = $value['memberid'];
    	}
    	$userDetails = ExtendedOrders::getUserDetailsOfOrder($id, $memberId);
    	return $this->render('foodorders',[
    			'orderDetails' => $orderDetails,
    			'userDetails' => $userDetails,
    			'orderStatus' => $orderStatus,
    			'orderId' => $orderId,
    			'model' => $model
    			
    	]);
    }
    /**
     * Reject an order
     */
    public function actionRejectOrder()
    {
    	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	if (yii::$app->request->isAjax) {
    		$orderId = yii::$app->request->post('orderId');
    		$note = yii::$app->request->post('note');
    		$orders = ExtendedOrders::getOrderStatus($orderId);
    		$orderStatus = $orders['orderstatus'];
    		$rejectStatus = ExtendedOrderstatus::REJECTED;
    		$modifiedBy = Yii::$app->user->identity->id;
    		$modifiedDate = date('Y-m-d H:i:s');
    		if($orderStatus != ExtendedOrderstatus::HANDOVER  && $orderStatus != ExtendedOrderstatus::REJECTED  && $orderStatus !=  ExtendedOrderstatus::CANCELLED  ) {
    			$rejectOrder = ExtendedOrders::rejectOrder($orderId,$rejectStatus,$note,$modifiedBy,$modifiedDate);
    			if($rejectOrder == true) {
    			    $institutionDetails = Yii::$app->user->identity;
    			    $institutionId = $institutionDetails['institutionid']; 
    			    $this->toSendOrderNotification($orderId,$rejectStatus,$institutionId,$this->currentUser()->institution->name);
    				return [
    					'status' => 'success',
    					'data' => null
    				];
    			} else {
					return [ 
						'status' => 'error',
						'data' => null 
					];
				}
    		}
    	}
    }
    /**
     * Update the status of 
     * an order to ready
     */
    public function actionUpdateStatus()
    {
    	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	if (yii::$app->request->isAjax) {
    		$orderId = yii::$app->request->post('orderId');
    		$orderStatus = yii::$app->request->post('orderStatus');
    		if ($orderId && $orderStatus) {
    			$modifiedBy = Yii::$app->user->identity->id;
    			$modifiedDate = date('Y-m-d H:i:s');
    			$updateToReady = ExtendedOrders::updateToReady($orderStatus,$modifiedBy,$modifiedDate,$orderId);
    			if ($orderStatus != ExtendedOrderstatus::REMOVE_FROM_MY_ORDER) {
    				$institutionId = Yii::$app->user->identity->institutionid;
    				$this->toSendOrderNotification($orderId,$orderStatus,$institutionId,$this->currentUser()->institution->name);
    			}
    			if($updateToReady == true) {
    				return [
    					'status' => 'success',
    					'data' => null
    				];
    			} else {
    				return [
    					'status' => 'error',
    					'data' => null
    				];
    			}
    		}
    	}
    }
    /**
     * To send the order notification
     */
    protected function toSendOrderNotification($orderId, $orderStatus, $institutionId, $institutionName)
    {
    	
    	$orderDetails   = ExtendedOrders::getOrderData($orderId);
    	$pushNotificationSender = Yii::$app->PushNotificationHandler;
        $notificationHandler = Yii::$app->NotificationHandler;
    	if ($orderStatus == ExtendedOrderstatus::CANCELLED || $orderStatus == ExtendedOrderstatus::PLACED) {
    		$deviceDetails =  ExtendedOrders::getOrderNotificationDevices($institutionId,ExtendedPrivilege::MANAGE_FOOD_ORDERS);
    		$fields = ['orderid', 'orderstatus', 'memberid', 'membertype'];
    		$bulkInsert = [];
    		foreach ($deviceDetails as $device) {
    			$bulkInsert[] = [$orderId,$orderStatus,$device['memberid'],$device['membertype']];
    			$registrationid = null;
    			if (strtolower($device['membertype']) == "m" && $device['membernotification'] == true)  {
    					$registrationid = $device['deviceid'];
    			}
    			if (strtolower($device['membertype']) == "s" && $device['spousenotification'] == true) {
    					$registrationid = $device['deviceid'];
    			}
    			if ($registrationid) {
    				$message = '';
    				if ($orderStatus == ExtendedOrderstatus::PLACED) {
    					$message = $orderDetails['membername'] . " has placed a food order";
    				} else if ($orderStatus == ExtendedOrderstatus::CANCELLED) {
    					$message = $orderDetails['membername'] . " has cancelled the food order";
    				}
    				$requestData  = $pushNotificationSender->setPushNotificationRequest($registrationid,$message,"food-request",$institutionId,$orderId,$institutionName,strtolower($device['devicetype']),$device['userid']);
    				$response  = $pushNotificationSender->sendNotification(strtolower($device['devicetype']), $registrationid, $requestData);	
    			}
    		}
    	} else {
    		$fields = ['orderid','orderstatus','memberid','membertype'];
    		$bulkInsert = [];
    		$deviceDetailsModel = new ExtendedDevicedetails();
    		$deviceDetails = $deviceDetailsModel->getMemberDeviceDetails($orderId);
    		if (!empty($deviceDetails)) {
    			$registrationid = null;
    			foreach ($deviceDetails as $device) {
    			    $bulkInsert[] = [$orderId,$orderStatus,$device['memberid'],$device['membertype']];
    			if (strtolower($device['membertype']) == "m" && $device['membernotification'] == true) {
    				$registrationid = $device['deviceid'];
    			}
    			if (strtolower($device['membertype']) == "s" && $device['spousenotification'] == true) {
    				$registrationid = $device['deviceid'];
    			}
    			if ($orderStatus == ExtendedOrderstatus::READY) {
    				$message =  "Your food order from " . $institutionName . " is ready";
    			} else if ($orderStatus == ExtendedOrderstatus::CONFIRMED) {
    				$message = "Your order has been confirmed by " . $institutionName;
    			} else if ($orderStatus == ExtendedOrderstatus::REJECTED) {
    				$message = "Your order has been rejected by " . $institutionName;
    			}
    			else if ($orderStatus == ExtendedOrderstatus::HANDOVER) {
    				$message = "Your food order has been delivered by " . $institutionName;
    			}
    			if ($registrationid) {			
    				$requestData  = $pushNotificationSender->setPushNotificationRequest($registrationid,$message,"food-update",$institutionId,$orderId,$institutionName,strtolower($device['devicetype']),$device['id']);
    				$response  = $pushNotificationSender->sendNotification(strtolower($device['devicetype']), $registrationid, $requestData);
    			}
    		  }	
    		}
    	}
    }
}


