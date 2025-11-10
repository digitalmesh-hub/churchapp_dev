<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "conversationtopic".
 *
 * @property int $conversationtopicid
 * @property string $subjecttitle
 * @property string $subject
 * @property int $institutionid
 * @property int $createdby
 * @property string $createddatetime
 * @property int $modifiedby
 * @property string $modifieddatetime
 * @property int $isactive
 *
 * @property Conversation[] $conversations
 * @property Usercredentials $createdby0
 * @property Institution $institution
 * @property Usercredentials $modifiedby0
 * @property Userconversationtopic[] $userconversationtopics
 */
class Conversationtopic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'conversationtopic';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['institutionid', 'createdby', 'createddatetime'], 'required'],
            [['institutionid', 'createdby', 'modifiedby'], 'integer'],
            [['createddatetime', 'modifieddatetime'], 'safe'],
            [['subjecttitle'], 'string', 'max' => 100],
            [['subject'], 'string', 'max' => 250],
            [['isactive'], 'string', 'max' => 4],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['modifiedby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['modifiedby' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'conversationtopicid' => 'Conversationtopicid',
            'subjecttitle' => 'Subjecttitle',
            'subject' => 'Subject',
            'institutionid' => 'Institutionid',
            'createdby' => 'Createdby',
            'createddatetime' => 'Createddatetime',
            'modifiedby' => 'Modifiedby',
            'modifieddatetime' => 'Modifieddatetime',
            'isactive' => 'Isactive',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConversations()
    {
        return $this->hasMany(Conversation::className(), ['conversationtopicid' => 'conversationtopicid']);
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
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionid']);
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
    public function getUserconversationtopics()
    {
        return $this->hasMany(Userconversationtopic::className(), ['conversationtopicid' => 'conversationtopicid']);
    }
}
