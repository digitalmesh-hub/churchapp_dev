<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "bill_receipt".
 *
 * @property int $id
 * @property int $institutionId
 * @property int $billId
 * @property int $receiptNo
 *
 * @property Bills $bill
 * @property Institution $institution
 */
class BillReceipt extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bill_receipt';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['institutionId', 'billId', 'receiptNo'], 'required'],
            [['institutionId', 'billId', 'receiptNo'], 'integer'],
            [['billId'], 'exist', 'skipOnError' => true, 'targetClass' => Bills::className(), 'targetAttribute' => ['billId' => 'billid']],
            [['institutionId'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'institutionId' => 'Institution ID',
            'billId' => 'Bill ID',
            'receiptNo' => 'Receipt No',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBill()
    {
        return $this->hasOne(Bills::className(), ['billid' => 'billId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionId']);
    }
}
