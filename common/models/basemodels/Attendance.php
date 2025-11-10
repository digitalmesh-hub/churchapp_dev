<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "attendance".
 *
 * @property int $attendanceid
 * @property int $memberid
 * @property int $attendance
 * @property string $attandancedate
 * @property int $institutionid
 * @property int $createdby
 * @property string $createddatetime
 * @property int $modifiedby
 * @property string $modifieddatetime
 *
 * @property Usercredentials $createdby0
 * @property Institution $institution
 * @property Member $member
 * @property Usercredentials $modifiedby0
 */
class Attendance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'attendance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['memberid', 'attandancedate', 'institutionid'], 'required'],
            [['memberid', 'institutionid', 'createdby', 'modifiedby'], 'integer'],
            [['attandancedate', 'createddatetime', 'modifieddatetime'], 'safe'],
            [['attendance'], 'string', 'max' => 4],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
            [['modifiedby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['modifiedby' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'attendanceid' => 'Attendanceid',
            'memberid' => 'Memberid',
            'attendance' => 'Attendance',
            'attandancedate' => 'Attandancedate',
            'institutionid' => 'Institutionid',
            'createdby' => 'Createdby',
            'createddatetime' => 'Createddatetime',
            'modifiedby' => 'Modifiedby',
            'modifieddatetime' => 'Modifieddatetime',
        ];
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
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModifiedby0()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'modifiedby']);
    }
}
