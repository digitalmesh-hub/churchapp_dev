<?php

namespace common\models\extendedmodels;

use Yii;
use yii\base\ErrorException;
use common\models\basemodels\Survey;

/**
 * This is the model class for table "survey".
 *
 * @property int $surveyid
 * @property string $description
 * @property int $institutionid
 * @property int $active
 * @property string $createddatetime
 * @property int $createdby
 * @property string $modifieddatetime
 * @property int $modifiedby
 *
 * @property Usercredentials $createdby0
 * @property Institution $institution
 * @property Usercredentials $modifiedby0
 * @property Surveystatus[] $surveystatuses
 */
class ExtendedSurvey extends Survey
{
   /**
    *Get surveys
    * @param $memberId int
    * @param $userType int
    * @param $institutionId int
    * @return $surveys array
   */
    public static function getSurveys($memberId, $userType, $institutionId)
    {
        try {
            $surveys = Yii::$app->db->createCommand(
                    "CALL get_surveys(:memberid, :membertype, :institutionid)")
                    ->bindValue(':memberid', $memberId)
                    ->bindValue(':membertype', $userType)
                    ->bindValue(':institutionid', $institutionId )
                    ->queryAll();
            if(!empty($surveys)){
                return $surveys;
            }
            else{
                return true;
            }
        } 
        catch(ErrorException $e) {
            return false;
        }
    }
}
