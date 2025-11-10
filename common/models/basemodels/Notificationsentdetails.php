<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "notificationsentdetails".
 *
 * @property int $id
 * @property string $reminderdate
 * @property string $body
 * @property int $institutionid
 * @property string $subject
 * @property int $messagetype
 *
 * @property Institution $institution
 */
class Notificationsentdetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notificationsentdetails';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reminderdate', 'body', 'institutionid', 'subject', 'messagetype'], 'required'],
            [['reminderdate'], 'safe'],
            [['institutionid', 'messagetype'], 'integer'],
            [['body'], 'string', 'max' => 8000],
            [['subject'], 'string', 'max' => 250],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reminderdate' => 'Reminderdate',
            'body' => 'Body',
            'institutionid' => 'Institutionid',
            'subject' => 'Subject',
            'messagetype' => 'Messagetype',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionid']);
    }
}
