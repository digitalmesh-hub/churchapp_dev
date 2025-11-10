<?php

namespace common\models\basemodels;

use Yii;

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
class PaymentTransactions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_transactions';
    }

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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'txnid' => 'Txnid',
            'guid' => 'Guid',
            'memberId' => 'Member ID',
            'amount' => 'Amount',
            'description' => 'Description',
            'status' => 'Status',
            'error_message' => 'Error Message',
            'response' => 'Response',
            'created' => 'Created',
            'updated' => 'Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'memberId']);
    }
}
