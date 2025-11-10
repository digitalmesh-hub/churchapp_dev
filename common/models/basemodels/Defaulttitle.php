<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "defaulttitle".
 *
 * @property int $titleid
 * @property string $description
 */
class Defaulttitle extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'defaulttitle';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'string', 'max' => 75],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'titleid' => 'Titleid',
            'description' => 'Description',
        ];
    }
}
