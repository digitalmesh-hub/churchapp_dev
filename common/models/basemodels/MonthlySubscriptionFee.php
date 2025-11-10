<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "monthly_subscription_fee".
 *
 * @property integer $id
 * @property string $create_at
 * @property double $amount
 * @property string $description
 * @property string $transactionDate
 * @property integer $userId
 * @property integer $institutionId
 * @property integer $status
 */
class MonthlySubscriptionFee extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'monthly_subscription_fee';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_at', 'transactionDate'], 'safe'],
            [['amount', 'description', 'userId', 'institutionId'], 'required'],
            [['amount'], 'number'],
            [['userId', 'institutionId', 'status'], 'integer'],
            [['description'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'create_at' => 'Create At',
            'amount' => 'Amount',
            'description' => 'Description',
            'transactionDate' => 'Transaction Date',
            'userId' => 'User ID',
            'institutionId' => 'Institution ID',
            'status' => 'Status',
        ];
    }
}
