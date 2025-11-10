<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\BirthdayAnniversarySeendetails;
use Exception;

/**
 * This is the model class for table "birthday_anniversary_seendetails".
 *
 * @property int $id
 * @property int $userid
 * @property int $institutionid
 * @property int $viewedstatus
 * @property string $vieweddate
 * @property string $type
 *
 * @property Institution $institution
 * @property Usercredentials $user
 */
class ExtendedBirthdayAnniversarySeendetails extends BirthdayAnniversarySeendetails
{
	/**
	 * To set birthday anniversary seen details
	 * @param unknown $userId
	 * @param unknown $institutionId
	 * @param unknown $type
	 * @param unknown $vieweddate
	 * @return \yii\db\false|boolean
	 */
	public static function getBirthdayAnniversaryCount($userId,$institutionId,$type,$vieweddate)
	{
		try {
			$birthdaySeenCount = Yii::$app->db->createCommand('select count(userid) as count from birthday_anniversary_seendetails
					where userid=:userid and institutionid=:institutionid and type=:type 
					and date(vieweddate)=date(:vieweddate) and viewedstatus=1')
					->bindValue(':userid',$userId)
					->bindValue(':institutionid',$institutionId)
					->bindValue(':type',$type)
					->bindValue(':vieweddate',$vieweddate)
					->queryOne();	
			return $birthdaySeenCount;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To set birthday anniversary as seen
	 * @param unknown $userId
	 * @param unknown $institutionId
	 * @param unknown $viewedStatus
	 * @param unknown $viewedDate
	 * @param unknown $type
	 * @return number|boolean
	 */
	public static function setBirthdayAnniversarySeen($userId,$institutionId,$viewedStatus,$viewedDate,$type)
	{
		try {
			$setBirthdayAnniversarySeenDetails = Yii::$app->db->createCommand('
					insert into birthday_anniversary_seendetails(userid,institutionid,viewedstatus,vieweddate,type)
					values(:userid,:institutionid,:viewedstatus,:vieweddate,:type)')
								->bindValue(':userid',$userId)
								->bindValue(':institutionid',$institutionId)
								->bindValue(':viewedstatus',$viewedStatus)
								->bindValue(':vieweddate',$viewedDate)
								->bindValue(':type',$type)
								->execute();
			return $setBirthdayAnniversarySeenDetails;
			
		} catch (Exception $e) {
			return false;
		}
	}
}
