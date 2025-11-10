<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "bevco_slots".
 *
 * @property int $id
 * @property int $institution_id
 * @property string $slot_date
 * @property string $start_time
 * @property string $end_time
 * @property int $slot_number
 */
class BevcoSlots extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bevco_slots';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['institution_id', 'slot_number'], 'integer'],
            [['slot_date', 'start_time', 'end_time', 'slot_number'], 'required'],
            [['slot_date', 'start_time', 'end_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'institution_id' => 'Institution ID',
            'slot_date' => 'Slot Date',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'slot_number' => 'Slot Number',
        ];
    }
}
