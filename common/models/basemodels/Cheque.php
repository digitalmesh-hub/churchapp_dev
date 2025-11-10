<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "cheque".
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
class Cheque extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cheque';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['BillId'], 'required'],
            [['BillId'], 'integer'],
            [['Date'], 'safe'],
            [['paymentType', 'ChequeNo', 'NeftNo', 'Branch', 'UpiId'], 'string', 'max' => 45],
            [['Bank'], 'string', 'max' => 200],
            [['BillId'], 'exist', 'skipOnError' => true, 'targetClass' => Bills::className(), 'targetAttribute' => ['BillId' => 'billid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'BillId' => 'Bill ID',
            'paymentType' => 'Payment Type',
            'ChequeNo' => 'Cheque No',
            'NeftNo' => 'Neft No',
            'Bank' => 'Bank',
            'Branch' => 'Branch',
            'Date' => 'Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBill()
    {
        return $this->hasOne(Bills::className(), ['billid' => 'BillId']);
    }
}
