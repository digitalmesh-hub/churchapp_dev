<?php

namespace common\models\extendedmodels;

use Yii;
use yii\base\ErrorException;
use common\models\basemodels\Institution;
use common\models\basemodels\Designation;

/**
 * This is the model class for table "designation".
 *
 * @property int $designationid
 * @property string $description
 * @property int $designationorder
 * @property int $institutionid
 *
 * @property Committee[] $committees
 * @property Institution $institution
 * @property Institutionstaffdesignation[] $institutionstaffdesignations
 */
class ExtendedDesignation extends Designation
{
	/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'institutionid'], 'required'],
            [['description'], 'required','message'=>'Committee Designation Cannot be blank.'],
            [['designationorder', 'institutionid'], 'integer'],
            [['description'], 'string', 'max' => 100],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
        ];
    }

    /**
     * Add Committee Type
     * @param object $model
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function addCommitteeDesignation($model)
    {
        try{
        	//find the maximum committee order
            $count = Yii::$app->db->createCommand("SELECT MAX(`designationorder`) AS value FROM designation WHERE institutionid = :institutionid")
            ->bindValue(':institutionid' , $model->institutionid)
            ->queryScalar();
            
            if($count == 0){
            	$model->designationorder = 1;
            }
            else{
            	$model->designationorder = $count +1;
            }

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
     * Update Committee Designation
     * @param object $model
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function updateCommitteeDesignation($model)
    {
        try{
            $command = Yii::$app->db->createCommand("UPDATE designation SET
                description = :description
                WHERE institutionid = :institutionid AND 
                designationid = :designationid")
            ->bindValue(':description' , $model->description)
            ->bindValue(':institutionid' , $model->institutionid)
            ->bindValue(':designationid' , $model->designationid);
            $command->getRawSql();
            $command->execute();
            return true;
        }
        catch(ErrorException $e){
            return false;
        }
    }

    /**
     * Update Committee Type Order
     * @param object $model
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
    */
    public function updateCommitteeDesignationOrder($designationId, $order)
    {
        try{
            $command = Yii::$app->db->createCommand("UPDATE designation SET
                `designationorder` = :designationorder
                WHERE designationid = :designationid")
            ->bindValue(':designationorder' , $order)
            ->bindValue(':designationid' , $designationId);
            $command->execute();
            return true;
        }
        catch(ErrorException $e){
            return false;
        }
    }
}

