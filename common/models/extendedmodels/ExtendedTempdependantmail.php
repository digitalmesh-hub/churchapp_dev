<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Tempdependantmail; 

/**
 * This is the model class for table "tempdependantmail".
 *
 * @property int $id
 * @property int $tempmemberid
 * @property int $dependantid
 * @property int $titleid
 * @property string $dependantname
 * @property string $dob
 * @property string $relation
 * @property string $weddinganniversary
 * @property int $spousedependantid
 * @property int $ismarried
 * @property int $isapproved
 * @property string $tempimage
 * @property string $tempimagethumbnail
 * @property string $tempimageid
 *
 * @property Dependant $dependant
 * @property Tempmember $tempmember
 * @property Title $title
 */
class ExtendedTempdependantmail extends Tempdependantmail
{
   /**
    * To update temp mail
    */
	public static function updateTempImageMailWithDependantId($dependantImage,$dependantImageThumbnail,$dependantIds)
	{
		try {
			$sql = "Update tempdependantmail set tempimage=:dependantimage,tempimagethumbnail=:dependantimagethumbnail where dependantid=:dependantid";
			$updateTempMail = Yii::$app->db->createCommand($sql)
								->bindValue(':dependantimage', $dependantImage)
								->bindValue(':dependantimagethumbnail', $dependantImageThumbnail)
								->bindValue(':dependantid', $dependantIds)
								->execute();
			if($updateTempMail)
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
	/**
	 * To update temp dependant spouse mail
	 */
	public static function updateTempDependantSpouseMailImage($dependantSpouseImage,$dependantSpouseImageThumbnail,$dependantIds)
	{
		try {
			$sql = "Update tempdependantmail set tempimage=:dependantimage,tempimagethumbnail=:dependantimagethumbnail where spousedependantid=:dependantid";
			$updateTempMail = Yii::$app->db->createCommand($sql)
								->bindValue(':dependantimage', $dependantSpouseImage)
								->bindValue(':dependantimagethumbnail', $dependantSpouseImageThumbnail)
								->bindValue(':dependantid', $dependantIds)
								->execute();
			if($updateTempMail)
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
