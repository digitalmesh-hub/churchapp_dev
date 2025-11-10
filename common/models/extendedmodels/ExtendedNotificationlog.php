<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Notificationlog;

/**
 * This is the model class for table "notificationlog".
 *
 * @property int $notificationlogid
 * @property int $deviceid
 * @property int $userid
 * @property int $eventid
 * @property int $memberid
 * @property string $notificationtype
 * @property int $institutionid
 * @property string $CreatedDateTime
 * @property int $CreatedBy
 *
 * @property Usercredentials $createdBy
 * @property Devicedetails $device
 * @property Events $event
 * @property Institution $institution
 * @property Member $member
 * @property Usercredentials $user
 */
class ExtendedNotificationlog extends Notificationlog
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notificationlog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['deviceid', 'institutionid', 'CreatedDateTime', 'CreatedBy'], 'required'],
            [['deviceid', 'userid', 'eventid', 'memberid', 'institutionid', 'CreatedBy'], 'integer'],
            [['CreatedDateTime'], 'safe'],
            [['notificationtype'], 'string', 'max' => 2],
            [['CreatedBy'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['CreatedBy' => 'id']],
            [['deviceid'], 'exist', 'skipOnError' => true, 'targetClass' => Devicedetails::className(), 'targetAttribute' => ['deviceid' => 'id']],
            [['eventid'], 'exist', 'skipOnError' => true, 'targetClass' => Events::className(), 'targetAttribute' => ['eventid' => 'id']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'notificationlogid' => 'Notificationlogid',
            'deviceid' => 'Deviceid',
            'userid' => 'Userid',
            'eventid' => 'Eventid',
            'memberid' => 'Memberid',
            'notificationtype' => 'Notificationtype',
            'institutionid' => 'Institutionid',
            'CreatedDateTime' => 'Created Date Time',
            'CreatedBy' => 'Created By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'CreatedBy']);
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
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
    }
}
