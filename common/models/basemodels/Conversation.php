<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "conversation".
 *
 * @property int $conversationid
 * @property int $conversationtopicid
 * @property string $conversation
 * @property int $createdby
 * @property string $createddatetime
 *
 * @property Usercredentials $createdby0
 * @property Conversationtopic $conversationtopic
 * @property Userconversation[] $userconversations
 */
class Conversation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'conversation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['conversationtopicid', 'createdby', 'createddatetime'], 'required'],
            [['conversationtopicid', 'createdby'], 'integer'],
            [['conversation'], 'string'],
            [['createddatetime'], 'safe'],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
            [['conversationtopicid'], 'exist', 'skipOnError' => true, 'targetClass' => Conversationtopic::className(), 'targetAttribute' => ['conversationtopicid' => 'conversationtopicid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'conversationid' => 'Conversationid',
            'conversationtopicid' => 'Conversationtopicid',
            'conversation' => 'Conversation',
            'createdby' => 'Createdby',
            'createddatetime' => 'Createddatetime',
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
    public function getConversationtopic()
    {
        return $this->hasOne(Conversationtopic::className(), ['conversationtopicid' => 'conversationtopicid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserconversations()
    {
        return $this->hasMany(Userconversation::className(), ['conversationid' => 'conversationid']);
    }
}
