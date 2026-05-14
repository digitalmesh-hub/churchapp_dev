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
        // Direct SQL query with dependant support (no stored procedure needed)
        // Note: Spouse data is stored in member table columns (spouse_firstName, spouse_pic, etc.)
        $sql = "SELECT 
            c.committeegroupid,
            cg.description as committeegroupdescription,
            cg.`order` as CommitteeTypeSortOrder,
            c.committeeid,
            c.memberid,
            c.isspouse,
            c.dependantid,
            
            -- Display name: check dependantid first, then isspouse, then member (without title - title shown separately)
            CASE 
                WHEN c.dependantid IS NOT NULL THEN 
                    CASE 
                        WHEN dep.id IS NULL THEN '[Deleted Dependant]'
                        ELSE COALESCE(dep.dependantname, '')
                    END
                WHEN c.isspouse = 1 THEN 
                    TRIM(CONCAT_WS(' ', 
                           NULLIF(m.spouse_firstName, ''), 
                           NULLIF(m.spouse_middleName, ''), 
                           NULLIF(m.spouse_lastName, '')))
                ELSE 
                    TRIM(CONCAT_WS(' ', 
                           NULLIF(m.firstName, ''), 
                           NULLIF(m.middleName, ''), 
                           NULLIF(m.lastName, '')))
            END AS membername,
            
            -- Title
            CASE 
                WHEN c.dependantid IS NOT NULL THEN COALESCE(dept.Description, '')
                WHEN c.isspouse = 1 THEN COALESCE(st.Description, '')
                ELSE COALESCE(mt.Description, '')
            END AS title,
            
            -- Image
            CASE 
                WHEN c.dependantid IS NOT NULL THEN COALESCE(dep.image, '')
                WHEN c.isspouse = 1 THEN COALESCE(m.spouse_pic, '')
                ELSE COALESCE(m.member_pic, '')
            END AS memberimage,
            
            -- Phone
            CASE 
                WHEN c.dependantid IS NOT NULL THEN COALESCE(dep.dependantmobile, '')
                WHEN c.isspouse = 1 THEN COALESCE(m.spouse_mobile1, '')
                ELSE COALESCE(m.member_mobile1, '')
            END AS memberphone,
            
            -- Email
            CASE 
                WHEN c.dependantid IS NOT NULL THEN COALESCE(m.member_email, '')
                WHEN c.isspouse = 1 THEN COALESCE(m.spouse_email, '')
                ELSE COALESCE(m.member_email, '')
            END AS memberemail,
            
            -- Relation (for dependants)
            CASE 
                WHEN c.dependantid IS NOT NULL THEN 
                    CASE 
                        WHEN dep.id IS NULL THEN ''
                        WHEN dep.relation IS NOT NULL AND dep.relation != '' THEN dep.relation
                        WHEN partner.relation = 'Son' THEN 'Daughter-in-law'
                        WHEN partner.relation = 'Son in law' THEN 'Daughter'
                        WHEN partner.relation = 'Daughter' THEN 'Son-in-law'
                        WHEN partner.relation = 'Daughter in law' THEN 'Son'
                        WHEN partner.relation = 'Father' THEN 'Mother'
                        WHEN partner.relation = 'Mother' THEN 'Father'
                        WHEN partner.relation = 'Brother' THEN 'Sister-in-law'
                        WHEN partner.relation = 'Sister' THEN 'Brother-in-law'
                        WHEN partner.relation = 'Grandfather' THEN 'Grandmother'
                        WHEN partner.relation = 'Grandmother' THEN 'Grandfather'
                        WHEN partner.relation = 'Grandson' THEN 'Granddaughter-in-law'
                        WHEN partner.relation = 'Granddaughter' THEN 'Grandson-in-law'
                        WHEN dep.dependantid IS NOT NULL THEN 'Spouse'
                        ELSE ''
                    END
                ELSE ''
            END AS relation,
            
            CASE 
                WHEN c.dependantid IS NOT NULL AND dep.id IS NOT NULL THEN 
                    TRIM(CONCAT(
                        CASE 
                            WHEN dep.relation IS NOT NULL AND dep.relation != '' THEN dep.relation
                            WHEN partner.relation = 'Son' THEN 'Daughter-in-law'
                            WHEN partner.relation = 'Son in law' THEN 'Daughter'
                            WHEN partner.relation = 'Daughter' THEN 'Son-in-law'
                            WHEN partner.relation = 'Daughter in law' THEN 'Son'
                            WHEN partner.relation = 'Father' THEN 'Mother'
                            WHEN partner.relation = 'Mother' THEN 'Father'
                            WHEN partner.relation = 'Brother' THEN 'Sister-in-law'
                            WHEN partner.relation = 'Sister' THEN 'Brother-in-law'
                            WHEN partner.relation = 'Grandfather' THEN 'Grandmother'
                            WHEN partner.relation = 'Grandmother' THEN 'Grandfather'
                            WHEN partner.relation = 'Grandson' THEN 'Granddaughter-in-law'
                            WHEN partner.relation = 'Granddaughter' THEN 'Grandson-in-law'
                            WHEN dep.dependantid IS NOT NULL THEN 'Spouse'
                            ELSE ''
                        END,
                        ' of ',
                        CASE WHEN mt.Description IS NOT NULL THEN CONCAT(mt.Description, ' ') ELSE '' END,
                        TRIM(CONCAT_WS(' ', 
                            NULLIF(m.firstName, ''), 
                            NULLIF(m.middleName, ''), 
                            NULLIF(m.lastName, '')))
                    ))
                ELSE ''
            END AS remarks,
            
            d.designationid,
            d.description,
            d.designationorder,
            c.active
        FROM committee c
        INNER JOIN committeegroup cg ON cg.committeegroupid = c.committeegroupid AND cg.active = 1
        INNER JOIN designation d ON d.designationid = c.designationid
        INNER JOIN member m ON m.memberid = c.memberid
        LEFT JOIN title mt ON m.membertitle = mt.TitleId
        LEFT JOIN title st ON m.spousetitle = st.TitleId
        LEFT JOIN dependant dep ON c.dependantid = dep.id
        LEFT JOIN title dept ON dep.titleid = dept.TitleId
        LEFT JOIN dependant partner ON partner.id = dep.dependantid
        LEFT JOIN committee_period cp ON cp.committee_period_id = c.committeeperiodid
        WHERE c.institutionid = :institutionid
            AND (c.committeeperiodid = :committeeperiodid OR :committeeperiodid IS NULL)
            AND (c.committeegroupid = :committeegroupid OR :committeegroupid IS NULL)
            AND ((DATE(cp.period_from) <= DATE(:date) OR :date IS NULL) 
                 AND (DATE(cp.period_to) >= DATE(:date) OR :date IS NULL))
            AND cp.active = 1
        ORDER BY cg.`order`, d.designationorder ASC";
        
        $command = Yii::$app->db->createCommand($sql)
            ->bindValue(':institutionid', $institutionId)
            ->bindValue(':committeeperiodid', $committeePeriod)
            ->bindValue(':committeegroupid', $committeeType)
            ->bindValue(':date', $date);
        
        $committeeMembers = $command->queryAll();
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
