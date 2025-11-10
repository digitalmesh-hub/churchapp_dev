<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\UserLocation;
use common\models\extendedmodels\ExtendedUsercredentials;

/**
 * This is the model class for table "userlocation".
 *
 * @property int $userlocationid
 * @property int $userid
 * @property double $latitude
 * @property double $longitude
 * @property string $lastupdateddatetime
 *
 * @property Usercredentials $user
 */
class ExtendedUserLocation extends UserLocation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'latitude', 'longitude', 'lastupdateddatetime'], 'required'],
            [['userid'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['lastupdateddatetime'], 'safe'],
            [['userid'], 'unique'],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedUsercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }
    /**
     * To delete a user
     * @param unknown $userId
     */
    public static function deleteUserLocation ($userId)
    {
    
    	try {
    			
    		$deleteUserLocation = Yii::$app->db->createCommand('delete from userlocation where userid = :userId')
    		->bindValue(':userId', $userId)
    		->execute();
    		return $deleteUserLocation;
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To add user location
     * @param $userId int
     * @param $latitude float
     * @param $longitude float
     * @param $lastUpdatedDateTime DateTime
     */
    public static function addUserLocation ($userId,$latitude,$longitude,$lastUpdatedDateTime)
    {
    	try {
    		$addUserLocation = Yii::$app->db->createCommand('INSERT INTO userlocation (userid,latitude,longitude,lastupdateddatetime) VALUES (:userid, :latitude, :longitude, :lastupdateddatetime)')
    		->bindValues([
    				':userid' => $userId,
    				':latitude' => $latitude,
    				':longitude' => $longitude,
    				':lastupdateddatetime' => $lastUpdatedDateTime,
    		])
    		->execute();
    		return $addUserLocation;
    			
    	} catch (Exception $e) {
    		return false;
    	}
    }
    /**
     * To get the details of near by members
     * @param $institutionId int
     * @param $userId int
     * @param $latitude double
     * @param $longitude double
     * @param $distance double
     */
    public static function getNearByMembers($institutionId,$userId,$latitude,$longitude,$distance)
    {
    	try {
    		$memberData = Yii::$app->db->createCommand(
    				"CALL getnearbymembers(:institutionid, :userid, :latpoint, :longpoint, :radius)")
    				->bindValue(':institutionid' , $institutionId )
    				->bindValue(':userid', $userId)
    				->bindValue(':latpoint', $latitude)
    				->bindValue(':longpoint', $longitude)
    				->bindValue(':radius', $distance)
    				->queryAll();
    		return $memberData;
    		
    	} catch (Exception $e) {
    		return false;
    		
    	}
    	
    }
    
    
}
