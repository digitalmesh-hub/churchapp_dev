<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "userconversationtopic".
 *
 * @property int $userconversationtopicid
 * @property int $conversationtopicid
 * @property int $userid
 * @property int $isread
 * @property string $readtime
 *
 * @property Conversationtopic $conversationtopic
 * @property Usercredentials $user
 */
class UserConversationTopic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'userconversationtopic';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['conversationtopicid', 'userid'], 'required'],
            [['conversationtopicid', 'userid'], 'integer'],
            [['readtime'], 'safe'],
            [['isread'], 'string', 'max' => 4],
            [['conversationtopicid'], 'exist', 'skipOnError' => true, 'targetClass' => Conversationtopic::className(), 'targetAttribute' => ['conversationtopicid' => 'conversationtopicid']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userconversationtopicid' => 'Userconversationtopicid',
            'conversationtopicid' => 'Conversationtopicid',
            'userid' => 'Userid',
            'isread' => 'Isread',
            'readtime' => 'Readtime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConversationtopic()
    {
        return $this->hasOne(Conversationtopic::className(), ['conversationtopicid' => 'conversationtopicid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
    }
}
