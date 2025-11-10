<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\extendedmodels\ExtendedEventsentdetails;

class NotificationCleanerController extends Controller
{
	/**
	 * To delete the sent details from the table
	 */
	public function actionDeleteSentDetails(){
		
		ExtendedEventsentdetails::deleteEventSentDetails();
	}
	/**
	 * To delete the successfull event sent details
	 */
	public function actionDeleteSuccessfullEventSentDetails(){
		
		ExtendedEventsentdetails::deleteSuccessfullSentDetails();
	} 
}