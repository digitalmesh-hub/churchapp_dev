<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "addresstype".
 *
 * @property int $id
 * @property string $type
 *
 * @property Settings[] $settings
 */
class Addresstype extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'addresstype';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSettings()
    {
        return $this->hasMany(Settings::className(), ['addresstypeid' => 'id']);
    }
}
