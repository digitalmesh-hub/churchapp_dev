<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "rsvpnotificationsent".
 *
 * @property int $id
 * @property int $userid
 * @property int $rsvpid
 * @property string $createddatetime
 *
 * @property Rsvpdetails $rsvp
 * @property Usercredentials $user
 */
class Rsvpnotificationsent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rsvpnotificationsent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'rsvpid', 'createddatetime'], 'required'],
            [['userid', 'rsvpid'], 'integer'],
            [['createddatetime'], 'safe'],
            [['rsvpid'], 'exist', 'skipOnError' => true, 'targetClass' => Rsvpdetails::className(), 'targetAttribute' => ['rsvpid' => 'id']],
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
            'userid' => 'Userid',
            'rsvpid' => 'Rsvpid',
            'createddatetime' => 'Createddatetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRsvp()
    {
        return $this->hasOne(Rsvpdetails::className(), ['id' => 'rsvpid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
    }
}
