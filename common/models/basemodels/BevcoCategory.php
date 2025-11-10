<?php

namespace common\models\basemodels;
use yii\db\ActiveRecord;

use Yii;

/**
 * This is the model class for table "bevco_sub_category".
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $description
 * @property int $is_available
 * @property int $institution_id
 * @property int $created_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property BevcoCategory $category
 * @property Usercredentials $createdBy
 * @property Institution $institution
 */
class BevcoCategory extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bevco_category';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'is_available' => 'Is Available',
            'institution_id' => 'Institution ID',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At'
        ];
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
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institution_id']);
    }
}
