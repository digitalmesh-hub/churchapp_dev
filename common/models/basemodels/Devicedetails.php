<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "devicedetails".
 *
 * @property int $id
 * @property string $deviceid
 * @property string $deviceidentifier
 * @property int $userid
 * @property string $registeredon
 * @property int $active
 * @property string $usertype
 * @property int $institutionid
 * @property string $devicetype
 * @property string $appversion
 *
 * @property Usercredentials $user
 * @property Institution $institution
 * @property Eventseendetails[] $eventseendetails
 * @property Notificationlog[] $notificationlogs
 */
class Devicedetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'devicedetails';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'usertype', 'devicetype', 'appversion'], 'required'],
            [['userid', 'institutionid'], 'integer'],
            [['registeredon'], 'safe'],
            [['deviceid', 'deviceidentifier'], 'string', 'max' => 1000],
            [['active'], 'string', 'max' => 4],
            [['usertype'], 'string', 'max' => 1],
            [['devicetype'], 'string', 'max' => 45],
            [['appversion'], 'string', 'max' => 5],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['userid' => 'id']],
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
            'deviceid' => 'Deviceid',
            'deviceidentifier' => 'Deviceidentifier',
            'userid' => 'Userid',
            'registeredon' => 'Registeredon',
            'active' => 'Active',
            'usertype' => 'Usertype',
            'institutionid' => 'Institutionid',
            'devicetype' => 'Devicetype',
            'appversion' => 'Appversion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'userid']);
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
    public function getEventseendetails()
    {
        return $this->hasMany(Eventseendetails::className(), ['deviceid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationlogs()
    {
        return $this->hasMany(Notificationlog::className(), ['deviceid' => 'id']);
    }
}
