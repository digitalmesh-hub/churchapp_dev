<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\BevcoOrder;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class ExtendedBevcoOrder extends BevcoOrder
{
    
	const STATUS_PLACED = 1;
	const STATUS_COMPLETED = 2;
	const STATUS_CANCELLED = 3;
	const STATUS_EXPIRED = 4;

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

     /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'required'],
        ];
    }

    public function getStatuses($state = null, $without = [])
    {
        $states = [
            self::STATUS_PLACED => Yii::t('app', 'Placed'),
            self::STATUS_CANCELLED => Yii::t('app', 'Cancelled'),
            self::STATUS_EXPIRED => Yii::t('app', 'Expired'),
            self::STATUS_COMPLETED => Yii::t('app', 'Completed'),
        ];

        if (!empty($without)) {
           foreach ($without as $v) {
               unset($states[$v]);
           }
        }

        return $state ? ArrayHelper::getValue($states, $state) : $states;
    }

    public function getFullSlot()
    {
        return date("g:i a", strtotimeNew($this->slot->start_time)) .'-'.date("g:i a", strtotimeNew($this->slot->end_time));
    }    

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMemberFullname()
    {   
        $name = "";
        $name .= $this->member->firstName;

        if($this->member->middleName)
            $name .= " ".$this->member->middleName;

        if($this->member->lastName)
            $name .= " ".$this->member->lastName;

        return $name ."(".$this->member->memberno.")";
    }
}
