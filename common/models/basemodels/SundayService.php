<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "sunday_service".
 *
 * @property int $id
 * @property string $service_date
 * @property string $content
 * @property int $institution_id
 * @property int $active
 * @property int $created_by
 * @property string $created_at
 * @property int $updated_by
 * @property string $updated_at
 *
 * @property Institution $institution
 * @property Usercredentials $createdBy
 * @property Usercredentials $updatedBy
 */
class SundayService extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sunday_service';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_date', 'content', 'institution_id', 'created_by'], 'required'],
            [['service_date', 'created_at', 'updated_at'], 'safe'],
            [['content'], 'string'],
            [['institution_id', 'active', 'created_by', 'updated_by'], 'integer'],
            [['active'], 'integer', 'max' => 4],
            [['institution_id'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institution_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_date' => 'Service Date',
            'content' => 'Content',
            'institution_id' => 'Institution ID',
            'active' => 'Active',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
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
    public function getCreatedBy()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'updated_by']);
    }
}
