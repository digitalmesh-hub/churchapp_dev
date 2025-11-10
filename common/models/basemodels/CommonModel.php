<?php

namespace common\models\basemodels;

use Yii;
use Exception;

class CommonModel 
{
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
	
}