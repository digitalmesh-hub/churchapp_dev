<?php

namespace common\models\basemodels;

use Yii;

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
class Album extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'album';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['eventid'], 'required'],
            [['eventid', 'createdby', 'modifiedby'], 'integer'],
            [['createddatetime', 'modifieddatetime'], 'safe'],
            [['albumname'], 'string', 'max' => 50],
            [['ispublished', 'isalbumchanged'], 'string', 'max' => 4],
            [['eventid'], 'unique'],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
            [['eventid'], 'exist', 'skipOnError' => true, 'targetClass' => Events::className(), 'targetAttribute' => ['eventid' => 'id']],
            [['modifiedby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['modifiedby' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'albumid' => 'Albumid',
            'eventid' => 'Eventid',
            'albumname' => 'Albumname',
            'createdby' => 'Createdby',
            'createddatetime' => 'Createddatetime',
            'modifiedby' => 'Modifiedby',
            'modifieddatetime' => 'Modifieddatetime',
            'ispublished' => 'Ispublished',
            'isalbumchanged' => 'Isalbumchanged',
        ];
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
    public function getEvent()
    {
        return $this->hasOne(Events::className(), ['id' => 'eventid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModifiedby0()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'modifiedby']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlbumimages()
    {
        return $this->hasMany(Albumimage::className(), ['albumid' => 'albumid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendingAlbumImages()
    {
        return $this->hasMany(PendingAlbumImage::className(), ['albumid' => 'albumid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendingimagenotifications()
    {
        return $this->hasMany(Pendingimagenotification::className(), ['albumid' => 'albumid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendingimagenotificationsents()
    {
        return $this->hasMany(Pendingimagenotificationsent::className(), ['albumid' => 'albumid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuccessfullalbumsents()
    {
        return $this->hasMany(Successfullalbumsent::className(), ['albumid' => 'albumid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempAlbumImages()
    {
        return $this->hasMany(TempAlbumImage::className(), ['albumid' => 'albumid']);
    }
}
