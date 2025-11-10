<?php

namespace common\models\extendedmodels;

use common\models\basemodels\Surveystatus;
use Yii;

class ExtendedSurveystatus extends Surveystatus
{
   
   /**
    * Update survey status
    * @param $token base64
    * @return boolen value
   */
   /*public function updateSurveyStatus($token)
   {
   		  try{
   			    return Yii::$app->db->createCommand("UPDATE surveystatus set isattended = 1 WHERE token = :token")
            ->bindValue(':token' , $token)->execute();  
   		  }	catch(Exception $e){
   			    return false;
   		  }
    }*/

    
  /* public function addEditSurvey($memberId, $memberType, $token, $surveyId)
   {
   		try{
   			$surveyDetails = Yii::$app->db->createCommand("SELECT surveystatusid, surveyid, memberid, token,isattended FROM surveystatus WHERE surveyid =:surveyid AND memberid = :memberid AND membertype =:membertype")
   			->bindValue(':surveyid', $surveyId)
   			->bindValue(':memberid', $memberId)
   			->bindValue(':membertype', strtolower($memberType))
        ->queryOne();
            if ($surveyDetails) {
            	return $this->updateSurveyToken($memberId, $memberType, $token, $surveyId);
            } else {
            	return $this->addSurveyStatus($memberId, $memberType, $token, $surveyId);
            }
   		}	catch(Exception $e){
        yii::error($e->getMessage());
   			return false;
   		}
    }

    
   	protected function updateSurveyToken($memberId, $memberType, $token, $surveyId)
    {
   		try{
   			return Yii::$app->db->createCommand("UPDATE surveystatus SET token= :token WHERE surveyid = :surveyid AND memberid = :memberid AND membertype = :membertype")
            ->bindValue(':token', $token)
            ->bindValue(':surveyid', $surveyId)
   			    ->bindValue(':memberid', $memberId)
   			    ->bindValue(':membertype', strtolower($memberType))
            ->execute();    		
          }	catch(Exception $e){
   			return false;
   		}
    }

   	protected function addSurveyStatus($memberId, $memberType, $token, $surveyId)
    {
   		try{
   			 return Yii::$app->db->createCommand("INSERT INTO surveystatus (surveyid,memberid,membertype,token) VALUES (:surveyid, :memberid, :membertype, :token)")
            ->bindValue(':surveyid', $surveyId)
       			->bindValue(':memberid', $memberId)
       			->bindValue(':membertype', strtolower($memberType))
       			->bindValue(':token', $token)
            ->execute();     
   		}	catch(Exception $e){
   			return false;
   		}
    }*/
}
