<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "schedulerlog".
 *
 * @property int $id
 * @property string $schedulertype
 * @property string $schedulertime
 */
class Schedulerlog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'schedulerlog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['schedulertype', 'schedulertime'], 'required'],
            [['schedulertime'], 'safe'],
            [['schedulertype'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'schedulertype' => 'Schedulertype',
            'schedulertime' => 'Schedulertime',
        ];
    }
}
