<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "timezoneinfo".
 *
 * @property int $id
 * @property string $timezonename
 * @property string $displaystring
 */
class TimeZoneInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'timezoneinfo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['timezonename', 'displaystring'], 'required'],
            [['timezonename'], 'string', 'max' => 250],
            [['displaystring'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'timezonename' => 'Timezonename',
            'displaystring' => 'Displaystring',
        ];
    }
}
