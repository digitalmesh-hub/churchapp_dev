<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\BevcoSettings;
use yii\helpers\ArrayHelper;
use ArrayObject;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class ExtendedBevcoSettings extends BevcoSettings
{

    public $start_time;
    public $end_time;
    public $slot_duration;
    public $sales_per_slot;
    public $interval_unit;

    //const INTERVAL_UNIT_DAY = 1;
    const INTERVAL_UNIT_WEEK = 2;
    //const INTERVAL_UNIT_MONTH = 3;


    public $_customDataObject;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['institution_id', 'start_time', 'end_time', 'slot_duration', 'sales_per_slot','interval_unit'], 'required'],
            [['institution_id', 'interval_unit'], 'integer'],
            [['sales_per_slot'], 'integer', 'min' => 1],
            [['created_at', 'updated_at', 'start_time', 'end_time', 'slot_duration'], 'safe'],
            [['slot_duration'], 'integer'],
            [['institution_id'], 'unique'],
            [['institution_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedInstitution::className(), 'targetAttribute' => ['institution_id' => 'id']],
            [['start_time', 'end_time'], 'validateSlot']
        ];
    }

    public function validateSlot($attribute, $params, $validator)
    {
        $start_in_24_hour_format  = date("H:i", strtotimeNew($this->start_time));
        $end_in_24_hour_format  = date("H:i", strtotimeNew($this->end_time));
        if (strtotimeNew($start_in_24_hour_format) > strtotimeNew($end_in_24_hour_format)) {
            $this->addError('start_time', 'Start Time should be lesser than end time');
        }
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function getDurations()
    {
        $arr = [];
        foreach (range(10, 120, 10) as $number) {
            $arr[$number] = $number;
        }

        return $arr;
    }

    public function load($data, $formName = null)
    {
        if (!$ok = parent::load($data,$formName))
            return false;

        $this->custom_data = json_encode(ArrayHelper::toArray($this->pick($this, 'slot_duration', 'start_time', 'end_time', 'sales_per_slot', 'interval_unit')));

        return true;
    }

    private function pick($data, ...$keys)
    {
        $result = new \stdClass();
        foreach ($keys as $key) {
            if (property_exists($data, $key)) {
                $result->$key = $data->$key;
            }
        }
        return $result;
    }

    public function getIntervalUnit($key = null)
    {
        $arr = [
            //self::INTERVAL_UNIT_DAY => Yii::t('app', 'Days'),
            self::INTERVAL_UNIT_WEEK => Yii::t('app', 'Weekly'),
            //self::INTERVAL_UNIT_MONTH => Yii::t('app', 'Monthly')
        ];
        return isset($key) ? ArrayHelper::getValue($arr, $key) : $arr;
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        if ($this->hasAttribute('custom_data')) {
            $startingData = $this->custom_data ? json_decode($this->custom_data,JSON_OBJECT_AS_ARRAY) : [];
            $this->_customDataObject = new ArrayObject($startingData);
        }
    }

    public function convertCustomDataToModels()
    {
        if($this->_customDataObject) {
            foreach ($this->_customDataObject as $key => $value) {
                if(property_exists($this, $key)){
                    $this->$key = $value;
                }
            }
        }
    }
}
