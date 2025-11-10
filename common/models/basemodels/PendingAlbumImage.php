<?php

namespace common\models\basemodels;

use Yii;

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
class PendingAlbumImage extends \yii\db\ActiveRecord
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
            [['thumbnail'], 'string', 'max' => 250],
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
}
