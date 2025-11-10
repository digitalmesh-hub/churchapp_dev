<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "bevco_order".
 *
 * @property int $id
 * @property int $member_id
 * @property int $institution_id
 * @property int $slot_id
 * @property string $order_date
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Member $member
 * @property Institution $institution
 * @property Usercredentials $createdBy
 * @property Usercredentials $updatedBy
 */
class BevcoOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bevco_order';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'institution_id' => 'Institution ID',
            'slot_id' => 'Slot ID',
            'order_date' => 'Order Date',
            'status' => 'Status',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'member_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institution_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlot()
    {
        return $this->hasOne(BevcoSlots::className(), ['id' => 'slot_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
    */
    public function getItems()
    {
        return $this->hasMany(BevcoOrderItem::className(), ['order_id' => 'id']);
    }
}
