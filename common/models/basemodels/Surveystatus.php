<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "surveystatus".
 *
 * @property int $surveystatusid
 * @property int $surveyid
 * @property int $memberid
 * @property string $membertype
 * @property string $token
 * @property int $isattended
 *
 * @property Member $member
 * @property Survey $survey
 */
class Surveystatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'surveystatus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['surveyid', 'memberid', 'membertype', 'token'], 'required'],
            [['surveyid', 'memberid', 'isattended'], 'integer'],
            [['membertype'], 'string', 'max' => 1],
            [['token'], 'string', 'max' => 100],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
            [['surveyid'], 'exist', 'skipOnError' => true, 'targetClass' => Survey::className(), 'targetAttribute' => ['surveyid' => 'surveyid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'surveystatusid' => 'Surveystatusid',
            'surveyid' => 'Surveyid',
            'memberid' => 'Memberid',
            'membertype' => 'Membertype',
            'token' => 'Token',
            'isattended' => 'Isattended',
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
    public function getSurvey()
    {
        return $this->hasOne(Survey::className(), ['surveyid' => 'surveyid']);
    }
}
