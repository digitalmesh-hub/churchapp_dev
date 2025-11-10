<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Albumimage;
use Exception;

/**
 * This is the model class for table "albumimage".
 *
 * @property int $id
 * @property string $imageid
 * @property int $albumid
 * @property string $imageurl
 * @property string $caption
 * @property int $iscover
 * @property int $createdby
 * @property string $createddatetime
 * @property int $modifiedby
 * @property string $modifieddatetime
 * @property string $coverchangeddatetime
 * @property string $thumbnail
 *
 * @property Album $album
 * @property Usercredentials $createdby0
 * @property Usercredentials $modifiedby0
 */
class ExtendedAlbumimage extends Albumimage
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'albumimage';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
				[['imageid', 'albumid', 'createdby', 'createddatetime', 'imageurl'], 'required'],
				[['albumid', 'createdby', 'modifiedby'], 'integer'],
				[['createddatetime', 'modifieddatetime', 'coverchangeddatetime'], 'safe'],
				[['imageid'], 'string', 'max' => 45],
				[['imageurl', 'thumbnail'], 'file', 'skipOnEmpty' => true,
					'extensions' => 'png, jpg,jpeg', 'skipOnEmpty' =>true,
					'maxSize' => \Yii::$app->params['fileUploadSize']['imageFileSize'],
					'tooBig' => \Yii::$app->params['fileUploadSize']['imageSizeMsg']],
				[['caption'], 'string', 'max' => 100],
				[['iscover'], 'string', 'max' => 4],
				[['albumid'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedAlbum::className(), 'targetAttribute' => ['albumid' => 'albumid']],
				[['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedUsercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
				[['modifiedby'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedUsercredentials::className(), 'targetAttribute' => ['modifiedby' => 'id']],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
				'id' => 'ID',
				'imageid' => 'Imageid',
				'albumid' => 'Albumid',
				'imageurl' => 'File',
				'caption' => 'Caption',
				'iscover' => 'Iscover',
				'createdby' => 'Createdby',
				'createddatetime' => 'Createddatetime',
				'modifiedby' => 'Modifiedby',
				'modifieddatetime' => 'Modifieddatetime',
				'coverchangeddatetime' => 'Coverchangeddatetime',
				'thumbnail' => 'Thumbnail',
		];
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAlbum()
	{
		return $this->hasOne(Album::className(), ['albumid' => 'albumid']);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCreatedby0()
	{
		return $this->hasOne(Usercredentials::className(), ['id' => 'createdby']);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getModifiedby0()
	{
		return $this->hasOne(Usercredentials::className(), ['id' => 'modifiedby']);
	}
	/**
	 * To delete album images
	 * @param unknown $albumId
	 * @return boolean
	 */
	public static function deleteAlbumImages($albumId)
	{
		try {
			$deleteImage = Yii::$app->db->createCommand("delete from albumimage where albumid = :albumid")
							->bindValue(':albumid', $albumId)
							->execute();
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To delete album image
	 * @param unknown $imageId
	 * @return boolean
	 */
	public static function deleteImage($imageId)
	{
		try {
			$deleteAnImage = Yii::$app->db->createCommand("delete from albumimage where id = :imageid")
							->bindValue(':imageid', $imageId)
							->execute();
			return true;
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * To change the caption
	 * of an image
	 * @param unknown $imageId
	 * @param unknown $caption
	 * @return boolean
	 */
	 public static function changeCaption($imageId,$caption)
	{
		try {
		
			$updateCaption = Yii::$app->db->createCommand("update albumimage set caption=:caption where id=:imageid")
							->bindValue(':caption', $caption)
							->bindValue(':imageid', $imageId)
							->execute();
			
			return true;
			
		} catch (Exception $e) {
			return false;
		}
	}
	/**
	 * Get published image details
	 * @param unknown $date
	 * @param unknown $institutionId
	 * @return boolean
	 */
	public static function getIsPublished($date,$institutionId)
	{
		try {
			$qry = "select al.ispublished from albumimage alimg
				inner join album al
				on al.albumid=alimg.albumid
				inner join events e
				on al.eventid=e.id WHERE e.expirydate > :expdate and e.institutionid = :institutionid";
			$values = [
					':expdate' => $date,
					':institutionid' => $institutionId
			];
			$model = Yii::$app->db->createCommand ( $qry )->bindValues ( $values )->queryAll ();
			return $model;
		} catch (Exception $e) {
			return false;
		}
	}
}
