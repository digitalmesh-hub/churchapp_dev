<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Staffdesignation;

/**
 * This is the model class for table "staffdesignation".
 *
 * @property int $staffdesignationid
 * @property string $designation
 * @property int $institutionid
 * @property bool $active
 * @property string $createddatetime
 * @property int $createdby
 * @property string $modifieddatetime
 * @property int $modifiedby
 *
 * @property Institution $institution
 * @property Usercredentials $createdby0
 * @property Usercredentials $modifiedby0
 */
class ExtendedStaffdesignation extends Staffdesignation
{
	/**
	 * To get the staff list
	 */
	public static function getStaffList($institusionId){
	
		$sql = "SELECT `staffdesignationid`,`designation` FROM `staffdesignation` where `institutionid`=:institusionId AND active=1";
		
		try{
			$command = Yii::$app->db->createCommand($sql)
		                ->bindValue(':institusionId' , $institusionId)
    				->queryAll();
    		return $command;
		}
		catch(ErrorException $e){
			return false;
		}
		
	} 
}

