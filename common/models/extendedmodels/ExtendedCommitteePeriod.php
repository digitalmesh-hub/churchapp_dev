<?php

namespace common\models\extendedmodels;

use Yii;
use yii\base\ErrorException;
use common\models\basemodels\Institution;
use common\models\basemodels\Committeegroup;
use common\models\basemodels\CommitteePeriod;
/**
 * This is the model class for table "committee_period".
 *
 * @property int $committee_period_id
 * @property string $period_from
 * @property string $period_to
 * @property int $active
 * @property int $institutionid
 * @property string $createddatetime
 * @property int $committeegroupid
 *
 * @property Committee[] $committees
 * @property Committeegroup $committeegroup
 * @property Institution $institution
 */
class ExtendedCommitteePeriod extends CommitteePeriod
{
	

	/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        	[['period_from'], 'required', 'message' => 'Start Date cannot be blank'],
        	[['period_to'], 'required', 'message' => 'End Date cannot be blank'],
        	[['committeegroupid'], 'required' , 'message' => 'Committee cannot be blank'],
            [['active', 'institutionid', 'createddatetime','committeegroupid'], 'required'],
            [['period_from', 'period_to', 'createddatetime'], 'safe'],
            [['institutionid', 'committeegroupid'], 'integer'],
            [['active'], 'integer', 'max' => 4],
            [['committeegroupid'], 'exist', 'skipOnError' => true, 'targetClass' => Committeegroup::className(), 'targetAttribute' => ['committeegroupid' => 'committeegroupid']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
        ];
    }
    /**
     * To get the periods
     * of all active committees
     * in an institution
     * @param unknown $institutionId
     * @return boolean
     */
    public static function getCommittePeriod($institutionId)
    {
    	try {
    
    		$committeePeriod = Yii::$app->db->createCommand(
    				"CALL getallactive_committee_period_group(:institutionid)")
    				->bindValue(':institutionid', $institutionId)
    				->queryAll();
    				return $committeePeriod;
    
    					
    	} catch (\Exception $e) {
    		return false;
    	}
    }

    /**
     * check committee period exist or not
     * @param object $model
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function checkCommitteePeriodExist($model, $isUpdate)
    {
        try{
            if($isUpdate){
                $command = Yii::$app->db->createCommand("SELECT committee_period_id,period_from,period_to,active FROM committee_period WHERE institutionid = :institutionid AND 
                committeegroupid = :committeegroupid AND committee_period_id !=:committee_period_id")
                ->bindValue(':institutionid' , $model->institutionid)
                ->bindValue(':committee_period_id' ,$model->committee_period_id)
                ->bindValue(':committeegroupid' , $model->committeegroupid);
                $periods = $command->queryAll();
            }
            else{
                $command = Yii::$app->db->createCommand("SELECT * FROM committee_period WHERE institutionid = :institutionid AND 
                committeegroupid = :committeegroupid AND active=1")
                ->bindValue(':institutionid' , $model->institutionid)
                ->bindValue(':committeegroupid' , $model->committeegroupid);
                $periods = $command->queryAll();
            }
            

//             $startDate = \DateTime::createFromFormat(Yii::$app->params['dateFormat']['sqlDateFormat'], 
//             			$model->period_from);
            $startDate = strtotimeNew($model->period_from);
//             $endDate = \DateTime::createFromFormat(Yii::$app->params['dateFormat']['sqlDateFormat'], 
//             			$model->period_to);
            $endDate = strtotimeNew($model->period_to);
            if($periods && $periods != null){
            	foreach($periods as $index){
//             		$period_from = \DateTime::createFromFormat(Yii::$app->params['dateFormat']['sqlDandTFormat'], 
//             			$index['period_from']);
                    $period_from = strtotimeNew($index['period_from']);
//             		$period_to = \DateTime::createFromFormat(Yii::$app->params['dateFormat']['sqlDandTFormat'], 
//             			$index['period_to']);
            		$period_to = strtotimeNew($index['period_to']);
            		if(($period_to >= $startDate && $period_from <= $startDate) || ($period_to >= $endDate && $period_from <= $endDate)){
            				return false;
            		}
            	}
            }
            return true;
        }
        catch(ErrorException $e){
            return false;
        }
    }

     /**
     * get all committee periods
     * @param integer $institutionId
     * @param integer $isActive
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function getAllCommitteePeriod($institutionid)
    {
    	try{
	        $periodResult = Yii::$app->db->createCommand("CALL getall_committee_period_group(:institutionid)")
	        ->bindValue(':institutionid' , $institutionid)
	        ->queryAll();
	        return $periodResult;
	    }
	    catch(ErrorException $e){
	    	return false;
	    }
    }


     /**
     * check committee period exist or not(update)
     * @param object $model
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function checkCommitteePeriodExistForUpdate($model)
    {
        try{
            $command = Yii::$app->db->createCommand("SELECT committee_period_id,period_from,period_to,active FROM committee_period WHERE institutionid = :institutionid AND 
                committeegroupid = :committeegroupid AND committee_period_id=:committee_period_id")
            ->bindValue(':institutionid' , $model->institutionid)
            ->bindValue(':committee_period_id' ,$model->committee_period_id)
            ->bindValue(':committeegroupid' , $model->committeegroupid);
            $periods = $command->queryAll();
            return $periods;
        }
        catch(ErrorException $e){
            return false;
        }
    }

    /**
     * get committee members
     * @param integer $institutionId
     * @param integer $isActive
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function getCommitteeMembers($committeeType, $committeePeriod, $institutionId, $date)
    {   
        
        $committeeMembers = Yii::$app->db->createCommand('CALL get_committee_detailstolist(:institutionid, :committeeperiodid, :committeegroupid, :date)')
        ->bindValue(':institutionid' , $institutionId)
        ->bindValue(':committeeperiodid', $committeePeriod)
        ->bindValue(':committeegroupid', $committeeType)
        ->bindValue(':date', $date)
        ->queryAll();
        return $committeeMembers;
    }

    /**
     * get committee period list
     * @param integer $institutionId
     * @param integer $committeeType
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function getCommitteePeriodList($institutionId, $committeeType){
        
         try{
            // select committee_period_id,period_from,period_to,active from committee_period where institutionid=@institutionid  and committeegroupid= @committee_group_id and active=1;
            $command = Yii::$app->db->createCommand("SELECT committee_period_id,period_from,period_to,active FROM committee_period WHERE institutionid = :institutionid  AND committeegroupid = :committee_group_id AND active=1")
            ->bindValue(':institutionid' , $institutionId)
            ->bindValue(':committee_group_id' , $committeeType);
            $periodList = $command->queryAll();
            return $periodList;
        }
        catch(ErrorException $e){
            return false;
        }
    }
}
