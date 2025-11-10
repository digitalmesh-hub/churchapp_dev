<?php

namespace common\models\extendedmodels;

use Yii;
use yii\base\ErrorException;
use common\models\basemodels\Institution;
use common\models\basemodels\Committeegroup;

/**
 * This is the Extendedmodel class for table "committeegroup".
 *
 * @property int $committeegroupid
 * @property string $description
 * @property int $institutionid
 * @property int $active
 * @property int $order
 * @property Committee[] $committees
 * @property CommitteePeriod[] $committeePeriods
 * @property Institution $institution
 */
class ExtendedCommitteegroup extends Committeegroup
{

	/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'required'],
            [['institutionid', 'order'], 'integer'],
            [['description'], 'string', 'max' => 25],
            [['active'], 'integer', 'max' => 4],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
        ];
    }

    /**
     * To get the groups
	 * of all active committees 
	 * in an institution
     * @param unknown $institutionId
     * @return boolean
     */
	public static function getCommitteeGroup($institutionId)
    {
		try {

			$committeeGroup = Yii::$app->db->createCommand(
					"CALL get_all_committeegroup_with_periods(:institutionid)")
					->bindValue(':institutionid', $institutionId)
					->queryAll();
			return $committeeGroup;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
     * Add Committee Type
     * @param object $model
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function addCommitteeType($model)
    {
        try{
        	//find the maximum committee order
            $count = Yii::$app->db->createCommand("SELECT MAX(`order`) AS value FROM committeegroup WHERE institutionid = :institutionid")
            ->bindValue(':institutionid' , $model->institutionid)
            ->queryScalar();
            
            if($count == 0){
            	$model->order = 1;
            }
            else{
            	$model->order = $count + 1;
            }
            $model->active = 1;
            $model->save();
            if($model->getErrors()){
            	return false;
            }
            else{
            	return true;
            }
        }
        catch(ErrorException $e){
            return false;
        }
    }

    /**
     * Update Committee Type
     * @param object $model
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function updateCommitteeType($model)
    {
        try{
            $command = Yii::$app->db->createCommand("UPDATE committeegroup SET
                description = :description
                WHERE institutionid = :institutionid AND 
                committeegroupid = :committeegroupid")
            ->bindValue(':description' , $model->description)
            ->bindValue(':institutionid' , $model->institutionid)
            ->bindValue(':committeegroupid' , $model->committeegroupid);
            $command->execute();
            return true;
        } catch(ErrorException $e){
            return false;
        }
    }

    /**
     * Update Committee Type Order
     * @param object $model
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function updateCommitteeTypeOrder($groupId, $order)
    {
        try{
            $command = Yii::$app->db->createCommand("UPDATE committeegroup SET
                `order` = :order
                WHERE committeegroupid = :committeegroupid")
            ->bindValue(':order' , $order)
            ->bindValue(':committeegroupid' , $groupId);
            $command->execute();
            return true;
        }
        catch(ErrorException $e){
            return false;
        }
    }


    /**
     * get committee group list
     * @param integer $institutionId
     * @param integer $committeeType
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function getCommitteeGroupList($institutionId){
        
         try{
            $command = Yii::$app->db->createCommand("SELECT committeegroupid,description,institutionid,active,`order` FROM committeegroup WHERE institutionid = :institutionid order by `order`")
            ->bindValue(':institutionid' , $institutionId);
            $groupList = $command->queryAll();
            return $groupList;
        }
        catch(ErrorException $e){
            return false;
        }
    }
}