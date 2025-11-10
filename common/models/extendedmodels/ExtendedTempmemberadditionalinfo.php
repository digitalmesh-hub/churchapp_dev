<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Tempmemberadditionalinfo;
/**
 * This is the model class for table "tempmemberadditionalinfo".
 *
 * @property int $id
 * @property int $memberid
 * @property string $temptagcloud
 * @property int $isapproved
 *
 * @property Member $member
 */
class ExtendedTempmemberadditionalinfo extends Tempmemberadditionalinfo
{
	/**
	 * to get the tag cloud
	 */
	public static function getTagCloud($memberId)
	{
		try {
			
			$tagCloud = Yii::$app->db->createCommand("
						SELECT temptagcloud FROM tempmemberadditionalinfo where isapproved=0 and memberid=:memberid")
						->bindValue(':memberid', $memberId)
						->queryOne();
			return $tagCloud;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To update the approved bit
	 */
	public static function updateApprovedBit($memberId)
	{
		try {
			$sql = "update tempmemberadditionalinfo set isapproved=1 where memberid=:memberid";
			$updateBit = Yii::$app->db->createCommand($sql)
						->bindValue(':memberid', $memberId)
						->execute();
			if($updateBit)
			{
				return true;
			}
			else{
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
}
