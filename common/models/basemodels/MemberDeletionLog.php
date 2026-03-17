<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "member_deletion_log".
 *
 * @property int $id
 * @property int $institution_id
 * @property int $member_id
 * @property string $membership_no
 * @property string $name
 * @property string $email
 * @property string $relation
 * @property string $reason
 * @property int $deleted_by
 * @property string $deleted_by_name
 * @property string $deleted_at
 *
 * @property Institution $institution
 * @property Usercredentials $deletedBy
 */
class MemberDeletionLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_deletion_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['institution_id', 'member_id', 'membership_no', 'name', 'relation', 'reason', 'deleted_by'], 'required'],
            [['institution_id', 'member_id', 'deleted_by'], 'integer'],
            [['relation', 'reason'], 'string'],
            [['deleted_at'], 'safe'],
            [['membership_no'], 'string', 'max' => 75],
            [['name', 'deleted_by_name'], 'string', 'max' => 200],
            [['email'], 'string', 'max' => 150],
            [['relation'], 'in', 'range' => ['member', 'spouse']],
            [['institution_id'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institution_id' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['deleted_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'institution_id' => 'Institution ID',
            'member_id' => 'Member ID',
            'membership_no' => 'Membership No',
            'name' => 'Name',
            'email' => 'Email',
            'relation' => 'Relation',
            'reason' => 'Reason',
            'deleted_by' => 'Deleted By',
            'deleted_by_name' => 'Deleted By Name',
            'deleted_at' => 'Deleted At',
        ];
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
    public function getDeletedBy()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'deleted_by']);
    }
}
