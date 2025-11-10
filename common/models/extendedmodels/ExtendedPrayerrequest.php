<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Prayerrequest;
use common\models\basemodels\Institution;
use common\models\basemodels\Usercredentials;

/**
 * This is the model class for table "prayerrequest".
 *
 * @property int $prayerrequestid
 * @property int $userid
 * @property int $institutionid
 * @property string $subject
 * @property string $description
 * @property string $createdtime
 * @property int $isresponded
 *
 * @property Institution $institution
 * @property Usercredentials $user
 * @property Prayerrequestnotification[] $prayerrequestnotifications
 * @property Prayerrequestnotificationsent[] $prayerrequestnotificationsents
 */
class ExtendedPrayerrequest extends Prayerrequest
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'prayerrequest';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
				[['userid', 'institutionid'], 'integer'],
				[['createdtime'], 'safe'],
				[['subject'], 'string', 'max' => 100],
				[['description'], 'string', 'max' => 250],
				[['isresponded'], 'string', 'max' => 4],
				[['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
				[['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
		];
	}
	/**
	 * To save the prayer request
	 * @param $userId int
	 * @param $institutionId int
	 * @param $prayerRequestTitle string
	 * @param $prayerRequestContent string
	 * @param $createdTime DateTime
	 */
	public static function savePrayerRequest($userId,$institutionId,$prayerRequestTitle,$prayerRequestContent,$createdTime)
	{
		try {
			$addPrayerRequest = Yii::$app->db->createCommand(
					"CALL addprayerrequest(:userid, :institutionid, :subject, :description, :createdtime)")
					->bindValue(':userid' , $userId )
					->bindValue(':institutionid', $institutionId)
					->bindValue(':subject', $prayerRequestTitle)
					->bindValue(':description', $prayerRequestContent)
					->bindValue(':createdtime', $createdTime)
					->queryOne();
			return $addPrayerRequest;
		} catch (Exception $e) {
			yii::error($e->getMessage());
			return false;
		}
	}
	/**
	 * To get the prayer request data
	 * @param unknown $prayerrequestiid
	 * @param string $isPrayer
	 * @return \yii\db\false|boolean
	 */
	public static function getRequestData($prayerrequestiid, $isPrayer=false)
	{
		try {

			$requestData = Yii::$app->db->createCommand(
				"CALL getprayerrequestdetails(:prayerrequestiid)")
				->bindValue(':prayerrequestiid' , $prayerrequestiid )
				->queryOne();
			if($isPrayer){	
				if(!empty($requestData)){
					return $requestData;
				}
				else{
					return true;
				}
			}
			else{
				return $requestData;
			}
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To get the prayer request count
	 * @param unknown $userId
	 * @param unknown $currentDate
	 * @return string|NULL|\yii\db\false|boolean
	 */
	public static function getAllPrayerRequestCount($userId,$currentDate)
	{	

		$institutionId = Yii::$app->user->identity->institutionid;
		try {
			$prayerRequestCount = Yii::$app->db->createCommand(
					"CALL getallprayerrequestcount(:currentdate,:userid,:institutionid)")
					->bindValue(':currentdate' , $currentDate )
					->bindValue(':userid' , $userId )
					->bindValue(':institutionid',$institutionId)
					->queryScalar();
			return $prayerRequestCount;
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To save the prayer email id
	 * @param unknown $institutionId
	 * @param unknown $prayermail
	 * @return number|boolean
	 */
    public static function savePrayerEmail($institutionId,$prayermail)
    {
        try {
            $savePrayerEmail = Yii::$app->db->createCommand('update institution set prayeremail =:prayeremail WHERE id=:institutionid')
                                        ->bindValue(':institutionid',$institutionId)
                                        ->bindValue(':prayeremail',$prayermail)
                                        ->execute();
            return $savePrayerEmail;
    
        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * To get the prayer email id
     * @param unknown $institutionId
     * @return \yii\db\false|boolean
     */
    public static function getPrayerEmail($institutionId)
    {
        try {
            $getPrayerEmail = Yii::$app->db->createCommand('select prayeremail from institution where id=:institutionid')
                                    ->bindValue(':institutionid',$institutionId)
                                    ->queryOne();
            return $getPrayerEmail;
        } catch (Exception $e) {
            return false;
        }
    } 
    /**
     * To get all prayer requests
     * @param unknown $currentDate
     * @param unknown $userId
     * @return boolean
     */
    public static function getAllPrayerRequest($currentDate,$userId)
    {
    	$institutionId = Yii::$app->user->identity->institutionid;
    	try {
    		$allPrayerrequest = Yii::$app->db->createCommand(
					"CALL getallprayerrequesttolist1(:currentdate,:userid, :institutionid)") // change the sp to getallprayerrequesttolist
					->bindValue(':currentdate' , $currentDate )
					->bindValue(':userid' , $userId )
					->bindValue(':institutionid',$institutionId)
					->queryAll();
			if(!empty($allPrayerrequest)){
				return $allPrayerrequest;
			}
			else{
				return true;
			}
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To update isResponded bit
     * if user acknowledged with a mail
     * @param $requestId int
     */
    public static function setIsRespondedBit($requestId)
    {
    	try {
    		$updateBit = Yii::$app->db->createCommand('update prayerrequest set isresponded=1 where prayerrequestid = :prayerrequestid')
            ->bindValue(':prayerrequestid',$requestId)
            ->execute();
    		return true;
    	} catch (Exception $e) {
    		return false;
    	}
    }
	
}
