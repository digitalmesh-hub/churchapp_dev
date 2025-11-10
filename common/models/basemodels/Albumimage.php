<?php

namespace common\models\basemodels;

use Yii;

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
class Albumimage extends \yii\db\ActiveRecord
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
            [['imageid', 'albumid', 'createdby', 'createddatetime'], 'required'],
            [['albumid', 'createdby', 'modifiedby'], 'integer'],
            [['createddatetime', 'modifieddatetime', 'coverchangeddatetime'], 'safe'],
            [['imageid'], 'string', 'max' => 45],
            [['imageurl', 'thumbnail'], 'string', 'max' => 250],
            [['caption'], 'string', 'max' => 100],
            [['iscover'], 'string', 'max' => 4],
            [['albumid'], 'exist', 'skipOnError' => true, 'targetClass' => Album::className(), 'targetAttribute' => ['albumid' => 'albumid']],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
            [['modifiedby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['modifiedby' => 'id']],
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
            'imageurl' => 'Imageurl',
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
}
