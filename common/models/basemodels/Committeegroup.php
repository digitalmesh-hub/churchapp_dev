<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "committeegroup".
 *
 * @property int $committeegroupid
 * @property string $description
 * @property int $institutionid
 * @property int $active
 * @property int $order
 *
 * @property Committee[] $committees
 * @property CommitteePeriod[] $committeePeriods
 * @property Institution $institution
 */
class Committeegroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'committeegroup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'institutionid', 'order'], 'required'],
            [['institutionid', 'order'], 'integer'],
            [['description'], 'string', 'max' => 25],
            [['active'], 'integer', 'max' => 4],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'committeegroupid' => 'Committeegroupid',
            'description' => 'Description',
            'institutionid' => 'Institutionid',
            'active' => 'Active',
            'order' => 'Order',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommittees()
    {
        return $this->hasMany(Committee::className(), ['committeegroupid' => 'committeegroupid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommitteePeriods()
    {
        return $this->hasMany(CommitteePeriod::className(), ['committeegroupid' => 'committeegroupid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionid']);
    }
}
