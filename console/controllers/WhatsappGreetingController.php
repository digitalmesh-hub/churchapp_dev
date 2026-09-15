<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\extendedmodels\ExtendedInstitutionWhatsappConfig;

/**
 * Thin cron/CLI wrapper around common\components\WhatsAppGreetingSender, which holds
 * the actual member/spouse birthday/anniversary sending logic (shared with the admin
 * web UI's "Send Now" button, so a web request never has to shell out to this CLI).
 *
 * `actionSendGreetings` is the cron entry point — intended to be invoked frequently
 * (e.g. hourly, matching the existing NotificationSchedulerController convention; there
 * is no crontab file checked into this repo, the OS-level schedule lives on the server).
 * It only processes institutions with send_mode='auto', gated by a per-institution local
 * send-time window, and relies on whatsapp_message_log's unique key + hasAlreadySent()
 * check to stay idempotent across repeated runs within that window.
 *
 * `actionSendNow` is a CLI convenience for manually triggering one institution
 * (e.g. for testing) — the admin UI button calls WhatsAppGreetingSender directly instead.
 */
class WhatsappGreetingController extends Controller
{
    /**
     * Cron entry point. Run frequently (e.g. hourly); the send-time window + dedup log
     * keep it idempotent.
     */
    public function actionSendGreetings(): void
    {
        $institutions = ExtendedInstitution::getAllInstitutions();
        if (empty($institutions)) {
            return;
        }

        foreach ($institutions as $institution) {
            $institutionId = $institution['id'];

            $config = ExtendedInstitutionWhatsappConfig::getActiveConfig($institutionId);
            if ($config === null || $config->send_mode !== 'auto') {
                continue;
            }

            $timeZone = trim($institution['timezone']);
            if (!empty($timeZone)) {
                date_default_timezone_set($timeZone);
            }

            $sendHour = (int) date('H', strtotime($config->send_time));
            $currentHour = (int) date('H');
            // 3-hour window from the configured send_time, matching the width of the
            // existing push-notification scheduler's fixed 7-10am window.
            if ($currentHour < $sendHour || $currentHour >= $sendHour + 3) {
                continue;
            }

            Yii::$app->whatsAppGreetingSender->processInstitution($config, date('Y-m-d'));
        }
    }

    /**
     * CLI convenience for manually triggering one institution: `php yii whatsapp-greeting/send-now 1`.
     *
     * @param int $institutionId
     * @return array ['sent' => int, 'failed' => int, 'skipped' => bool, 'message' => string|null]
     */
    public function actionSendNow(int $institutionId): array
    {
        $config = ExtendedInstitutionWhatsappConfig::getActiveConfig($institutionId);
        if ($config === null) {
            return ['sent' => 0, 'failed' => 0, 'skipped' => true, 'message' => 'WhatsApp is not configured or is disabled for this institution.'];
        }

        $institution = ExtendedInstitution::findOne($institutionId);
        if ($institution !== null && !empty($institution->timezone)) {
            date_default_timezone_set(trim($institution->timezone));
        }

        return Yii::$app->whatsAppGreetingSender->processInstitution($config, date('Y-m-d'));
    }
}
