<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "billseendetails".
 *
 * @property int $id
 * @property int $memberid
 * @property string $usertype
 * @property int $month
 * @property int $year
 * @property int $institutionid
 * @property string $createddatetime
 *
 * @property Institution $institution
 * @property Member $member
 */
class Billseendetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'billseendetails';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['memberid', 'usertype', 'month', 'year', 'institutionid', 'createddatetime'], 'required'],
            [['memberid', 'month', 'year', 'institutionid'], 'integer'],
            [['createddatetime'], 'safe'],
            [['usertype'], 'string', 'max' => 1],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'memberid' => 'Memberid',
            'usertype' => 'Usertype',
            'month' => 'Month',
            'year' => 'Year',
            'institutionid' => 'Institutionid',
            'createddatetime' => 'Createddatetime',
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
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'memberid']);
    }
}
