<?php
namespace common\models\custom;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 */
class BevcoCategoryCustomData extends Model
{
   
    public $maximum_allowed_per_interval;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [['interval_unit', 'maximum_allowed_per_interval', 'interval_duration'], 'required'],
            // [['interval_duration', 'maximum_allowed_per_interval'], 'integer', 'min' => 1]
            [['maximum_allowed_per_interval'], 'required'],
            [['maximum_allowed_per_interval'], 'integer', 'min' => 1]
        ];
    }

    

}
   