<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Ordertax;

/**
 * This is the model class for table "ordertax".
 *
 * @property int $ordertaxid
 * @property int $orderid
 * @property int $taxid
 * @property string $taxrate
 *
 * @property Orders $order
 * @property Tax $tax
 */
class ExtendedOrdertax extends Ordertax
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ordertax';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['orderid', 'taxid', 'taxrate'], 'required'],
            [['orderid', 'taxid'], 'integer'],
            [['taxrate'], 'number'],
            [['orderid'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::className(), 'targetAttribute' => ['orderid' => 'orderid']],
            [['taxid'], 'exist', 'skipOnError' => true, 'targetClass' => Tax::className(), 'targetAttribute' => ['taxid' => 'taxid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ordertaxid' => 'Ordertaxid',
            'orderid' => 'Orderid',
            'taxid' => 'Taxid',
            'taxrate' => 'Taxrate',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Orders::className(), ['orderid' => 'orderid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTax()
    {
        return $this->hasOne(Tax::className(), ['taxid' => 'taxid']);
    }
}
