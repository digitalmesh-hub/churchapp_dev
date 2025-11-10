<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "qurbana".
 *
 * @property int $id
 * @property int $qurbana_type_id
 * @property int $member_id
 * @property int $institution_id
 * @property string $description
 * @property string $created_at
 * @property int $qurbana_date
 * 
 *
 * @property Qurbanatype $qurbana_type
 * @property Institution $institution
 * @property Member $member
 */
class Qurbana extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qurbana';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['qurbana_type_id', 'member_id', 'institution_id', 'qurbana_date'], 'required'],
            [['qurbana_type_id', 'member_id', 'institution_id'], 'integer'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 50],
            [['qurbana_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => QurbanaType::className(), 'targetAttribute' => ['qurbana_type_id' => 'id']],
            [['institution_id'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institution_id' => 'id']],
            [['member_id'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['member_id' => 'memberid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Id',
            'qurbana_type_id' => 'Qurbana Type Id',
            'member_id' => 'Member id',
            'description' => 'Description',
            'created_at' => 'Created At',
            'qurbana_date' => 'Qurbana Date',
            'institution_id' => 'Institution Id',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQurbanatype()
    {
        return $this->hasOne(QurbanaType::className(), ['id' => 'qurbana_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institution_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'member_id']);
    }

    /**
     * Save Qurbana request.
     *
     * @param int $memberId
     * @param int $institutionId
     * @param int $qbType
     * @param date $qbDate
     * @return bool Whether the request was successfully saved
     */
    public function saveQurbanaRequest($memberId, $institutionId, $qbType, $qbDate,$qbName = '')
    {
        date_default_timezone_set('Asia/Kolkata'); //Convert it to user timezone
  		$this->member_id = $memberId;
        $this->institution_id = $institutionId;
        $this->qurbana_type_id = $qbType;
        $this->qurbana_date = $qbDate;
        $this->name = $qbName;
        $this->created_at = date('Y-m-d H:i:s');
        if (!$this->save()) { 
            $errors = $this->getErrors();
            throw new \yii\web\UnprocessableEntityHttpException(json_encode($errors));
        }
    
        return true; 
    }


    /**
     * Get the count of Qurbana requests for today for a specific member and institution.
     *
     * @param int $memberId
     * @param int $institutionId
     * @return int The number of Qurbana requests for the given member and institution on today's date.
     */
    public function getPresentQurbanaCount($memberId, $institutionId)
    {
        return self::find()
            ->where([
                'qurbana_date' => date('Y-m-d'),
                'member_id' => $memberId,
                'institution_id' => $institutionId
            ])
            ->count();
    }
}
