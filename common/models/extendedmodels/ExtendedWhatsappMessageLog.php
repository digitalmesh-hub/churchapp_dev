<?php

namespace common\models\extendedmodels;

use common\models\basemodels\WhatsappMessageLog;

/**
 * This is the extended model class for table "whatsapp_message_log".
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
 */
class ExtendedWhatsappMessageLog extends WhatsappMessageLog
{
    /**
     * Whether this exact recipient/event/date has already been successfully sent.
     * Only a 'sent' row blocks a resend — a prior 'failed' attempt can be retried,
     * by the cron's next run or an admin's manual resend.
     *
     * @param int $institutionId
     * @param int $memberId
     * @param string $recipientType 'member'|'spouse'
     * @param string $eventType 'birthday'|'anniversary'
     * @param string $eventDate Y-m-d
     * @return bool
     */
    public static function hasAlreadySent(int $institutionId, int $memberId, string $recipientType, string $eventType, string $eventDate): bool
    {
        return self::find()->where([
            'institutionid' => $institutionId,
            'memberid' => $memberId,
            'recipient_type' => $recipientType,
            'event_type' => $eventType,
            'event_date' => $eventDate,
            'status' => 'sent',
        ])->exists();
    }

    /**
     * Record the outcome of a send attempt. Upserts on the same unique key the table
     * enforces (institutionid, memberid, recipient_type, event_type, event_date), so a
     * retried attempt updates the existing row rather than violating the unique constraint.
     *
     * @param array $data institutionid, memberid, recipient_type, event_type,
     *                     event_date, phone, template_id, wamid, status, error_code, error_detail
     * @return ExtendedWhatsappMessageLog
     */
    public static function recordAttempt(array $data): ExtendedWhatsappMessageLog
    {
        $log = self::find()->where([
            'institutionid' => $data['institutionid'],
            'memberid' => $data['memberid'],
            'recipient_type' => $data['recipient_type'],
            'event_type' => $data['event_type'],
            'event_date' => $data['event_date'],
        ])->one();

        if ($log === null) {
            $log = new self();
        }

        $log->setAttributes($data, false);
        if ($log->status === 'sent') {
            $log->sent_at = date('Y-m-d H:i:s');
        }
        $log->save(false);

        return $log;
    }

    /**
     * Query for the admin message log screen, most recent first.
     *
     * @param int $institutionId
     * @param string|null $status Filter by status, or null for all.
     * @return \yii\db\ActiveQuery
     */
    public static function getForInstitution(int $institutionId, ?string $status = null): \yii\db\ActiveQuery
    {
        $query = self::find()
            ->where(['institutionid' => $institutionId])
            ->orderBy(['created_at' => SORT_DESC]);

        if (!empty($status)) {
            $query->andWhere(['status' => $status]);
        }

        return $query;
    }
}
