<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Pendingimagenotificationsent;

/**
 * This is the model class for table "pendingimagenotificationsent".
 *
 * @property int $id
 * @property int $albumid
 * @property int $userid
 * @property int $uploadedby
 * @property string $createddatetime
 * @property Album $album
 * @property Usercredentials $uploadedby0
 * @property Usercredentials $user
 */
class ExtendedPendingimagenotificationsent extends Pendingimagenotificationsent
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pendingimagenotificationsent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['albumid', 'userid', 'uploadedby', 'createddatetime'], 'required'],
            [['albumid', 'userid', 'uploadedby'], 'integer'],
            [['createddatetime'], 'safe'],
            [['albumid'], 'exist', 'skipOnError' => true, 'targetClass' => Album::className(), 'targetAttribute' => ['albumid' => 'albumid']],
            [['uploadedby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['uploadedby' => 'id']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
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
            'userid' => 'Userid',
            'uploadedby' => 'Uploadedby',
            'createddatetime' => 'Createddatetime',
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
    public function getUploadedby0()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'uploadedby']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
    }
}
