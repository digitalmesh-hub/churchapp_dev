<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\TempAlbumImage;

/**
 * This is the model class for table "temp_album_image".
 *
 * @property int $id
 * @property string $temp_imageid
 * @property int $albumid
 * @property string $imageurl
 * @property string $caption
 * @property int $isnew
 * @property int $isdeleted
 * @property int $iscaptionchanged
 * @property int $isalbumcover
 * @property int $createdby
 * @property string $createddatetime
 * @property string $coverchangeddatetime
 *
 * @property Album $album
 * @property Usercredentials $createdby0
 */
class ExtendedTempAlbumImage extends TempAlbumImage
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'temp_album_image';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
				[['temp_imageid', 'albumid', 'isnew', 'isdeleted', 'iscaptionchanged', 'isalbumcover', 'createdby', 'createddatetime'], 'required'],
				[['albumid', 'createdby'], 'integer'],
				[['createddatetime', 'coverchangeddatetime'], 'safe'],
				[['temp_imageid'], 'string', 'max' => 45],
				[['imageurl'], 'string', 'max' => 250],
				[['caption'], 'string', 'max' => 100],
				[['isnew', 'isdeleted', 'iscaptionchanged', 'isalbumcover'], 'string', 'max' => 4],
				[['albumid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedAlbum::className(), 'targetAttribute' => ['albumid' => 'albumid']],
				[['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedUsercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
				'id' => 'ID',
				'temp_imageid' => 'Temp Imageid',
				'albumid' => 'Albumid',
				'imageurl' => 'Imageurl',
				'caption' => 'Caption',
				'isnew' => 'Isnew',
				'isdeleted' => 'Isdeleted',
				'iscaptionchanged' => 'Iscaptionchanged',
				'isalbumcover' => 'Isalbumcover',
				'createdby' => 'Createdby',
				'createddatetime' => 'Createddatetime',
				'coverchangeddatetime' => 'Coverchangeddatetime',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAlbum()
	{
		return $this->hasOne(ExtendedAlbum::className(), ['albumid' => 'albumid']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCreatedby0()
	{
		return $this->hasOne(ExtendedUsercredentials::className(), ['id' => 'createdby']);
	}
	/**
	 * To change the caption
	 * of an image
	 */
	public static function changeCaptionPending($imageId,$caption)
	{
		try {
			$updateCaption = Yii::$app->db->createCommand("update temp_album_image set caption=:caption where id=:imageid")
			->bindValue(':caption', $caption)
			->bindValue(':imageid', $imageId)
			->execute();
			return true;
				
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * Get pending albums
	 */
	public static function getPendingAlbum($institutionId)
	{
		try {
			$tempAlbum = Yii::$app->db->createCommand(
    				"CALL getallpendingalbums(:institutionid)")
    				->bindValue(':institutionid', $institutionId)
    				->queryAll();
			return $tempAlbum;
		} catch (Exception $e) {
			return false;
		}
		/* try {
			$tempAlbum = "select tl.*,al.albumname,al.createddatetime from temp_album_image tl
        				inner join album al on al.albumid = tl.albumid
        				inner join events e on al.eventid=e.id WHERE
        				tl.isalbumcover = 1 and tl.isdeleted=0 and e.institutionid=:institutionid";
			$values = [
					':institutionid' => $institutionId
			];
			$tempAlbumModel = Yii::$app->db->createCommand ( $tempAlbum )->bindValues ( $values )->queryAll ();
			return $tempAlbumModel;
			
		} catch (Exception $e) {
			return false;
		}  */
		
	}
	/**
	 * Get temporary album details
	 */
	public static function getTempAlbumImages($id)
	{
		try {
			$tempAlbum = "select pl.*,al.createddatetime,al.albumname
					   from temp_album_image pl
					   inner join album al
					   on al.albumid = pl.albumid
					   where tl.albumid = :albumid and tl.isdeleted=0";
			$values = [
					':albumid' => $id
			];
			$model = Yii::$app->db->createCommand ( $tempAlbum )->bindValues ( $values )->queryAll ();
			return $model;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To get user details of
	 * temporary album
	 */
	public static function getTempUserDetails($id)
	{
		try {
			$tempImageDetails = "select al.createddatetime,al.albumname,m.firstName,m.middleName,m.lastName
						from temp_album_image tl
						inner join album al
						on al.albumid = tl.albumid
						inner join usercredentials uc
						on tl.createdby = uc.id
						inner join usermember um
						on um.userid = uc.id
						inner join `member` m
						on m.memberid = um.memberid
						where tl.albumid = :albumid";
			$params = [
					':albumid' => $id
			];
			$tempImageData = Yii::$app->db->createCommand ( $tempImageDetails )->bindValues ( $params )->queryOne ();
			return $tempImageData;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * Add datas from temp image
	 * to album image
	 */
	public static function addDataToAlbumImage($albumId,$imageId)
	{
		try {
			$addApprovedImage = "INSERT INTO albumimage (imageid, albumid, imageurl, caption, iscover, createdby,createddatetime,thumbnail)
											  SELECT pending_imageid, albumid, pending_imageurl, caption,
											  0, createdby, createddatetime,thumbnail 
											  FROM pending_album_image WHERE albumid=:albumId and id IN (".$imageId." )";
			$values = [
					':albumId' => $albumId,
					//':imageId' => $imageId
			];
			$approveImage = Yii::$app->db->createCommand ( $addApprovedImage )->bindValues( $values )->execute();
			
		} catch (Exception $e) {
			return false;
		}
	}
}

