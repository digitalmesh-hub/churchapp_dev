<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "vicar_directory".
 *
 * @property int $id
 * @property int $member_id
 * @property int $vicar_position_id
 * @property int $institution_id
 * @property string $start_date
 * @property string $end_date
 * @property int $is_active
 * @property int $display_order
 * @property string $remarks
 * @property int $createdby
 * @property int $modifiedby
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Member $member
 * @property VicarPositions $vicarPosition
 * @property Institution $institution
 * @property Usercredentials $createdby0
 * @property Usercredentials $modifiedby0
 */
class VicarDirectory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vicar_directory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'vicar_position_id', 'institution_id', 'start_date', 'createdby'], 'required'],
            [['member_id', 'vicar_position_id', 'institution_id', 'is_active', 'display_order', 'createdby', 'modifiedby'], 'integer'],
            [['start_date', 'end_date', 'created_at', 'updated_at'], 'safe'],
            [['remarks'], 'string'],
            [['member_id'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['member_id' => 'memberid']],
            [['vicar_position_id'], 'exist', 'skipOnError' => true, 'targetClass' => VicarPositions::className(), 'targetAttribute' => ['vicar_position_id' => 'id']],
            [['institution_id'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institution_id' => 'id']],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['createdby' => 'id']],
            [['modifiedby'], 'exist', 'skipOnError' => true, 'targetClass' => Usercredentials::className(), 'targetAttribute' => ['modifiedby' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member',
            'vicar_position_id' => 'Vicar Position',
            'institution_id' => 'Institution',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'is_active' => 'Is Active',
            'display_order' => 'Display Order',
            'remarks' => 'Remarks',
            'createdby' => 'Created By',
            'modifiedby' => 'Modified By',
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
    public function getVicarPosition()
    {
        return $this->hasOne(VicarPositions::className(), ['id' => 'vicar_position_id']);
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
    public function getCreatedby0()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'createdby']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModifiedby0()
    {
        return $this->hasOne(Usercredentials::className(), ['id' => 'modifiedby']);
    }
}
