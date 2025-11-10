<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "ordernotificationsent".
 *
 * @property int $ordernotificationid
 * @property int $orderid
 * @property int $orderstatus
 * @property int $memberid
 * @property string $membertype
 *
 * @property Member $member
 * @property Orders $order
 */
class Ordernotificationsent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ordernotificationsent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['orderid', 'orderstatus', 'memberid', 'membertype'], 'required'],
            [['orderid', 'orderstatus', 'memberid'], 'integer'],
            [['membertype'], 'string', 'max' => 1],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
            [['orderid'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::className(), 'targetAttribute' => ['orderid' => 'orderid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ordernotificationid' => 'Ordernotificationid',
            'orderid' => 'Orderid',
            'orderstatus' => 'Orderstatus',
            'memberid' => 'Memberid',
            'membertype' => 'Membertype',
        ];
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
    public function getOrder()
    {
        return $this->hasOne(Orders::className(), ['orderid' => 'orderid']);
    }
}
