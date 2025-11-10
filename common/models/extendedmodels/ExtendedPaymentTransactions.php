<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\PaymentTransactions;

/**
 * This is the model class for table "payment_transactions".
 *
 * @property int $id
 * @property string $txnid
 * @property string $guid
 * @property int $memberId
 * @property string $amount
 * @property string $description
 * @property string $status
 * @property string $error_message
 * @property string $response
 * @property string $created
 * @property string $updated
 *
 * @property Member $member
 */
class ExtendedPaymentTransactions extends PaymentTransactions
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['txnid', 'guid', 'memberId', 'amount', 'status'], 'required'],
            [['memberId'], 'integer'],
            [['amount'], 'number'],
            [['error_message', 'response'], 'string'],
            [['created', 'updated'], 'safe'],
            [['txnid', 'guid', 'description', 'status'], 'string', 'max' => 45],
            [['txnid'], 'unique'],
            [['memberId'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberId' => 'memberid']],
        ];
    }
}
