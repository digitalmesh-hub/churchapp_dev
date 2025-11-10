<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "committee_period".
 *
 * @property int $committee_period_id
 * @property string $period_from
 * @property string $period_to
 * @property int $active
 * @property int $institutionid
 * @property string $createddatetime
 * @property int $committeegroupid
 *
 * @property Committee[] $committees
 * @property Committeegroup $committeegroup
 * @property Institution $institution
 */
class CommitteePeriod extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'committee_period';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['period_from', 'period_to', 'active', 'institutionid', 'createddatetime'], 'required'],
            [['period_from', 'period_to', 'createddatetime'], 'safe'],
            [['institutionid', 'committeegroupid'], 'integer'],
            [['active'], 'string', 'max' => 4],
            [['committeegroupid'], 'exist', 'skipOnError' => true, 'targetClass' => Committeegroup::className(), 'targetAttribute' => ['committeegroupid' => 'committeegroupid']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'committee_period_id' => 'Committee Period ID',
            'period_from' => 'Period From',
            'period_to' => 'Period To',
            'active' => 'Active',
            'institutionid' => 'Institutionid',
            'createddatetime' => 'Createddatetime',
            'committeegroupid' => 'Committeegroupid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommittees()
    {
        return $this->hasMany(Committee::className(), ['committeeperiodid' => 'committee_period_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommitteegroup()
    {
        return $this->hasOne(Committeegroup::className(), ['committeegroupid' => 'committeegroupid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionid']);
    }
}
