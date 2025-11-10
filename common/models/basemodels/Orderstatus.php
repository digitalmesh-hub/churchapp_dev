<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "orderstatus".
 *
 * @property int $id
 * @property string $status
 * @property int $statusid
 */
class Orderstatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orderstatus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'statusid'], 'required'],
            [['statusid'], 'integer'],
            [['status'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'statusid' => 'Statusid',
        ];
    }
}
