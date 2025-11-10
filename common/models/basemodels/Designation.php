<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "designation".
 *
 * @property int $designationid
 * @property string $description
 * @property int $designationorder
 * @property int $institutionid
 *
 * @property Committee[] $committees
 * @property Institution $institution
 * @property Institutionstaffdesignation[] $institutionstaffdesignations
 */
class Designation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'designation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['designationorder', 'institutionid'], 'required'],
            [['designationorder', 'institutionid'], 'integer'],
            [['description'], 'string', 'max' => 100],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'designationid' => 'Designationid',
            'description' => 'Description',
            'designationorder' => 'Designationorder',
            'institutionid' => 'Institutionid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommittees()
    {
        return $this->hasMany(Committee::className(), ['designationid' => 'designationid']);
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
    public function getInstitutionstaffdesignations()
    {
        return $this->hasMany(Institutionstaffdesignation::className(), ['staffdesignationid' => 'designationid']);
    }
}
