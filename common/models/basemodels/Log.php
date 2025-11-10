<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "log".
 *
 * @property int $id
 * @property string $date
 * @property string $thread
 * @property string $level
 * @property string $logger
 * @property string $message
 * @property string $exception
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'thread', 'level', 'logger', 'message'], 'required'],
            [['date'], 'safe'],
            [['thread', 'logger'], 'string', 'max' => 255],
            [['level'], 'string', 'max' => 50],
            [['message'], 'string', 'max' => 4000],
            [['exception'], 'string', 'max' => 2000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
            'thread' => 'Thread',
            'level' => 'Level',
            'logger' => 'Logger',
            'message' => 'Message',
            'exception' => 'Exception',
        ];
    }
}
