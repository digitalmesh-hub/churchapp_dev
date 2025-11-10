<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\BillReceipt;

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
class ExtendedBillReceipt extends BillReceipt
{
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
     * Gets the receipt number of the given bill Id
     * @param  integer $billId Bill Id
     * @return integer         Receipt number
     */
    public static function getReceiptNo($billId)
    {
        $receiptData = self::getReceitData($billId);

        // Gets receipt no if already exists, 
        // else creates a new receipt number and returns it.
        if (!empty($receiptData)) {
            return $receiptData->receiptNo;
        } else {
            $receiptNo = self::setReceiptNo($billId);
            return is_numeric($receiptNo) ? $receiptNo : false;
        }
    }

     /**
     * Generates new receipt number and save it in the `bill_receipt` table
     * @param  integer $billId Bill Id
     * @return boolean
     */
    public static function setReceiptNo($billId)
    {   
        $receiptNo = null;

        // Gets receipt data matching bill id and institution id
        $receiptData = self::getReceitData($billId);

        // Creates new receipt no and returns it, if not exits
        if (empty($receiptData)) {
            $lastReceiptData = self::find()->where([
                'institutionId' => Yii::$app->user->identity->institutionid
            ])->orderBy('receiptNo DESC')->one();
            
            if (!empty($lastReceiptData) && !empty($lastReceiptData->receiptNo)) {
                $receiptNo = $lastReceiptData->receiptNo + 1;
            } else {
                $receiptNo = Yii::$app->params['defaultReceiptNo'];
            }

            $receiptModel = new BillReceipt();
            $receiptModel->institutionId = Yii::$app->user->identity->institutionid;
            $receiptModel->billId = $billId;
            $receiptModel->receiptNo = $receiptNo;

            if ($receiptModel->save()) {
                return $receiptNo;
            } else {
                return false;
                Yii::error('Failed save receiptNo. Error:'.
                    json_encode($receiptModel->getErrors())
                );
            }
        } else {
            return $receiptData->receiptNo;
        }
    }

    /**
     * Gets the receipt details from database
     * @param  integer $billId Bill Id
     * @return object          Receipt Data
     */
    public static function getReceitData($billId)
    {
        return self::findOne([
            'billId' => $billId,
            'institutionId' => Yii::$app->user->identity->institutionid
        ]);
    }
}
