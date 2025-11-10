<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Tempdependant;


/**
 * This is the model class for table "tempdependant".
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
 * @property Member $tempmember
 * @property Dependant $dependant
 * @property Title $title
 */
class ExtendedTempdependant extends Tempdependant
{
	/**
	 * To get temp dependant details
	 */
	public static function getEditMemberTempDependants($memberId)
	{
		try {
			$dependantDetails = Yii::$app->db->createCommand("
					CALL get_temp_dependants(:memberid)")
					->bindValue(':memberid', $memberId)
					->queryAll();
			return $dependantDetails;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * update dependant
	 * profile picture
	 */
	public static function updateDependentSpouseImage($memberId,$dependantId,$pendingImageThumbnail,$pendingImageURL,$type)
	{
		try {
			if($type == 'dependant') {
				$sql = 'Update tempdependant set tempimage=:pendingimage,tempimagethumbnail=:pendingimagethumbnail 
						where dependantid=:dependantid and isapproved=0';
			} elseif ($type == 'dependantspouse') {
				$sql = 'Update tempdependant set tempimage=:pendingimage,tempimagethumbnail=:pendingimagethumbnail
						where spousedependantid=:dependantid and isapproved=0';
			}
			Yii::$app->db->createCommand($sql)
				->bindValue(':pendingimage', $pendingImageURL)
				->bindValue(':pendingimagethumbnail', $pendingImageThumbnail)
				->bindValue(':dependantid', $dependantId)
				->execute();
			return true;
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * delete dependant
	 */
	public static function deleteTempDependantById($memberId,$dependantIds)
	{
		try {
			
			$sql = 'DELETE FROM tempdependant WHERE tempmemberid=:tempmemberid and dependantid=:dependantid and isapproved=0';
			Yii::$app->db->createCommand($sql)
			->bindValue(':tempmemberid', $memberId)
			->bindValue(':dependantid', $dependantIds)
			->execute();
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * delete temp dependant
	 * spouse
	 */
	public static function deleteTempDependantSpouse($memberId,$dependantIds)
	{
		try {
			$sql = 'DELETE FROM tempdependant WHERE tempmemberid=:tempmemberid and spousedependantid=:dependantid and isapproved=0';
			Yii::$app->db->createCommand($sql)
			->bindValue(':tempmemberid', $memberId)
			->bindValue(':dependantid', $dependantIds)
			->execute();
				
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * adding dependants details 
	 * to the temp table
	 */
	public static function addTempDependant($dependant,$memberId)
	{
		try {
			$sql = 'INSERT INTO tempdependant(tempmemberid,dependantid,titleid,dependantname,dependantmobilecountrycode,dependantmobile,dob,relation,weddinganniversary,
							ismarried,tempimage,tempimageid) 
						VALUES 
					(:tempmemberid,:tempdependantid,:tempdependanttitleid,:tempdependantname,:dependantmobilecountrycode,:dependantmobile,:tempdob,
						:temprelation,:tempweddinganniversary,:tempdependantmaritalstatus,:tempdependantimage,:tempid)';
			$tempDob = (!empty($dependant['dependantDob'])) ? date(yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($dependant['dependantDob'])) : null;
			$tempWeddingDate = (!empty($dependant['dependantWeddingAnniversary'])) ? date(yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($dependant['dependantWeddingAnniversary'])) : null;
			Yii::$app->db->createCommand($sql)
			->bindValue(':tempmemberid', $memberId)
			->bindValue(':tempdependantid', $dependant['dependantId'])
			->bindValue(':tempdependanttitleid', $dependant['dependantTitleId'])
			->bindValue(':tempdependantname', $dependant['dependantName'])
			->bindValue(':dependantmobile', $dependant['dependantMobile'] ?? NULL)
			->bindValue(':dependantmobilecountrycode', $dependant['dependantMobileCountryCode'] ?? NULL)
			->bindValue(':tempdob', $tempDob)
			->bindValue(':temprelation', $dependant['dependantRelation'])
			->bindValue(':tempweddinganniversary', $tempWeddingDate)
			->bindValue(':tempdependantmaritalstatus', $dependant['dependantMaritalStatus'])
			->bindValue(':tempdependantimage', $dependant['dependantImage'])
			->bindValue(':tempid', $dependant['dependantId'])
			->execute();
			return true;
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * adding dependant spouse details
	 * to the temp table
	 */
	public static function addTempDependantSpouse($dependant,$memberId)
	{
		try {
			$sql = 'INSERT INTO tempdependant(tempmemberid,dependantid,titleid,dependantname,dependantmobilecountrycode,dependantmobile,dob,spousedependantid,tempimage,tempimageid) 
                    VALUES
					(:tempmemberid,:tempdependantid,:tempdependantspousetitleid,:tempdependantspousename,
					:dependantmobilecountrycode,:dependantmobile,
					:tempdeopendantspousedob,:tempspousedependantid,:tempdependantspouseimgae,:tempid)';
			
			$spouseDob = (!empty($dependant['dependantSpouseDateOfBirth'])) ? date(yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($dependant['dependantSpouseDateOfBirth'])) : null;
			Yii::$app->db->createCommand($sql)
			->bindValue(':tempmemberid', $memberId)
			->bindValue(':tempdependantid', $dependant['dependantId'])
			->bindValue(':tempdependantspousetitleid', $dependant['dependantSpouseTitleId'])
			->bindValue(':tempdependantspousename', $dependant['dependantSpouseName'])
			->bindValue(':dependantmobilecountrycode', $dependant['dependantSpouseMobileCountryCode'] ?? NULL)
			->bindValue(':dependantmobile', $dependant['dependantSpouseMobile'] ?? NULL)
			->bindValue(':tempdeopendantspousedob', $spouseDob)
			->bindValue(':tempspousedependantid', $dependant['dependantSpouseId'])
			->bindValue(':tempdependantspouseimgae', $dependant['dependantSpouseImage'])
			->bindValue(':tempid', $dependant['dependantId'])
			->execute();
			
			return true;
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * Whether temp dependant exist
	 */
	public static function isTempDependantExist($memberId,$dependantIds)
	{
		try {
			$sql = 'SELECT dependantid as id FROM tempdependant where dependantid=:dependantid and tempmemberid=:memberid AND isapproved=0';
			$qry = Yii::$app->db->createCommand($sql)
					->bindValue(':dependantid', $dependantIds)
					->bindValue(':memberid', $memberId)
					->queryOne();
			return $qry;
			
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * updating temp dependant
	 * details
	 */
	public static function updateTempDependent($dependant)
	{
		try {
			
			$sql = 'UPDATE tempdependant SET titleid=:titleid, dependantname=:dependantname,dependantmobilecountrycode=:dependantmobilecountrycode,dependantmobile=:dependantmobile,dob=:dob,relation=:relation,
					weddinganniversary=:weddinganniversary,ismarried=:ismarried
					WHERE dependantid=:dependantid and tempmemberid=:memberid and isapproved=0';
			$dob = (!empty($dependant['dependantDob'])) ? date(yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($dependant['dependantDob'])) : null;
			$weddingAnniversary = (!empty($dependant['dependantWeddingAnniversary'])) ? date(yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($dependant['dependantWeddingAnniversary'])) : null;
			$updateTempDep = Yii::$app->db->createCommand($sql)
								->bindValue(':titleid', $dependant['dependantTitleId'])
								->bindValue(':dependantname', $dependant['dependantName'])
								->bindValue(':dependantmobilecountrycode', $dependant['dependantMobileCountryCode'] ?? NULL)
								->bindValue(':dependantmobile', $dependant['dependantMobile'] ?? NULL)
								->bindValue(':dob', $dob)
								->bindValue(':relation', $dependant['dependantRelation'])
								->bindValue(':weddinganniversary', $weddingAnniversary)
								->bindValue(':ismarried', $dependant['dependantMaritalStatus'])
								->bindValue(':dependantid', $dependant['dependantId'])
								->bindValue(':memberid', $dependant['memberid'])
								->execute();
			if($updateTempDep)
			{
				return true;
			}
			else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * whether temp dependant
	 * spouse exist
	 */
	public static function isTempDependantSpouseExist($memberId,$dependantIds)
	{
		try {
			$sql = 'SELECT dependantid as id FROM tempdependant where spousedependantid=:dependantid and tempmemberid=:memberid AND isapproved=0';
			$isSpouseExist = Yii::$app->db->createCommand($sql)
							->bindValue(':dependantid', $dependantIds)
							->bindValue(':memberid', $memberId)
							->queryOne();
			return $isSpouseExist;
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * update temp dependant spouse
	 */
	public static function updateTempDependantSpouse($dependant)
	{
		try {
			$sql = 'UPDATE tempdependant SET titleid=:titleid, 
					dependantname=:dependantname,dependantmobilecountrycode=:dependantmobilecountrycode, dependantmobile=:dependantmobile, dob=:dob WHERE spousedependantid=:dependantid and tempmemberid=:memberid and isapproved=0';
			$spouseDob = (!empty($dependant['dependantSpouseDateOfBirth'])) ? date(Yii::$app->params['dateFormat']['sqlDandTFormat'],strtotimeNew($dependant['dependantSpouseDateOfBirth'])) : null;
			$updateSpouse = Yii::$app->db->createCommand($sql)
							->bindValue(':titleid', $dependant['dependantSpouseTitleId'])
							->bindValue(':dependantname', $dependant['dependantSpouseName'])
							->bindValue(':dependantmobilecountrycode', $dependant['dependantSpouseMobileCountryCode'] ?? NULL)
							->bindValue(':dependantmobile', $dependant['dependantSpouseMobile'] ?? NULL)
							->bindValue(':dob', $spouseDob)
							->bindValue(':dependantid', $dependant['dependantId'])
							->bindValue(':memberid', $dependant['memberid'])
							->execute();
			if($updateSpouse)
			{
				return true;
			}
			else 
			{
				return false;
			}
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * Update temp dependant image
	 */
	public static function updateTempDependantImage($dependantImage,$dependantImageThumbnail,$dependantIds)
	{
		try {
			$sql = "Update tempdependant set tempimage=:dependantimage,tempimagethumbnail=:dependantimagethumbnail where dependantid=:dependantid and isapproved=0";
			$updateTempDepImage = Yii::$app->db->createCommand($sql)
									->bindValue(':dependantimage', $dependantImage)
									->bindValue(':dependantimagethumbnail', $dependantImageThumbnail)
									->bindValue(':dependantid', $dependantIds)
									->execute();
			if($updateTempDepImage)
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
	 * To update temp dependant spouse image
	 */
	public static function updateTempDependentSpouseImage($dependantSpouseImage,$dependantSpouseImageThumbnail,$dependantIds)
	{
		try {
				$updateTempDependantSpouseImage = Yii::$app->db->createCommand("Update tempdependant set tempimage=:dependantimage,tempimagethumbnail=:dependantimagethumbnail where spousedependantid=:dependantid and isapproved=0")
											->bindValue(':dependantimage', $dependantSpouseImage)
											->bindValue(':dependantimagethumbnail', $dependantSpouseImageThumbnail)
											->bindValue(':dependantid', $dependantIds)
											->execute();
				if($updateTempDependantSpouseImage)
				{
					return true;
				}
				else
				{
					return false;
				}
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To update is approved bit
	 */
	public static function updateIsApprovedBit($memberId)
	{
		try {
			$sql = "update tempdependant set isapproved =1 where tempmemberid=:memberid";
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
	/**
	 * To get temp dependant details
	 */
	public static function getTempDependants($memberId)
	{
		try {
			$dependantDetails = Yii::$app->db->createCommand("
					CALL get_temp_dependants_mail(:memberid)")
					->bindValue(':memberid', $memberId)
					->queryAll();
			return $dependantDetails;
		} catch (Exception $e) {
			return false;
		}
	}
}
