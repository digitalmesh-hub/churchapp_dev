<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\BevcoOrderItem;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class ExtendedBevcoOrderItem extends BevcoOrderItem
{
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
}
