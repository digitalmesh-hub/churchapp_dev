<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "events".
 *
 * @property int $id
 * @property int $institutionid
 * @property string $notehead
 * @property string $notebody
 * @property string $activitydate
 * @property string $createddate
 * @property string $activatedon
 * @property string $noteurl
 * @property string $eventtype
 * @property int $createduser
 * @property string $venue
 * @property string $time
 * @property string $expirydate
 * @property int $rsvpavailable
 * @property int $modifiedby
 * @property string $modifieddatetime
 * @property int $iseventpublishable
 * @property int $familyunitid
 *
 * @property Album $album
 * @property Usercredentials $createduser0
 * @property Familyunit $familyunit
 * @property Institution $institution
 * @property Usercredentials $modifiedby0
 * @property Eventseendetails[] $eventseendetails
 * @property Notificationlog[] $notificationlogs
 * @property Rsvpdetails[] $rsvpdetails
 */
class Events extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'events';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['institutionid', 'createduser', 'modifiedby', 'familyunitid'], 'integer'],
            [['activitydate', 'createddate', 'activatedon', 'expirydate', 'modifieddatetime','publishedon'], 'safe'],
            [['createduser'], 'required'],
            [['notehead', 'noteurl'], 'string', 'max' => 500],
            [['notebody'], 'string', 'max' => 8000],
            [['eventtype'], 'string', 'max' => 1],
            [['venue'], 'string', 'max' => 150],
            [['time'], 'string', 'max' => 45],
            [['rsvpavailable', 'iseventpublishable'], 'integer', 'max' => 4],
            [['createduser'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createduser' => 'id']],
            [['familyunitid'], 'exist', 'skipOnError' => true, 'targetClass' => Familyunit::className(), 'targetAttribute' => ['familyunitid' => 'familyunitid']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['modifiedby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['modifiedby' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
       
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlbum()
    {
        return $this->hasOne(Album::className(), ['eventid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateduser0()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'createduser']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFamilyunit()
    {
        return $this->hasOne(Familyunit::className(), ['familyunitid' => 'familyunitid']);
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
    public function getEventseendetails()
    {
        return $this->hasMany(Eventseendetails::className(), ['eventid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationlogs()
    {
        return $this->hasMany(Notificationlog::className(), ['eventid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRsvpdetails()
    {
        return $this->hasMany(Rsvpdetails::className(), ['rsvpid' => 'id']);
    }
}
