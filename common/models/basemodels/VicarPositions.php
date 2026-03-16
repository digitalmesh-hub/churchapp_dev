<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "vicar_positions".
 *
 * @property int $id
 * @property string $position_name
 * @property string $position_description
 * @property int $is_main_vicar
 * @property int $display_order
 * @property int $institutionid
 * @property int $active
 * @property int $createdby
 * @property int $modifiedby
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Institution $institution
 * @property Usercredentials $createdby0
 * @property Usercredentials $modifiedby0
 * @property VicarDirectory[] $vicarDirectories
 */
class VicarPositions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vicar_positions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['position_name', 'institutionid', 'createdby'], 'required'],
            [['is_main_vicar', 'display_order', 'institutionid', 'active', 'createdby', 'modifiedby'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['position_name'], 'string', 'max' => 100],
            [['position_description'], 'string', 'max' => 255],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
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
            'position_name' => 'Position Name',
            'position_description' => 'Position Description',
            'is_main_vicar' => 'Is Main Vicar',
            'display_order' => 'Display Order',
            'institutionid' => 'Institution ID',
            'active' => 'Active',
            'createdby' => 'Created By',
            'modifiedby' => 'Modified By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVicarDirectories()
    {
        return $this->hasMany(VicarDirectory::className(), ['vicar_position_id' => 'id']);
    }
}
