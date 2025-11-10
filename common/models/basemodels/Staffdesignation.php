<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "staffdesignation".
 *
 * @property int $staffdesignationid
 * @property string $designation
 * @property int $institutionid
 * @property bool $active
 * @property string $createddatetime
 * @property int $createdby
 * @property string $modifieddatetime
 * @property int $modifiedby
 *
 * @property Institution $institution
 * @property Usercredentials $createdby0
 * @property Usercredentials $modifiedby0
 */
class Staffdesignation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'staffdesignation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['designation', 'institutionid', 'createddatetime', 'createdby'], 'required'],
            [['institutionid', 'createdby', 'modifiedby'], 'integer'],
            [['active'], 'boolean'],
            [['createddatetime', 'modifieddatetime'], 'safe'],
            [['designation'], 'string', 'max' => 100],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
            [['modifiedby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['modifiedby' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'staffdesignationid' => 'Staffdesignationid',
            'designation' => 'Designation',
            'institutionid' => 'Institutionid',
            'active' => 'Active',
            'createddatetime' => 'Createddatetime',
            'createdby' => 'Createdby',
            'modifieddatetime' => 'Modifieddatetime',
            'modifiedby' => 'Modifiedby',
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
    public function getCreatedby0()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'createdby']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModifiedby0()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'modifiedby']);
    }
}
