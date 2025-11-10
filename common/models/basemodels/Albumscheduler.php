<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "albumscheduler".
 *
 * @property int $id
 * @property string $lastschedulerruntime
 */
class Albumscheduler extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'albumscheduler';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lastschedulerruntime'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lastschedulerruntime' => 'Lastschedulerruntime',
        ];
    }
}
