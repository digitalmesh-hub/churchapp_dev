<?php

namespace common\models\basemodels;

use Yii;

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
class TempAlbumImage extends \yii\db\ActiveRecord
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
        return $this->hasOne(Album::className(), ['albumid' => 'albumid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedby0()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'createdby']);
    }
}
