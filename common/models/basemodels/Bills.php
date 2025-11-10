<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "bills".
 *
 * @property int $billid
 * @property string $transactiondate
 * @property string $transactiontype
 * @property string $description
 * @property string $debit
 * @property string $credit
 * @property string $amount
 * @property string $voucher
 * @property string $voucherType
 * @property int $type
 * @property string $memberNo
 * @property int $year
 * @property int $userid
 * @property int $month
 * @property int $institutionid
 * @property int $memberid
 * @property string $newmembernum
 *
 * @property BillReceipt[] $billReceipts
 * @property Institution $institution
 * @property Member $member
 * @property Usercredentials $user
 * @property Cheque[] $cheques
 */
class Bills extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bills';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['transactiondate'], 'safe'],
            [['type', 'year', 'userid', 'month', 'institutionid', 'memberid'], 'integer'],
            [['transactiontype', 'debit', 'credit', 'amount', 'voucher', 'newmembernum'], 'string', 'max' => 45],
            [['description'], 'string', 'max' => 500],
            [['voucherType'], 'string', 'max' => 50],
            [['memberNo'], 'string', 'max' => 25],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => UserCredentials::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'billid' => 'Billid',
            'transactiondate' => 'Transactiondate',
            'transactiontype' => 'Transactiontype',
            'description' => 'Description',
            'debit' => 'Debit',
            'credit' => 'Credit',
            'amount' => 'Amount',
            'voucher' => 'Voucher',
            'voucherType' => 'Voucher Type',
            'type' => 'Type',
            'memberNo' => 'Member No',
            'year' => 'Year',
            'userid' => 'Userid',
            'month' => 'Month',
            'institutionid' => 'Institutionid',
            'memberid' => 'Memberid',
            'newmembernum' => 'Newmembernum',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillReceipts()
    {
        return $this->hasMany(BillReceipt::className(), ['billId' => 'billid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UserCredentials::className(), ['id' => 'userid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCheques()
    {
        return $this->hasMany(Cheque::className(), ['BillId' => 'billid']);
    }
}
