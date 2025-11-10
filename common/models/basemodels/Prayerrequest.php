<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "prayerrequest".
 *
 * @property int $prayerrequestid
 * @property int $userid
 * @property int $institutionid
 * @property string $subject
 * @property string $description
 * @property string $createdtime
 * @property int $isresponded
 *
 * @property Institution $institution
 * @property Usercredentials $user
 * @property Prayerrequestnotification[] $prayerrequestnotifications
 * @property Prayerrequestnotificationsent[] $prayerrequestnotificationsents
 */
class Prayerrequest extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'prayerrequest';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'institutionid'], 'integer'],
            [['createdtime'], 'safe'],
            [['subject'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 250],
            [['isresponded'], 'string', 'max' => 4],
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
            'prayerrequestid' => 'Prayerrequestid',
            'userid' => 'Userid',
            'institutionid' => 'Institutionid',
            'subject' => 'Subject',
            'description' => 'Description',
            'createdtime' => 'Createdtime',
            'isresponded' => 'Isresponded',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerrequestnotifications()
    {
        return $this->hasMany(Prayerrequestnotification::className(), ['prayerrequestid' => 'prayerrequestid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerrequestnotificationsents()
    {
        return $this->hasMany(Prayerrequestnotificationsent::className(), ['prayerrequestid' => 'prayerrequestid']);
    }
}
