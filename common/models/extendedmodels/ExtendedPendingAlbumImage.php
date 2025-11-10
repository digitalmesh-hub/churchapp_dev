<?php

namespace common\models\extendedmodels;

use Yii;
use yii\base\ErrorException;
use common\models\basemodels\PendingAlbumImage;

/**
 * This is the model class for table "pending_album_image".
 *
 * @property int $id
 * @property int $albumid
 * @property string $pending_imageid
 * @property string $pending_imageurl
 * @property int $createdby
 * @property string $createddatetime
 * @property string $caption
 *
 * @property Album $album
 * @property Usercredentials $createdby0
 */
class ExtendedPendingAlbumImage extends PendingAlbumImage
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pending_album_image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['albumid', 'pending_imageid', 'pending_imageurl', 'createdby', 'createddatetime'], 'required'],
            [['albumid', 'createdby'], 'integer'],
            [['createddatetime'], 'safe'],
            [['pending_imageid'], 'string', 'max' => 45],
            [['pending_imageurl'], 'string', 'max' => 250],
            [['caption'], 'string', 'max' => 100],
            [['albumid'], 'exist', 'skipOnError' => true, 'targetClass' => Album::className(), 'targetAttribute' => ['albumid' => 'albumid']],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'albumid' => 'Albumid',
            'pending_imageid' => 'Pending Imageid',
            'pending_imageurl' => 'Pending Imageurl',
            'createdby' => 'Createdby',
            'createddatetime' => 'Createddatetime',
            'caption' => 'Caption',
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
     * To get the pending album images
     * @param unknown $institutionId
     * @param unknown $albumId
     * @return boolean
     */
    public static function getPendingAlbumImages($institutionId,$albumId)
    {
    	/* try {
    		$pendingImages = Yii::$app->db->createCommand(
    				"CALL getallpendingimages(:institutionid,:albumid,:imageid)")
    				->bindValue(':institutionid', $institutionId)
    				->bindValue(':albumid', $albumId)
    				->bindValue(':imageid', $imageId)
    				->queryAll();
    		return $pendingImages;
    	} catch (Exception $e) {
    		return false;
    	} */
    	
    	try {
			$pendingImages = Yii::$app->db->createCommand("SELECT pm.*,a.eventid,
								 if(um.usertype='M',CONCAT(mt.description,' ',IFNULL(m.firstName,''),' ',IFNULL(m.middleName,''), ' ',IFNULL(m.lastName,'')),    
									CONCAT(st.description,' ',IFNULL(m.spouse_firstName,''),' ',IFNULL(m.spouse_middleName,''), ' ',IFNULL(m.spouse_lastName,''))) as name
								    FROM pending_album_image pm
								    inner join album a on pm.albumid=a.albumid
								    inner join usercredentials uc on pm.createdby=uc.id             
								    inner join usermember um on um.userid = pm.createdby
								 	and um.institutionid = :institutionid
								inner join member m on m.memberid = um.memberid
								left join title mt on mt.titleid = m.membertitle
								left join title st on st.titleid = m.spousetitle
								 where pm.albumid= :albumid")
								->bindValue(':institutionid', $institutionId)
								->bindValue(':albumid', $albumId)
								->queryAll();
			 
			 return $pendingImages;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }

     /**
     * Get eventId from albumId
     * @param $albumId int   
     * @return $event array 
     */
    public function getEventIdFromAlbumId($albumId)
    {
         try {
            $event = Yii::$app->db->createCommand(
                    'SELECT eventid FROM album WHERE albumid =:albumid')
                    ->bindValue(':albumid', $albumId)
                    ->queryOne();
            if(!empty($event)){
                return $event;
            }
            else{
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check member available in committee
     * @param $albumId int
     * @param $pendingImageId int
     * @param $pendingImageURL string
     * @param $caption string
     * @param $createdBy int
     * @param $createdDateTime dateTime
     * @return boolean
    */
    public function savePendingImage($albumId, $pendingImageId, $pendingImageURL, $caption, $createdBy, $createdDateTime, $thumbnailImage)
    {
       
        try {
            if(!empty($pendingImageURL) && !empty($thumbnailImage) ) {
                $sql = 'INSERT INTO pending_album_image (albumid,
                    pending_imageid,pending_imageurl,caption,createdby,createddatetime,thumbnail)
                    VALUES(:albumId, :pendingImageId,
                    :pendingImageURL, :caption, :createdBy,
                    :createdDateTime, :thumbnail)';

                return Yii::$app->db->createCommand($sql)
                    ->bindValue(':albumId', $albumId)
                    ->bindValue(':pendingImageId', $pendingImageId)
                    ->bindValue(':pendingImageURL', $pendingImageURL)
                    ->bindValue(':caption', $caption)
                    ->bindValue(':createdBy', $createdBy)
                    ->bindValue(':createdDateTime', $createdDateTime)
                    ->bindValue(':thumbnail', $thumbnailImage)
                    ->execute();
            
            } else {
                yii::error('Image url not found');
                return false;
            }
        } catch (ErrorException $e) {
            return false;
        }
    }

    /**
     * update pending image caption
     */
    public static function updatePendingImageCaption($imageId, $albumId, $caption)
    {
        try {
            Yii::$app->db->createCommand(
                    "CALL update_pending_imagecaption(:imageId, :albumId, :caption)")
                    ->bindValue(':imageId', $imageId)
                    ->bindValue(':albumId', $albumId)
                    ->bindValue(':caption', $caption)
                    ->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}