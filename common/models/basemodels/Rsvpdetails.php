<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "rsvpdetails".
 *
 * @property int $id
 * @property int $rsvpid
 * @property int $userid
 * @property string $membername
 * @property int $rsvpvalue
 * @property string $mobile
 * @property int $membercount
 * @property int $childrencount
 * @property int $guestcount
 * @property int $memberid
 * @property string $acksentdatetime
 * @property string $createddatetime
 *
 * @property Member $member
 * @property Events $rsvp
 * @property Usercredentials $user
 * @property Rsvpnotification[] $rsvpnotifications
 * @property Rsvpnotificationsent[] $rsvpnotificationsents
 */
class Rsvpdetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rsvpdetails';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rsvpid', 'userid', 'createddatetime'], 'required'],
            [['rsvpid', 'userid', 'rsvpvalue', 'membercount', 'childrencount', 'guestcount', 'memberid'], 'integer'],
            [['acksentdatetime', 'createddatetime'], 'safe'],
            [['membername'], 'string', 'max' => 45],
            [['mobile'], 'string', 'max' => 13],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
            [['rsvpid'], 'exist', 'skipOnError' => true, 'targetClass' => Events::className(), 'targetAttribute' => ['rsvpid' => 'id']],
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
            'rsvpid' => 'Rsvpid',
            'userid' => 'Userid',
            'membername' => 'Membername',
            'rsvpvalue' => 'Rsvpvalue',
            'mobile' => 'Mobile',
            'membercount' => 'Membercount',
            'childrencount' => 'Childrencount',
            'guestcount' => 'Guestcount',
            'memberid' => 'Memberid',
            'acksentdatetime' => 'Acksentdatetime',
            'createddatetime' => 'Createddatetime',
        ];
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
    public function getRsvp()
    {
        return $this->hasOne(Events::className(), ['id' => 'rsvpid']);
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
    public function getRsvpnotifications()
    {
        return $this->hasMany(Rsvpnotification::className(), ['rsvpid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRsvpnotificationsents()
    {
        return $this->hasMany(Rsvpnotificationsent::className(), ['rsvpid' => 'id']);
    }
}
