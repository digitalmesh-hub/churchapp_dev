<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Album;
use Exception;

/**
 * This is the model class for table "album".
 *
 * @property int $albumid
 * @property int $eventid
 * @property string $albumname
 * @property int $createdby
 * @property string $createddatetime
 * @property int $modifiedby
 * @property string $modifieddatetime
 * @property int $ispublished
 * @property int $isalbumchanged
 *
 * @property Usercredentials $createdby0
 * @property Events $event
 * @property Usercredentials $modifiedby0
 * @property Albumimage[] $albumimages
 * @property PendingAlbumImage[] $pendingAlbumImages
 * @property Pendingimagenotification[] $pendingimagenotifications
 * @property Pendingimagenotificationsent[] $pendingimagenotificationsents
 * @property Successfullalbumsent[] $successfullalbumsents
 * @property TempAlbumImage[] $tempAlbumImages
 */
class ExtendedAlbum extends Album
{
	/**
	 * to get all album count
	 * @param unknown $userId
	 * @return string|NULL|\yii\db\false|boolean
	 */
	public static function getPendingAlbumCount($userId)
	{
		$institutionId = Yii::$app->user->identity->institutionid;
		try {
			$albumCount = Yii::$app->db->createCommand(
					"CALL getpendingalbumscount(:userid,:institutionid)")
					->bindValue(':userid', $userId)
					->bindValue(':institutionid',$institutionId)
					->queryScalar();
			return $albumCount;
		} catch (Exception $e) {
			return false;
		}
		
	}
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(ExtendedEvent::className(), ['id' => 'eventid']);
    }
	/**
	 * To get all user albums
	 * @param unknown $userId
	 * @param unknown $lastUpdatedOn
	 * @return boolean
	 */
	public static function getAllUserAlbums($userId,$lastUpdatedOn)
	{
		try {
			$userAlbums = Yii::$app->db->createCommand("
						CALL getalluseralbums(:userid,:lastupdatedon)")
						->bindValue(':userid', $userId)
						->bindValue(':lastupdatedon', $lastUpdatedOn)
						->queryAll();
			return $userAlbums;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To get photos
	 * in albums
	 * @param unknown $userId
	 * @param unknown $albumId
	 * @param unknown $lastUpdatedOn
	 * @return boolean
	 */
	public static function getPhotosInAlbum($userId,$albumId,$lastUpdatedOn)
	{
		try {
			$photosInAlbum = Yii::$app->db->createCommand("
						CALL getalbumimages(:userid,:albumid,:lastupdatedon)")
						->bindValue(':userid', $userId)
						->bindValue(':albumid', $albumId)
						->bindValue(':lastupdatedon', $lastUpdatedOn)
						->queryAll();
			return $photosInAlbum;
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To get album details
	 * @param unknown $albumId
	 * @return \yii\db\false|boolean
	 */
	public static function getAlbumInfo($albumId)
	{
		try {
			$albumInfo = Yii::$app->db->createCommand("
						CALL getalbuminfo(:albumid)")
						->bindValue(':albumid', $albumId)
						->queryOne();
			return $albumInfo;
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To get the list of
	 * all pending albums
	 * of a user
	 * @param unknown $userId
	 * @return unknown|boolean
	 */
	public static function getPendingAlbumList($userId)
	{
		$institutionId = Yii::$app->user->identity->institutionid;
		try {
			$albumList = Yii::$app->db->createCommand("
						CALL getpendingalbumimagestolist1(:userid,:institutionid)") 
						->bindValue(':userid', $userId)
						->bindValue(':institutionid',$institutionId)
						->queryAll();
			return $albumList;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * To get event id
	 * @param unknown $albumId
	 * @return \yii\db\false|boolean
	 */
	public static function getEventId($albumId)
	{
		try {
			$eventId = Yii::$app->db->createCommand("select eventid from album where albumid = :albumid")
						->bindValue(':albumid', $albumId)
						->queryOne();
			return $eventId;
			
		} catch (Exception $e) {
			return false;		
		}
	}
	/**
	 * 
	 * @param unknown $photoId
	 * @param unknown $albumId
	 * @return \yii\db\false|boolean
	 */
	public static function getPendingImages($photoId,$albumId)
	{
		try {
			$pendingImages = Yii::$app->db->createCommand("
				CALL getalbumspendingimages(:albumid,:photoid)")
				->bindValue(':albumid', $albumId)
				->bindValue(':photoid', $photoId)
				->queryOne();

			if(!empty($pendingImages)){
				return $pendingImages;
			}
			else{
				return true;
			}		
		} catch (Exception $e) {
			return false;
		}
		
	}
	/**
	 * To delete an album
	 * @param unknown $albumId
	 * @return boolean
	 */
	public static function deleteAlbum($albumId)
	{
		try {
			$deleteAlbumimages = ExtendedAlbumimage::deleteAlbumImages($albumId);
			if($deleteAlbumimages = true)
			{
				$deleteAlbum = Yii::$app->db->createCommand("delete from album where albumid = :albumid")
							->bindValue(':albumid', $albumId)
							->execute();
				return true;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To get the album details
	 * @param unknown $albumId
	 * @return \yii\db\false|boolean
	 */
	public static function getAlbumDetails($albumId)
	{
		try {
			$albumData = Yii::$app->db->createCommand("CALL getalbuminfo(:albumid)")
						->bindValue(':albumid', $albumId)
						->queryOne();
			return $albumData;
		} catch (Exception $e) {
			return false;
		}
	}
}

