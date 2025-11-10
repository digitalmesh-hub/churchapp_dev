<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "survey".
 *
 * @property int $surveyid
 * @property string $description
 * @property int $institutionid
 * @property int $active
 * @property string $createddatetime
 * @property int $createdby
 * @property string $modifieddatetime
 * @property int $modifiedby
 *
 * @property Usercredentials $createdby0
 * @property Institution $institution
 * @property Usercredentials $modifiedby0
 * @property Surveystatus[] $surveystatuses
 */
class Survey extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'survey';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['surveyid', 'description', 'institutionid', 'createddatetime', 'createdby'], 'required'],
            [['surveyid', 'institutionid', 'createdby', 'modifiedby'], 'integer'],
            [['createddatetime', 'modifieddatetime'], 'safe'],
            [['description'], 'string', 'max' => 250],
            [['active'], 'string', 'max' => 4],
            [['surveyid'], 'unique'],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['modifiedby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['modifiedby' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'surveyid' => 'Surveyid',
            'description' => 'Description',
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
    public function getModifiedby0()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'modifiedby']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveystatuses()
    {
        return $this->hasMany(Surveystatus::className(), ['surveyid' => 'surveyid']);
    }
}
