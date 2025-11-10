<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "birthday_anniversary_seendetails".
 *
 * @property int $id
 * @property int $userid
 * @property int $institutionid
 * @property int $viewedstatus
 * @property string $vieweddate
 * @property string $type
 *
 * @property Institution $institution
 * @property Usercredentials $user
 */
class BirthdayAnniversarySeendetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'birthday_anniversary_seendetails';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'institutionid'], 'required'],
            [['userid', 'institutionid', 'viewedstatus'], 'integer'],
            [['vieweddate'], 'safe'],
            [['type'], 'string', 'max' => 1],
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
            'userid' => 'Userid',
            'institutionid' => 'Institutionid',
            'viewedstatus' => 'Viewedstatus',
            'vieweddate' => 'Vieweddate',
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
