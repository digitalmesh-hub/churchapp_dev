<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "bevco_settings".
 *
 * @property int $id
 * @property int $institution_id
 * @property string $custom_data
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Institution $institution
 */
class BevcoSettings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bevco_settings';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'institution_id' => 'Institution ID',
            'custom_data' => 'Custom Data',
            'created_at' => 'Created At',
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
}
