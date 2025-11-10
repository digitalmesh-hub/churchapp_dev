<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "feedbacknotification".
 *
 * @property int $id
 * @property int $userid
 * @property int $feedbackid
 * @property string $createddatetime
 *
 * @property Feedback $feedback
 * @property Usercredentials $user
 */
class Feedbacknotification extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feedbacknotification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'feedbackid', 'createddatetime'], 'required'],
            [['userid', 'feedbackid'], 'integer'],
            [['createddatetime'], 'safe'],
            [['feedbackid'], 'exist', 'skipOnError' => true, 'targetClass' => Feedback::className(), 'targetAttribute' => ['feedbackid' => 'feedbackid']],
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
            'feedbackid' => 'Feedbackid',
            'createddatetime' => 'Createddatetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedback()
    {
        return $this->hasOne(Feedback::className(), ['feedbackid' => 'feedbackid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
    }
}
