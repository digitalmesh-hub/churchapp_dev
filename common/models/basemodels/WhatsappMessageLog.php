<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "whatsapp_message_log".
 *
 * @property int $id
 * @property int $institutionid
 * @property int $memberid
 * @property string $recipient_type
 * @property string $event_type
 * @property string $event_date
 * @property string $phone
 * @property int $template_id
 * @property string $wamid
 * @property string $status
 * @property string $error_code
 * @property string $error_detail
 * @property string $sent_at
 * @property string $created_at
 *
 * @property Institution $institution
 * @property Member $member
 * @property WhatsappTemplate $template
 */
class WhatsappMessageLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'whatsapp_message_log';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['institutionid', 'memberid', 'event_type', 'event_date', 'phone'], 'required'],
            [['institutionid', 'memberid', 'template_id'], 'integer'],
            [['recipient_type'], 'in', 'range' => ['member', 'spouse']],
            [['event_type'], 'in', 'range' => ['birthday', 'anniversary']],
            [['status'], 'in', 'range' => ['queued', 'sent', 'failed']],
            [['event_date', 'sent_at', 'created_at'], 'safe'],
            [['phone'], 'string', 'max' => 20],
            [['wamid'], 'string', 'max' => 128],
            [['error_code'], 'string', 'max' => 20],
            [['error_detail'], 'string', 'max' => 512],
            [['institutionid', 'memberid', 'recipient_type', 'event_type', 'event_date'], 'unique', 'targetAttribute' => ['institutionid', 'memberid', 'recipient_type', 'event_type', 'event_date']],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => WhatsappTemplate::className(), 'targetAttribute' => ['template_id' => 'id']],
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
            'memberid' => 'Member ID',
            'recipient_type' => 'Recipient Type',
            'event_type' => 'Event Type',
            'event_date' => 'Event Date',
            'phone' => 'Phone',
            'template_id' => 'Template ID',
            'wamid' => 'WhatsApp Message ID',
            'status' => 'Status',
            'error_code' => 'Error Code',
            'error_detail' => 'Error Detail',
            'sent_at' => 'Sent At',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Member::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate(): \yii\db\ActiveQuery
    {
        return $this->hasOne(WhatsappTemplate::className(), ['id' => 'template_id']);
    }
}
