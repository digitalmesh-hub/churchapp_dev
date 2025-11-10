<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "eventseendetails".
 *
 * @property int $eventid
 * @property int $userid
 * @property int $institutionid
 * @property int $viewedstatus
 * @property int $deviceid
 * @property string $vieweddate
 * @property string $type
 *
 * @property Devicedetails $device
 * @property Events $event
 * @property Institution $institution
 * @property Usercredentials $user
 */
class Eventseendetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'eventseendetails';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['eventid', 'userid'], 'required'],
            [['eventid', 'userid', 'institutionid', 'viewedstatus', 'deviceid'], 'integer'],
            [['vieweddate'], 'safe'],
            [['type'], 'string', 'max' => 1],
            [['deviceid'], 'exist', 'skipOnError' => true, 'targetClass' => Devicedetails::className(), 'targetAttribute' => ['deviceid' => 'id']],
            [['eventid'], 'exist', 'skipOnError' => true, 'targetClass' => Events::className(), 'targetAttribute' => ['eventid' => 'id']],
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
            'eventid' => 'Eventid',
            'userid' => 'Userid',
            'institutionid' => 'Institutionid',
            'viewedstatus' => 'Viewedstatus',
            'deviceid' => 'Deviceid',
            'vieweddate' => 'Vieweddate',
            'type' => 'Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevice()
    {
        return $this->hasOne(Devicedetails::className(), ['id' => 'deviceid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(Events::className(), ['id' => 'eventid']);
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
