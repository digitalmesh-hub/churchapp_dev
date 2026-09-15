<?php

namespace common\components;

use Yii;
use yii\base\Component;
use common\models\extendedmodels\ExtendedInstitutionWhatsappConfig;
use common\models\extendedmodels\ExtendedWhatsappTemplate;
use common\models\extendedmodels\ExtendedWhatsappMessageLog;

/**
 * Core WhatsApp birthday/anniversary greeting engine — member and spouse only,
 * dependants are explicitly out of scope. Shared by console/controllers/
 * WhatsappGreetingController (cron + CLI manual trigger) and the admin web
 * "Send Now" button, so the sending logic lives in exactly one place regardless
 * of which path triggers it.
 */
class WhatsAppGreetingSender extends Component
{
    /**
     * @param ExtendedInstitutionWhatsappConfig $config
     * @param string $date Y-m-d, evaluated in the institution's local timezone by the caller
     * @return array ['sent' => int, 'failed' => int, 'skipped' => bool, 'message' => string|null]
     */
    public function processInstitution(ExtendedInstitutionWhatsappConfig $config, string $date): array
    {
        $summary = ['sent' => 0, 'failed' => 0, 'skipped' => false, 'message' => null];

        $this->sendMemberBirthdays($config, $date, $summary);
        $this->sendSpouseBirthdays($config, $date, $summary);
        $this->sendAnniversaries($config, $date, $summary);

        return $summary;
    }

    /**
     * Resend a single previously-logged (typically failed) message, from the admin
     * message log screen. Rebuilds the template variables from the member's *current*
     * data rather than trying to persist/replay the original ones — a resend should
     * reflect the latest name etc., not stale data from whenever it first failed.
     *
     * @param ExtendedWhatsappMessageLog $log
     * @return array ['success' => bool, 'message' => string]
     */
    public function resendOne(ExtendedWhatsappMessageLog $log): array
    {
        $config = ExtendedInstitutionWhatsappConfig::getActiveConfig($log->institutionid);
        if ($config === null) {
            return ['success' => false, 'message' => 'WhatsApp is not configured or is disabled for this institution.'];
        }

        $template = $log->template_id
            ? ExtendedWhatsappTemplate::findOne($log->template_id)
            : ExtendedWhatsappTemplate::getActiveTemplate($log->institutionid, $log->event_type);
        if ($template === null) {
            return ['success' => false, 'message' => 'No template available for this event type.'];
        }

        $row = Yii::$app->db->createCommand(
            "SELECT m.firstName, m.middleName, m.lastName,
                    m.spouse_firstName, m.spouse_middleName, m.spouse_lastName,
                    i.name as institution_name
             FROM member m
             INNER JOIN institution i ON i.id = m.institutionid
             WHERE m.memberid = :memberId"
        )->bindValue(':memberId', $log->memberid)->queryOne();

        if ($row === false) {
            return ['success' => false, 'message' => 'Member no longer exists.'];
        }

        if ($log->recipient_type === 'spouse') {
            $context = [
                'first_name' => $row['spouse_firstName'],
                'full_name' => trim($row['spouse_firstName'] . ' ' . $row['spouse_middleName'] . ' ' . $row['spouse_lastName']),
                'church_name' => $row['institution_name'],
            ];
        } else {
            $context = [
                'first_name' => $row['firstName'],
                'full_name' => trim($row['firstName'] . ' ' . $row['middleName'] . ' ' . $row['lastName']),
                'church_name' => $row['institution_name'],
            ];
            if ($log->event_type === 'anniversary') {
                $context['spouse_name'] = trim($row['spouse_firstName'] . ' ' . $row['spouse_middleName'] . ' ' . $row['spouse_lastName']);
            }
        }

        $variables = $this->buildVariables($template, $context);

        $response = Yii::$app->whatsAppService->sendTemplateMessage(
            $config->phone_number_id,
            $config->access_token,
            $config->api_version,
            $log->phone,
            $template->meta_template_name,
            $template->language_code,
            $variables
        );

        $log->template_id = $template->id;
        $log->wamid = $response['wamid'];
        $log->status = $response['success'] ? 'sent' : 'failed';
        $log->error_code = $response['errorCode'];
        $log->error_detail = $response['errorDetail'];
        if ($response['success']) {
            $log->sent_at = date('Y-m-d H:i:s');
        }
        $log->save(false);

        return ['success' => $response['success'], 'message' => $response['success'] ? 'Resent.' : ($response['errorDetail'] ?: 'Failed to resend.')];
    }

    /**
     * Selects opted-in, active members whose birthday falls on $date and dispatches
     * a birthday greeting to each via the member's own mobile number.
     *
     * @param ExtendedInstitutionWhatsappConfig $config
     * @param string $date Y-m-d
     * @param array $summary Reference, incremented in place.
     * @return void
     */
    protected function sendMemberBirthdays(ExtendedInstitutionWhatsappConfig $config, string $date, array &$summary): void
    {
        $template = ExtendedWhatsappTemplate::getActiveTemplate($config->institutionid, 'birthday');
        if ($template === null) {
            return;
        }

        $rows = Yii::$app->db->createCommand(
            "SELECT m.memberid, m.institutionid, m.firstName, m.middleName, m.lastName,
                    m.member_mobile1 as mobile, m.member_mobile1_countrycode as mobile_countrycode,
                    i.name as institution_name
             FROM member m
             INNER JOIN settings s ON s.memberid = m.memberid
             INNER JOIN institution i ON i.id = m.institutionid
             WHERE m.institutionid = :institutionId
               AND m.membertype = 0 AND m.active = 1
               AND s.birthday = 1
               AND m.member_dob IS NOT NULL
               AND MONTH(m.member_dob) = MONTH(:eventDate) AND DAY(m.member_dob) = DAY(:eventDate)
               AND m.member_mobile1 IS NOT NULL AND m.member_mobile1 != ''"
        )->bindValue(':institutionId', $config->institutionid)
         ->bindValue(':eventDate', $date)
         ->queryAll();

        foreach ($rows as $row) {
            $this->dispatchGreeting($config, $template, 'birthday', 'member', $row['memberid'], $date, [
                'phone' => $row['mobile'],
                'countrycode' => $row['mobile_countrycode'],
                'context' => [
                    'first_name' => $row['firstName'],
                    'full_name' => trim($row['firstName'] . ' ' . $row['middleName'] . ' ' . $row['lastName']),
                    'church_name' => $row['institution_name'],
                ],
            ], $summary);
        }
    }

    /**
     * Selects opted-in members whose spouse's birthday falls on $date and dispatches
     * a birthday greeting to each via the spouse's own mobile number.
     *
     * @param ExtendedInstitutionWhatsappConfig $config
     * @param string $date Y-m-d
     * @param array $summary Reference, incremented in place.
     * @return void
     */
    protected function sendSpouseBirthdays(ExtendedInstitutionWhatsappConfig $config, string $date, array &$summary): void
    {
        $template = ExtendedWhatsappTemplate::getActiveTemplate($config->institutionid, 'birthday');
        if ($template === null) {
            return;
        }

        $rows = Yii::$app->db->createCommand(
            "SELECT m.memberid, m.institutionid, m.spouse_firstName, m.spouse_middleName, m.spouse_lastName,
                    m.spouse_mobile1 as mobile, m.spouse_mobile1_countrycode as mobile_countrycode,
                    i.name as institution_name
             FROM member m
             INNER JOIN settings s ON s.memberid = m.memberid
             INNER JOIN institution i ON i.id = m.institutionid
             WHERE m.institutionid = :institutionId
               AND m.membertype = 0 AND m.active = 1
               AND s.spousebirthday = 1
               AND m.spouse_dob IS NOT NULL AND m.spouse_firstName IS NOT NULL AND m.spouse_firstName != ''
               AND MONTH(m.spouse_dob) = MONTH(:eventDate) AND DAY(m.spouse_dob) = DAY(:eventDate)
               AND m.spouse_mobile1 IS NOT NULL AND m.spouse_mobile1 != ''"
        )->bindValue(':institutionId', $config->institutionid)
         ->bindValue(':eventDate', $date)
         ->queryAll();

        foreach ($rows as $row) {
            $this->dispatchGreeting($config, $template, 'birthday', 'spouse', $row['memberid'], $date, [
                'phone' => $row['mobile'],
                'countrycode' => $row['mobile_countrycode'],
                'context' => [
                    'first_name' => $row['spouse_firstName'],
                    'full_name' => trim($row['spouse_firstName'] . ' ' . $row['spouse_middleName'] . ' ' . $row['spouse_lastName']),
                    'church_name' => $row['institution_name'],
                ],
            ], $summary);
        }
    }

    /**
     * Selects opted-in, active members whose wedding anniversary falls on $date and
     * dispatches a single combined greeting to the member's own mobile number
     * (mirrors the existing push-notification behaviour of one notification per
     * couple rather than one per spouse).
     *
     * @param ExtendedInstitutionWhatsappConfig $config
     * @param string $date Y-m-d
     * @param array $summary Reference, incremented in place.
     * @return void
     */
    protected function sendAnniversaries(ExtendedInstitutionWhatsappConfig $config, string $date, array &$summary): void
    {
        $template = ExtendedWhatsappTemplate::getActiveTemplate($config->institutionid, 'anniversary');
        if ($template === null) {
            return;
        }

        // One message to the member's own number for the couple, matching the existing
        // push-notification anniversary behaviour (a single combined notification, not
        // two separate ones) - gated on the member's own anniversary opt-in flag.
        $rows = Yii::$app->db->createCommand(
            "SELECT m.memberid, m.institutionid, m.firstName, m.middleName, m.lastName,
                    m.spouse_firstName, m.spouse_middleName, m.spouse_lastName,
                    m.member_mobile1 as mobile, m.member_mobile1_countrycode as mobile_countrycode,
                    i.name as institution_name
             FROM member m
             INNER JOIN settings s ON s.memberid = m.memberid
             INNER JOIN institution i ON i.id = m.institutionid
             WHERE m.institutionid = :institutionId
               AND m.membertype = 0 AND m.active = 1
               AND s.anniversary = 1
               AND m.dom IS NOT NULL AND m.spouse_firstName IS NOT NULL AND m.spouse_firstName != ''
               AND MONTH(m.dom) = MONTH(:eventDate) AND DAY(m.dom) = DAY(:eventDate)
               AND m.member_mobile1 IS NOT NULL AND m.member_mobile1 != ''"
        )->bindValue(':institutionId', $config->institutionid)
         ->bindValue(':eventDate', $date)
         ->queryAll();

        foreach ($rows as $row) {
            $this->dispatchGreeting($config, $template, 'anniversary', 'member', $row['memberid'], $date, [
                'phone' => $row['mobile'],
                'countrycode' => $row['mobile_countrycode'],
                'context' => [
                    'first_name' => $row['firstName'],
                    'full_name' => trim($row['firstName'] . ' ' . $row['middleName'] . ' ' . $row['lastName']),
                    'spouse_name' => trim($row['spouse_firstName'] . ' ' . $row['spouse_middleName'] . ' ' . $row['spouse_lastName']),
                    'church_name' => $row['institution_name'],
                ],
            ], $summary);
        }
    }

    /**
     * Shared dedup-check + send + log flow for a single recipient.
     *
     * @param ExtendedInstitutionWhatsappConfig $config
     * @param ExtendedWhatsappTemplate $template
     * @param string $eventType 'birthday'|'anniversary'
     * @param string $recipientType 'member'|'spouse'
     * @param int $memberId
     * @param string $eventDate Y-m-d
     * @param array $recipient ['phone' => string, 'countrycode' => string, 'context' => array]
     * @param array $summary Reference, incremented in place.
     * @return void
     */
    protected function dispatchGreeting(ExtendedInstitutionWhatsappConfig $config, ExtendedWhatsappTemplate $template, string $eventType, string $recipientType, int $memberId, string $eventDate, array $recipient, array &$summary): void
    {
        if (ExtendedWhatsappMessageLog::hasAlreadySent($config->institutionid, $memberId, $recipientType, $eventType, $eventDate)) {
            return;
        }

        $phone = $this->formatE164($recipient['countrycode'], $recipient['phone']);
        if (empty($phone)) {
            return;
        }

        $variables = $this->buildVariables($template, $recipient['context']);

        $response = Yii::$app->whatsAppService->sendTemplateMessage(
            $config->phone_number_id,
            $config->access_token,
            $config->api_version,
            $phone,
            $template->meta_template_name,
            $template->language_code,
            $variables
        );

        $logData = [
            'institutionid' => $config->institutionid,
            'memberid' => $memberId,
            'recipient_type' => $recipientType,
            'event_type' => $eventType,
            'event_date' => $eventDate,
            'phone' => $phone,
            'template_id' => $template->id,
            'wamid' => $response['wamid'],
            'status' => $response['success'] ? 'sent' : 'failed',
            'error_code' => $response['errorCode'],
            'error_detail' => $response['errorDetail'],
        ];
        ExtendedWhatsappMessageLog::recordAttempt($logData);

        if ($response['success']) {
            $summary['sent']++;
        } else {
            $summary['failed']++;
            Yii::error("WhatsApp send failed for member {$memberId} ({$recipientType}/{$eventType}): " . $response['errorDetail'], __METHOD__);
        }
    }

    /**
     * @param string|null $countryCode
     * @param string $mobile
     * @return string Digits-only E.164 number (no leading '+'), or '' if the mobile is empty.
     */
    protected function formatE164(?string $countryCode, string $mobile): string
    {
        $mobile = preg_replace('/\D/', '', (string) $mobile);
        if (empty($mobile)) {
            return '';
        }
        $countryCode = preg_replace('/\D/', '', (string) $countryCode);
        return $countryCode . $mobile;
    }

    /**
     * @param ExtendedWhatsappTemplate $template
     * @param array $context Named values available for this recipient (first_name, full_name, church_name, ...).
     * @return array Positional variables in {{1}}, {{2}}, ... order.
     */
    protected function buildVariables(ExtendedWhatsappTemplate $template, array $context): array
    {
        $map = !empty($template->variable_map) ? json_decode($template->variable_map, true) : null;
        if (empty($map) || !is_array($map)) {
            // Sensible default when the admin hasn't configured a variable_map: {{1}} = name, {{2}} = church.
            $map = ['1' => 'first_name', '2' => 'church_name'];
        }
        ksort($map, SORT_NUMERIC);

        $variables = [];
        foreach ($map as $contextKey) {
            $variables[] = $context[$contextKey] ?? '';
        }
        return $variables;
    }
}
