<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\rbac\DbManager;

/**
 * Scheduler controller
 */
class SchedulerController extends Controller
{

    /**
     * This scheduler will move 90 days old click/view count data into history table and then will delete the source data.
     *
     */
    public function actionMove2history()
    {
        //Scheduler 
    	//Run  : php5 /projects/Remember/dev/src/yii scheduler/move2history
    	
    	$intervelInDays = Yii::$app->params['move2historyInterval'];
    	$rowCount = 0;
    	$viewDelCount = 0;
    	$clickDelCount = 0;
    	
    	//Query to fetch 90 days old historic data and put into AdClickViewHistory table
		$query = "INSERT INTO AdClickViewHistory (AdDetailId, AdClickCount, AdImpressionCount, AdDateRangeStart, AdDateRangeEnd)
				  SELECT did, sum(cc), sum(vc), min(adate), max(adate)
					FROM 
					(
					SELECT 
						AdDetailId AS did
						, 0 AS cc
						, AdViewCount AS vc
						, adViewDate as adate 
					FROM AdViewCount
					UNION  
					SELECT 
						AdDetailId
						, AdClickCount
						, 0
						, AdClickDate 
					FROM AdClickCount
					) AS subq 
					WHERE DATEDIFF(NOW(), adate) >= {$intervelInDays}
					GROUP BY did, DATE(adate)
					ORDER BY did ";     

		$rowCount = Yii::$app->db->createCommand($query)->execute();
        
    	if($rowCount > 0){
    		$del_query1 = "DELETE FROM AdViewCount WHERE DATEDIFF(NOW(), adViewDate) >= {$intervelInDays}";
    		$del_query2 = "DELETE FROM AdClickCount WHERE DATEDIFF(NOW(), AdClickDate) >= {$intervelInDays}";
    		$viewDelCount = Yii::$app->db->createCommand($del_query1)->execute();
    		$clickDelCount = Yii::$app->db->createCommand($del_query2)->execute();
    	}
    	
    	$message = date('Y-m-d') . " " . $rowCount . " record(s) copied to History table. ";
    	$message .= $viewDelCount . " view record deleted. ";
    	$message .= $clickDelCount . " click record deleted. ";
    	
    	Yii::info($message,'ad_scheduler');
        
    }
    
}
