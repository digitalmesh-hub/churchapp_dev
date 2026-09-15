<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "whatsapp_template".
 *
 * @property int $id
 * @property int $institutionid
 * @property string $event_type
 * @property string $meta_template_name
 * @property string $language_code
 * @property string $body_preview
 * @property string $variable_map
 * @property int $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property Institution $institution
 */
class WhatsappTemplate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'whatsapp_template';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['institutionid', 'event_type', 'meta_template_name'], 'required'],
            [['institutionid', 'is_active', 'updated_by'], 'integer'],
            [['event_type'], 'in', 'range' => ['birthday', 'anniversary']],
            [['body_preview'], 'string', 'max' => 1024],
            [['variable_map'], 'safe'],
            [['created_at', 'updated_at'], 'safe'],
            [['meta_template_name'], 'string', 'max' => 128],
            [['language_code'], 'string', 'max' => 10],
            [['institutionid', 'event_type', 'language_code'], 'unique', 'targetAttribute' => ['institutionid', 'event_type', 'language_code']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'institutionid' => 'Institution ID',
            'event_type' => 'Event Type',
            'meta_template_name' => 'Meta Template Name',
            'language_code' => 'Language Code',
            'body_preview' => 'Body Preview',
            'variable_map' => 'Variable Map',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionid']);
    }
}
