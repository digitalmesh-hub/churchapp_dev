<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\extendedmodels\ExtendedOrders;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\extendedmodels\ExtendedOrderstatus;

class OrderNotificationsController extends Controller{
	
	/**
	 * Sent order notification
	 */
	public function actionOrderNotificationSent(){
		$pushNotificationSender =Yii::$app->PushNotificationHandler;
		
		$institutions = ExtendedInstitution::getAllInstitutions();
		$notificationSent = [];
		$fields    = ['orderid','orderstatus','memberid','membertype'];
		foreach ($institutions as $institution){
			
			$institutionId   = $institution['id'];
	 		$timeZone 	     = trim($institution['timezone']);
	 		$institutionName = trim($institution['name']); 
	 		
	 		$orderNotificationDevices = ExtendedOrders::getOrderNotificationDevices($institutionId);
	 		if (!empty($orderNotificationDevices)){
		 		foreach ($orderNotificationDevices as $notificationDevices ){
		 			$message = '';
		 			switch ($notificationDevices['orderstatus']){
		 				case ExtendedOrderstatus::PLACED:
                          $message = $notificationDevices['membername'] . " has placed a food order.";
                          break;
                                case ExtendedOrderstatus::CONFIRMED:
                                    $message = "Your food order has been confirmed by " . $notificationDevices['institution'];
                                    break;
                                case ExtendedOrderstatus::READY:
                                    $message = "Your food order from" . $notificationDevices['institution'] . " is ready.";
                                    break;
                                case ExtendedOrderstatus::HANDOVER:
                                    $message = "Your food order has been delivered by " . $notificationDevices['institution'];
                                    break;
                                case ExtendedOrderstatus::REJECTED:
                                    $message = "Your food order has been rejected by " . $notificationDevices['institution'];
                                    break;
                                case ExtendedOrderstatus::CANCELLED:
                                    $message = $notificationDevices['membername'] . " has cancelled the food order.";
                                    break;   
		 			}
		 			
		 			//notification Type
		 			$registrationid = null;
		 			
		 			if ($notificationDevices['orderstatus'] == ExtendedOrderstatus::PLACED || $notificationDevices['orderstatus'] == ExtendedOrderstatus::CANCELLED )
		 			{
		 				$registrationid = $notificationDevices['deviceid'];
		 				$notificationType = "food-request";
		 			}
		 			else
		 				if (($notificationDevices['orderstatus']        == ExtendedOrderstatus::CONFIRMED)
		 						|| ($notificationDevices['orderstatus'] == ExtendedOrderstatus::READY)
		 						|| ($notificationDevices['orderstatus'] == ExtendedOrderstatus::HANDOVER)
		 						|| ($notificationDevices['orderstatus'] == ExtendedOrderstatus::REJECTED))
		 				{
		 					if ( strtolower($notificationDevices['usertype']) == "m" && $notificationDevices['notificationenabled'] == true)
		 					{
		 						$registrationid = $notificationDevices['deviceid'];
		 					}
		 					if (strtolower($notificationDevices['usertype']) == "s" && $notificationDevices['notificationenabled'] == true)
		 					{
		 						$registrationid = $notificationDevices['deviceid'];
		 					}
		 			
		 					$notificationType = "food-update";
		 				}
		 			$response = null;
		 	
		 				$requestData  = $pushNotificationSender->setPushNotificationRequest($registrationid,$message,$notificationType,$institutionId,$notificationDevices['orderid'],$institutionName,strtolower($notificationDevices['devicetype']),$notificationDevices['userid']);
		 			
		 				$response  = $pushNotificationSender->sendNotification(strtolower($notificationDevices['devicetype']), $registrationid, $requestData);
		 				
		 		
		 			if ($response){
		 				$notificationSent[] = [$notificationDevices['orderid'],$notificationDevices['orderstatus'],$notificationDevices['memberid'],$notificationDevices['usertype']];
		 				
		 			}
		 		}
	 		}
		}
		if (count($notificationSent)>0){
			 
			Yii::$app->db->createCommand()->batchInsert('ordernotificationsent', $fields,$notificationSent )->execute();
			 
		}
	}
}