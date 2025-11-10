<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "successfullalbumsent".
 *
 * @property int $id
 * @property string $sentto
 * @property int $albumid
 * @property int $userid
 * @property string $notificationsenton
 * @property int $institutionid
 * @property string $type
 *
 * @property Album $album
 * @property Institution $institution
 * @property Usercredentials $user
 */
class Successfullalbumsent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'successfullalbumsent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sentto', 'albumid', 'userid', 'notificationsenton', 'institutionid', 'type'], 'required'],
            [['albumid', 'userid', 'institutionid'], 'integer'],
            [['notificationsenton'], 'safe'],
            [['sentto'], 'string', 'max' => 1000],
            [['type'], 'string', 'max' => 15],
            [['albumid'], 'exist', 'skipOnError' => true, 'targetClass' => Album::className(), 'targetAttribute' => ['albumid' => 'albumid']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
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
            'sentto' => 'Sentto',
            'albumid' => 'Albumid',
            'userid' => 'Userid',
            'notificationsenton' => 'Notificationsenton',
            'institutionid' => 'Institutionid',
            'type' => 'Type',
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
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
    }
}
