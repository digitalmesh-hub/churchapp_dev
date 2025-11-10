<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Cheque;

/**
 * This is the extended model class for table "cheque".
 *
 * @property int $Id
 * @property int $BillId
 * @property string $paymentType
 * @property string $ChequeNo
 * @property string $NeftNo
 * @property string $Bank
 * @property string $Branch
 * @property string $Date
 * @property string $UpiId
 *
 * @property Bills $bill
 */
class ExtendedCheque extends Cheque
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['BillId'], 'required'],
            [['BillId'], 'integer'],
            [['Date'], 'safe'],
            [['paymentType', 'ChequeNo', 'NeftNo', 'Branch','UpiId'], 'string', 'max' => 45],
            [['Bank'], 'string', 'max' => 200],
            [['BillId'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendedBills::className(), 'targetAttribute' => ['BillId' => 'billid']],
        ];
    }
}
