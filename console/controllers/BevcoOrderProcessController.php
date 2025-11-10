<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\extendedmodels\ExtendedBevcoOrder;

class BevcoOrderProcessController extends Controller
{
	public function actionProcess()
	{
		$this->processExpiredOrder();
	}
	
	protected function processExpiredOrder()
	{
		$sql1 = "UPDATE bevco_order bo
        		JOIN
    				bevco_slots bs ON bs.id = bo.slot_id 
				SET 
    				bo.status = :status_expired
				WHERE
    				CAST(CONCAT(bo.order_date, ' ', bs.end_time) AS DATETIME) < NOW()
        		AND bo.status = :status_placed";

        \Yii::$app->db->createCommand($sql1)->bindValues([
        	':status_expired' => ExtendedBevcoOrder::STATUS_EXPIRED,
        	':status_placed' => ExtendedBevcoOrder::STATUS_PLACED])
        ->execute();
	}
}