<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Dependant;
use common\models\extendedmodels\ExtendedTitle;
use common\models\extendedmodels\ExtendedMember;
use Exception;

/**
 * This is the model class for table "dependant".
 *
 * @property int $id
 * @property int $memberid
 * @property int $titleid
 * @property string $dependantname
 * @property string $dob
 * @property string $relation
 * @property int $dependantid
 * @property string $weddinganniversary
 * @property int $ismarried
 * @property string $image
 * @property string $thumbnailimage
 * @property string $dependantmobile
 * @property string $dependantmobilecountrycode
 *
 * @property Title $title
 * @property Member $member
 * @property Tempdependant[] $tempdependants
 * @property Tempdependantmail[] $tempdependantmails
 */
class ExtendedDependant extends Dependant
{
	
	/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
           // [['memberid'], 'required'],
            [['memberid', 'titleid', 'dependantid', 'ismarried'], 'integer'],
            [['dob', 'weddinganniversary'], 'safe'],
            [['dependantname'], 'string', 'max' => 50],
            [['relation'], 'string', 'max' => 45],
        	[['tempmemberid'], 'string', 'max' => 15],
            [['image', 'thumbnailimage'], 'string', 'max' => 500],
			[['dependantmobilecountrycode'], 'string', 'max' => 4],
			[['dependantmobile'], 'string', 'max' => 13],
            [['titleid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedTitle::className(), 'targetAttribute' => ['titleid' => 'TitleId']],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedMember::className(), 'targetAttribute' => ['memberid' => 'memberid']],
        ];
    }
	/**
	 * To get the details of dependants
	 * @param unknown $memberId
	 * @param string $isService
	 * @return mixed
	 */
	
 	public function getDependants($memberId, $isService=false)
 	{
 		try {
 			$memberDepents = Yii::$app->db->createCommand(
 					"CALL get_dependants(:memberId)")
 					->bindValue(':memberId' , $memberId )
 					->queryAll();
 			if ($isService) {
	 			if (!empty($memberDepents)) {
	 				return $memberDepents;
	 			} else {
	 				return true;
	 			}
	 		} else {
 				return $memberDepents;
 			}
 		} catch (Exception $e) {
 			// Log the error for debugging
 			Yii::error('Failed to get dependants for member ' . $memberId . ': ' . $e->getMessage(), __METHOD__);
 			return false;
 		}
 	}
 	
 	/**
 	 * To get the details of members who has no dependants
 	 * @param unknown $memberId
 	 * @return boolean
 	 */
 	public function getMemberNotDependants($memberId){
 			
 		try {
 			
 			$memberDepents = Yii::$app->db->createCommand(
 					"CALL get_member_not_dependants(:memberId)")
 					->bindValue(':memberId' , $memberId )
 					->queryAll();
 			
 			return $memberDepents;
 			
 				
 		} catch (Exception $e) {
			yii::error("ExtendedDependant:getMemberNotDependants::".$e->getMessage());
 			return false;
 		}
 	}
 	/**
 	 * To update the member id
 	 * @param unknown $dependantIds
 	 * @param unknown $memberId
 	 * @return boolean
 	 */
 	public function updateMemberId($dependantIds,$memberId){
 		
 		try{
 			$sql = "update dependant set memberid = ".$memberId ."  where id in ( ".$dependantIds.")";
 		
 			$memberDepents = Yii::$app->db->createCommand($sql)->execute();
 			return true;
 		}catch (Exception $e) {
 			return false;
 		}
 		
 	}
 	/**
 	 * To delete dependant details
 	 * @param unknown $memberId
 	 * @param unknown $dependantIds
 	 * @return boolean
 	 */
 	public static function deleteDependantByDependantId($memberId,$dependantIds)
 	{
 		try {
 			$sql = 'DELETE FROM dependant WHERE memberid=:tempmemberid and id=:dependantid';
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
 	 * To delete dependant spouse
 	 * @param unknown $memberId
 	 * @param unknown $dependantIds
 	 * @return boolean
 	 */
 	public static function deleteDependantSpouseByDependantId($memberId, $dependantIds)
 	{
 		try {
 			$sql = 'DELETE FROM dependant WHERE memberid=:tempmemberid and dependantid=:dependantid';
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
 	 * To check whether a user exist
 	 * @param unknown $memberId
 	 * @param unknown $dependantIds
 	 * @return \yii\db\false|boolean
 	 */
 	public static function isDependantExist($memberId,$dependantIds)
 	{
 		try {
 			
 			$sql = 'SELECT id as id FROM dependant where id=:dependantid and memberid=:memberid';
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
 	 * Update dependant
 	 * @param unknown $dependant
 	 * @return boolean
 	 */
 	public static function updateDependants($dependant)
 	{
 		try {
 			$sql = 'UPDATE dependant SET titleid=:titleid, 
 					dependantname=:dependantname,dependantmobilecountrycode=:dependantmobilecountrycode,dependantmobile=:dependantmobile,dob=:dob,relation=:relation,weddinganniversary=:weddinganniversary,ismarried=:ismarried 
 					WHERE id=:id';

 			$dob = (!empty($dependant['dependantDob'])) ? date(yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($dependant['dependantDob'])) : null;
 			$weddingAnniversary =  (!empty($dependant['dependantWeddingAnniversary'])) ? date(yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($dependant['dependantWeddingAnniversary'])) : null;
 			$updateDependant = Yii::$app->db->createCommand($sql)
					 			->bindValue(':titleid', $dependant['dependantTitleId'])
					 			->bindValue(':dependantname', $dependant['dependantName'])
								->bindValue(':dependantmobilecountrycode', $dependant['dependantMobileCountryCode'] ?? NULL)
								->bindValue(':dependantmobile', $dependant['dependantMobile'] ?? NULL)
					 			->bindValue(':dob', $dob)
					 			->bindValue(':relation', $dependant['dependantRelation'])
					 			->bindValue(':weddinganniversary', $weddingAnniversary)
					 			->bindValue(':ismarried', $dependant['dependantMaritalStatus'])
					 			->bindValue(':id', $dependant['dependantId'])
					 			->execute();
 			if($updateDependant)
 			{
 				return true;
 			}
 			else 
 			{
 				return false;
 			}
 			
 			return true;
 		} catch (Exception $e) {
 			return false;
 		}
 	}
	 /**
	  *  Add dependant
	  * @param unknown $dependant
	  * @param unknown $memberId
	  * @return string|boolean
	  */
 	public static function addDependants($dependant,$memberId)
 	{
 		try {
 			$sql = 'INSERT INTO dependant(memberid,titleid,dependantname,dependantmobilecountrycode, dependantmobile, dob,relation,weddinganniversary,ismarried,dependantid,tempdependantId) 
 					values
 					(:memberid,:titleid,:dependantname,:dependantmobile,:dependantmobilecountrycode, :dob,:relation,:weddinganniversary,:ismarried,:dependantid,:tempdependantId)';
 			$dob = (!empty($dependant['dependantDob'])) ? date(yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($dependant['dependantDob'])) : null;
 			$weddingAnniversary = (!empty($dependant['dependantWeddingAnniversary'])) ? date(yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($dependant['dependantWeddingAnniversary'])) : null;
 			$addDependant = Yii::$app->db->createCommand($sql)
				 			->bindValue(':memberid', $memberId)
				 			->bindValue(':titleid', $dependant['dependantTitleId'])
				 			->bindValue(':dependantname', $dependant['dependantName'])
							->bindValue(':dependantmobile', $dependant['dependantMobile'] ?? null)
							->bindValue(':dependantmobilecountrycode', $dependant['dependantmobilecountrycode'] ?? null)
				 			->bindValue(':dob', $dob)
				 			->bindValue(':relation', $dependant['dependantRelation'])
				 			->bindValue(':weddinganniversary', $weddingAnniversary) 
				 			->bindValue(':ismarried', $dependant['dependantMaritalStatus'])
				 			->bindValue(':dependantid', $dependant['dependantId'])
				 			->bindValue(':tempdependantId', isset($dependant['tempdependantId'])?$dependant['tempdependantId']:null) 
				 			->execute();
 			if($addDependant)
 			{
 				$lastDepId = Yii::$app->db->getLastInsertID();
 				return $lastDepId;
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
 	 * To check whether dependant spouse exist or not
 	 * @param unknown $memberId
 	 * @param unknown $dependantIds
 	 * @return \yii\db\false|boolean
 	 */
 	public static function isDependantSpouseExist($memberId,$dependantIds)
 	{
 		try {
 			$sql = 'SELECT id as id FROM dependant where dependantid=:dependantid and memberid=:memberid';
 			$isExistSpouse = Yii::$app->db->createCommand($sql)
				 			->bindValue(':dependantid', $dependantIds)
				 			->bindValue(':memberid', $memberId)
				 			->queryOne();
 			return $isExistSpouse;
 		} catch (Exception $e) {
 			return false;
 		}
 	}
 	/**
 	 * To add dependant spouse
 	 * @param unknown $dependant
 	 * @return string|boolean
 	 */
 	public static function addDependantSpouse($dependant)
 	{
 		try {
 			$sql = 'INSERT INTO dependant
								(memberid,titleid,dependantname,dependantmobilecountrycode,dependantmobile,dob,dependantid) 
			 					values(:memberid,:titleid,:dependantname,:dependantmobilecountrycode,:dependantmobile,:dob,:dependantid)';
			$spouseDob = (!empty($dependant['dependantSpouseDateOfBirth'])) ? date(yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($dependant['dependantSpouseDateOfBirth'])) : null;
 			$addDependantSpouse = Yii::$app->db->createCommand($sql)
						 			->bindValue(':memberid', $dependant['memberid'])
						 			->bindValue(':titleid', $dependant['dependantSpouseTitleId'])
						 			->bindValue(':dependantname', $dependant['dependantSpouseName'])
									->bindValue(':dependantmobilecountrycode', $dependant['dependantSpouseMobileCountryCode'] ?? NULL)
									->bindValue(':dependantmobile', $dependant['dependantSpouseMobile'] ?? NULL)
						 			->bindValue(':dob', $spouseDob)
						 			->bindValue(':dependantid', $dependant['dependantId'])
						 			->execute();
			if($addDependantSpouse)
			{
				$lastDepSpouseId = Yii::$app->db->getLastInsertID();
				return $lastDepSpouseId;
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
 	 * To update dependant spouse
 	 * @param unknown $dependant
 	 * @return boolean
 	 */
 	public static function updateDependantSpouse($dependant)
 	{
 		try {
 			$sql = 'UPDATE dependant SET titleid=:titleid, dependantname=:dependantname,dependantmobilecountrycode=:dependantmobilecountrycode,dependantmobile=:dependantmobile, dob=:dob 
                     WHERE id=:dependantspouseid and dependantid=:dependantid';
            $spouseDob = (!empty($dependant['dependantSpouseDateOfBirth'])) ? date(Yii::$app->params['dateFormat']['sqlDandTFormat'],strtotimeNew($dependant['dependantSpouseDateOfBirth'])) : null;

 			$updateSpouse = Yii::$app->db->createCommand($sql)
				 			->bindValue(':titleid', $dependant['dependantSpouseTitleId'])
				 			->bindValue(':dependantname', $dependant['dependantSpouseName'])
							->bindValue(':dependantmobilecountrycode', $dependant['dependantSpouseMobileCountryCode'] ?? NULL)
							->bindValue(':dependantmobile', $dependant['dependantSpouseMobile'] ?? NULL)
				 			->bindValue(':dob', $spouseDob)
				 			->bindValue(':dependantspouseid', $dependant['dependantSpouseId'])
				 			->bindValue(':dependantid', $dependant['dependantId'])
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
 	 * To update dependant Image
 	 */
 	public static function updateDependantImage($dependantImage,$dependantImageThumbnail,$dependantIds)
 	{
 		try {
 			$updateDependantImage = Yii::$app->db->createCommand("Update dependant set image=:dependantimage,thumbnailimage=:dependantimagethumbnail where id=:dependantid")
 									->bindValue(':dependantimage', $dependantImage)
 									->bindValue(':dependantimagethumbnail', $dependantImageThumbnail)
 									->bindValue(':dependantid', $dependantIds)
 									->execute();
 			if($updateDependantImage)
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
 	 * To update dependant spouse image
 	 */
 	public static function updateDependantSpouseImage($dependantSpouseImage,$dependantSpouseImageThumbnail,$dependantIds)
 	{
 		try {
 			$updateDependantSpouseImage = Yii::$app->db->createCommand("Update dependant set image=:dependantspouseimage,thumbnailimage=:dependantspouseimagethumbnail where dependantid=:dependantid")
 			->bindValue(':dependantspouseimage', $dependantSpouseImage)
 			->bindValue(':dependantspouseimagethumbnail', $dependantSpouseImageThumbnail)
 			->bindValue(':dependantid', $dependantIds)
 			->execute();
 			if($updateDependantSpouseImage)
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
 	 * To get dependant details
 	 */
 	public static function getExistingDependantDetails($dependantId)
 	{
 		try {
 			$sql = yii::$app->db->createCommand("select * from dependant where id=:dependantid")
 					->bindValue(':dependantid', $dependantId)
 					->queryOne();
 			return $sql;
 		} catch (Exception $e) {
 			return false;
 		}
 	}

 	/**
 	 * To update dependant 
 	 * @param unknown $dependant
 	 * @return boolean
 	 */
 	public static function updateDependant($dependant, $memberId)
 	{
 		try {
 			$sql = 'UPDATE dependant SET titleid=:titleid, dependantname=:dependantname,dependantmobile=:dependantmobile, dependantmobilecountrycode=:dependantmobilecountrycode,dob=:dob, relation=:relation,weddinganniversary=:weddinganniversary,ismarried=:ismarried WHERE id=:dependantid and memberid=:memberid';
            $dob = (!empty($dependant['dependantDob'])) ? date(Yii::$app->params['dateFormat']['sqlDandTFormat'],strtotimeNew($dependant['dependantDob'])) : null;
            $weddingAnniversary = (!empty($dependant['dependantWeddingAnniversary'])) ? date(Yii::$app->params['dateFormat']['sqlDandTFormat'],strtotimeNew($dependant['dependantWeddingAnniversary'])) : null;

 			$updateSpouse = Yii::$app->db->createCommand($sql)
				 			->bindValue(':titleid', $dependant['dependantTitleId'])
				 			->bindValue(':dependantname', $dependant['dependantName'])
							->bindValue(':dependantmobile', $dependant['dependantMobile'] ?? NULL)
							->bindValue(':dependantmobilecountrycode', $dependant['dependantMobileCountryCode'] ?? NULL)
				 			->bindValue(':dob', $dob)
				 			->bindValue(':relation', $dependant['dependantRelation'])
				 			->bindValue(':weddinganniversary', $weddingAnniversary)
				 			->bindValue(':ismarried', $dependant['dependantMaritalStatus'])
				 			->bindValue(':dependantid', $dependant['dependantId'])
				 			->bindValue(':memberid', $memberId)
				 			->execute();
 			return true;
 		} catch (Exception $e) {
 			return false;
 		}
 	}

 	/**
 	 * To update dependant spouse
 	 * @param unknown $dependant
 	 * @return boolean
 	 */
 	public static function updateDependantSpouseByDependantId($dependant, $memberId)
 	{
 		try {
 			$sql = 'UPDATE dependant SET titleid=:titleid, dependantname=:dependantname,dependantmobilecountrycode=:dependantmobilecountrycode, dependantmobile=:dependantmobile, dob=:dob WHERE dependantid=:dependantid and memberid=:memberid';
            $spouseDob = (!empty($dependant['dependantSpouseDateOfBirth'])) ? date(Yii::$app->params['dateFormat']['sqlDandTFormat'],strtotimeNew($dependant['dependantSpouseDateOfBirth'])) : null;
            $weddingAnniversary = (!empty($dependant['dependantWeddingAnniversary'])) ? date(Yii::$app->params['dateFormat']['sqlDandTFormat'],strtotimeNew($dependant['dependantWeddingAnniversary'])) : null;

 			$updateSpouse = Yii::$app->db->createCommand($sql)
				 			->bindValue(':titleid', $dependant['dependantSpouseTitleId'])
				 			->bindValue(':dependantname', $dependant['dependantSpouseName'])
							->bindValue(':dependantmobilecountrycode', $dependant['dependantSpouseMobileCountryCode'] ?? NULL)
							->bindValue(':dependantmobile', $dependant['dependantSpouseMobile'] ?? NULL)
				 			->bindValue(':dob', $spouseDob)
				 			->bindValue(':dependantid', $dependant['dependantId'])
				 			->bindValue(':memberid', $memberId)
				 			->execute();
 			return true;
 		} catch (Exception $e) {
 			return false;
 		}
 	}

 	public static function getDependantId($tempId,$isSpouse=false) 
 	{
		if($isSpouse == "false" || !($isSpouse)) {
			$sql = "SELECT id FROM dependant where tempdependantId = :tempId";
		}
		else {
			$sql = "SELECT dependantid FROM dependant where tempdependantId = :tempId";
		}
 		return yii::$app->db->createCommand($sql)->bindValue(':tempId', $tempId)->queryScalar();
 	}

}
