<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "familyunit".
 *
 * @property int $familyunitid
 * @property string $description
 * @property int $institutionid
 * @property int $active
 *
 * @property Events[] $events
 * @property Institution $institution
 * @property Member[] $members
 */
class Familyunit extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'familyunit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'institutionid'], 'required'],
            [['institutionid'], 'integer'],
            [['description'], 'string', 'max' => 45],
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
            'familyunitid' => 'Familyunitid',
            'description' => 'Family Unit',
            'institutionid' => 'Institutionid',
            'active' => 'Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(Events::className(), ['familyunitid' => 'familyunitid']);
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
    public function getMembers()
    {
        return $this->hasMany(Member::className(), ['familyunitid' => 'familyunitid']);
    }
}
