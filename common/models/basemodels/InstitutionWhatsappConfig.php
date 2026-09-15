<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "institution_whatsapp_config".
 *
 * @property int $id
 * @property int $institutionid
 * @property string $waba_id
 * @property string $phone_number_id
 * @property string $display_phone
 * @property string $access_token
 * @property string $token_expires_at
 * @property string $api_version
 * @property string $send_mode
 * @property string $send_time
 * @property int $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property Institution $institution
 */
class InstitutionWhatsappConfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'institution_whatsapp_config';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['institutionid', 'waba_id', 'phone_number_id', 'access_token'], 'required'],
            [['institutionid', 'is_active', 'updated_by'], 'integer'],
            [['token_expires_at', 'created_at', 'updated_at'], 'safe'],
            [['access_token'], 'string'],
            [['send_mode'], 'in', 'range' => ['auto', 'manual']],
            [['send_time'], 'safe'],
            [['waba_id', 'phone_number_id'], 'string', 'max' => 64],
            [['display_phone'], 'string', 'max' => 20],
            [['api_version'], 'string', 'max' => 10],
            [['institutionid'], 'unique'],
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
            'waba_id' => 'WhatsApp Business Account ID',
            'phone_number_id' => 'Phone Number ID',
            'display_phone' => 'Display Phone',
            'access_token' => 'Access Token',
            'token_expires_at' => 'Token Expires At',
            'api_version' => 'API Version',
            'send_mode' => 'Send Mode',
            'send_time' => 'Send Time',
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
