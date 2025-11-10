<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "qurbana_type".
 *
 * @property int $id
 * @property string $type
 * @property int $status
 *
 * @property Qurbana[] $qurbana
 */
class QurbanaType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qurbana_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Id',
            'type' => 'Name',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQurbana()
    {
        return $this->hasMany(Qurbana::className(), ['id' => 'qurbana_type_id']);
    }
}
