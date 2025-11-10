<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "userconversation".
 *
 * @property int $userconversationid
 * @property int $conversationid
 * @property int $userid
 * @property int $isread
 * @property string $readtime
 *
 * @property Conversation $conversation
 * @property Usercredentials $user
 */
class UserConversation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'userconversation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['conversationid', 'userid'], 'required'],
            [['conversationid', 'userid'], 'integer'],
            [['readtime'], 'safe'],
            [['isread'], 'string', 'max' => 4],
            [['conversationid'], 'exist', 'skipOnError' => true, 'targetClass' => Conversation::className(), 'targetAttribute' => ['conversationid' => 'conversationid']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userconversationid' => 'Userconversationid',
            'conversationid' => 'Conversationid',
            'userid' => 'Userid',
            'isread' => 'Isread',
            'readtime' => 'Readtime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConversation()
    {
        return $this->hasOne(Conversation::className(), ['conversationid' => 'conversationid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
    }
}
