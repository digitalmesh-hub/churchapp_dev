<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "successfulleventsent".
 *
 * @property int $id
 * @property string $sentto
 * @property int $notificationid
 * @property int $userid
 * @property string $notificationsenton
 * @property string $notificationtype
 * @property int $institutionid
 * @property string $type
 *
 * @property Institution $institution
 * @property Usercredentials $user
 */
class Successfulleventsent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'successfulleventsent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sentto', 'notificationid', 'userid', 'notificationsenton', 'notificationtype', 'institutionid', 'type'], 'required'],
            [['notificationid', 'userid', 'institutionid'], 'integer'],
            [['notificationsenton'], 'safe'],
            [['sentto'], 'string', 'max' => 1000],
            [['notificationtype'], 'string', 'max' => 1],
            [['type'], 'string', 'max' => 15],
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
            'notificationid' => 'Notificationid',
            'userid' => 'Userid',
            'notificationsenton' => 'Notificationsenton',
            'notificationtype' => 'Notificationtype',
            'institutionid' => 'Institutionid',
            'type' => 'Type',
        ];
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
