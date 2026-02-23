<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "member_connection".
 *
 * @property int $id
 * @property int $member_id
 * @property int $connected_member_id
 * @property string $created_at
 *
 * @property Member $member
 * @property Member $connectedMember
 */
class MemberConnection extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_connection';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'connected_member_id'], 'required'],
            [['member_id', 'connected_member_id'], 'integer'],
            [['created_at'], 'safe'],
            [['member_id', 'connected_member_id'], 'unique', 'targetAttribute' => ['member_id', 'connected_member_id']],
            [['member_id'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['member_id' => 'memberid']],
            [['connected_member_id'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['connected_member_id' => 'memberid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'connected_member_id' => 'Connected Member ID',
            'created_at' => 'Created At',
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
    public function getConnectedMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'connected_member_id']);
    }
}
