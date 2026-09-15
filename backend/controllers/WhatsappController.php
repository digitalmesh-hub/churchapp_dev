<?php

namespace backend\controllers;

use Yii;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use common\models\extendedmodels\ExtendedInstitution;
use common\models\extendedmodels\ExtendedInstitutionWhatsappConfig;
use common\models\extendedmodels\ExtendedWhatsappTemplate;
use common\models\extendedmodels\ExtendedWhatsappMessageLog;

/**
 * Admin UI for the WhatsApp birthday/anniversary greeting feature: per-institution
 * WABA settings + enable/disable, template mapping, message log, and a manual
 * "Send Now" trigger. All actions are scoped to the logged-in admin's own
 * institution — never trust an institution id from the request.
 */
class WhatsappController extends BaseController
{
    /**
     * View/save the institution's WhatsApp config (WABA id, phone number id,
     * access token, send mode/time, enable toggle).
     */
    public function actionSettings(): string|\yii\web\Response
    {
        $institutionId = $this->currentUser()->institutionid;

        $model = ExtendedInstitutionWhatsappConfig::find()->where(['institutionid' => $institutionId])->one();
        $isNew = $model === null;
        if ($isNew) {
            $model = new ExtendedInstitutionWhatsappConfig();
            $model->institutionid = $institutionId;
            $model->send_mode = 'manual';
            $model->send_time = '08:00:00';
        } else {
            // Never render the real token back into the form; blank means "leave unchanged".
            $model->access_token = '';
        }

        if ($model->load(Yii::$app->request->post())) {
            $postedToken = trim((string) $model->access_token);

            if ($postedToken === '') {
                if ($isNew) {
                    $model->addError('access_token', 'Access token is required.');
                } else {
                    $existing = ExtendedInstitutionWhatsappConfig::find()->where(['institutionid' => $institutionId])->one();
                    $model->access_token = $existing->access_token;
                }
            } else {
                $model->access_token = ExtendedInstitutionWhatsappConfig::encryptToken($postedToken);
            }

            $model->updated_by = $this->currentUserId();

            if (!$model->hasErrors() && $model->save()) {
                $this->sessionAddFlashArray('success', 'WhatsApp settings saved.', true);
                return $this->redirect(['whatsapp/settings']);
            }

            $this->sessionAddFlashArray('error', 'Failed to save WhatsApp settings. ' . implode(' ', $model->getFirstErrors()), true);
            $model->access_token = '';
        }

        return $this->render('settings', ['model' => $model, 'isNew' => $isNew]);
    }

    /**
     * AJAX: send a one-off test message to a given number, using the birthday
     * template (or whichever event type is passed) with placeholder sample values.
     */
    public function actionSendTest(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->request->isAjax) {
            return ['status' => 'error', 'message' => 'Invalid request'];
        }

        $institutionId = $this->currentUser()->institutionid;
        $testNumber = Yii::$app->request->post('testNumber');
        $eventType = Yii::$app->request->post('eventType', 'birthday');
        if (!in_array($eventType, ['birthday', 'anniversary'], true)) {
            $eventType = 'birthday';
        }

        $phone = preg_replace('/\D/', '', (string) $testNumber);
        if (empty($phone)) {
            return ['status' => 'error', 'message' => 'Enter a valid phone number, digits only, including country code.'];
        }

        $config = ExtendedInstitutionWhatsappConfig::getActiveConfig($institutionId);
        if ($config === null) {
            return ['status' => 'error', 'message' => 'Save and enable your WhatsApp settings first.'];
        }

        $template = ExtendedWhatsappTemplate::getActiveTemplate($institutionId, $eventType);
        if ($template === null) {
            return ['status' => 'error', 'message' => 'No active template configured for ' . $eventType . '.'];
        }

        $response = Yii::$app->whatsAppService->sendTemplateMessage(
            $config->phone_number_id,
            $config->access_token,
            $config->api_version,
            $phone,
            $template->meta_template_name,
            $template->language_code,
            ['Test Member', $this->currentUser()->institution->name]
        );

        if ($response['success']) {
            return ['status' => 'success', 'message' => 'Test message sent (id: ' . $response['wamid'] . ').'];
        }
        return ['status' => 'error', 'message' => $response['errorDetail'] ?: 'Failed to send test message.'];
    }

    /**
     * List + inline-manage the institution's event-type/language template mappings.
     */
    public function actionTemplates(): string
    {
        $institutionId = $this->currentUser()->institutionid;
        $templates = ExtendedWhatsappTemplate::getForInstitution($institutionId);

        return $this->render('templates', ['templates' => $templates]);
    }

    /**
     * Create/update a template mapping row.
     */
    public function actionSaveTemplate(): \yii\web\Response
    {
        $institutionId = $this->currentUser()->institutionid;
        $id = Yii::$app->request->post('id');

        $model = null;
        if (!empty($id)) {
            $model = ExtendedWhatsappTemplate::find()->where(['id' => $id, 'institutionid' => $institutionId])->one();
        }
        if ($model === null) {
            $model = new ExtendedWhatsappTemplate();
            $model->institutionid = $institutionId;
        }

        $model->event_type = Yii::$app->request->post('event_type');
        $model->meta_template_name = trim((string) Yii::$app->request->post('meta_template_name'));
        $model->language_code = trim((string) Yii::$app->request->post('language_code', 'en')) ?: 'en';
        $model->body_preview = Yii::$app->request->post('body_preview');
        $model->is_active = Yii::$app->request->post('is_active') ? 1 : 0;
        $model->updated_by = $this->currentUserId();

        // Variable map posted as parallel arrays: position[] / context_key[].
        $positions = Yii::$app->request->post('map_position', []);
        $contextKeys = Yii::$app->request->post('map_context_key', []);
        $variableMap = [];
        foreach ($positions as $index => $position) {
            $position = trim((string) $position);
            $key = trim((string) ($contextKeys[$index] ?? ''));
            if ($position !== '' && $key !== '') {
                $variableMap[$position] = $key;
            }
        }
        $model->variable_map = !empty($variableMap) ? json_encode($variableMap) : null;

        if ($model->save()) {
            $this->sessionAddFlashArray('success', 'Template saved.', true);
        } else {
            $this->sessionAddFlashArray('error', 'Failed to save template. ' . implode(' ', $model->getFirstErrors()), true);
        }

        return $this->redirect(['whatsapp/templates']);
    }

    /**
     * AJAX: delete a template mapping.
     */
    public function actionDeleteTemplate(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $institutionId = $this->currentUser()->institutionid;
        $id = Yii::$app->request->post('id');

        $model = ExtendedWhatsappTemplate::find()->where(['id' => $id, 'institutionid' => $institutionId])->one();
        if ($model === null) {
            return ['status' => 'error', 'message' => 'Template not found.'];
        }
        $model->delete();

        return ['status' => 'success'];
    }

    /**
     * Message log grid, optionally filtered by status.
     */
    public function actionMessageLog(): string
    {
        $institutionId = $this->currentUser()->institutionid;
        $status = Yii::$app->request->get('status');

        $dataProvider = new ActiveDataProvider([
            'query' => ExtendedWhatsappMessageLog::getForInstitution($institutionId, $status),
            'pagination' => ['pageSize' => 25],
        ]);

        return $this->render('message-log', ['dataProvider' => $dataProvider, 'status' => $status]);
    }

    /**
     * AJAX: resend a previously logged (typically failed) message.
     */
    public function actionResendMessage(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->request->isAjax) {
            return ['status' => 'error', 'message' => 'Invalid request'];
        }

        $institutionId = $this->currentUser()->institutionid;
        $id = Yii::$app->request->post('id');

        $log = ExtendedWhatsappMessageLog::find()->where(['id' => $id, 'institutionid' => $institutionId])->one();
        if ($log === null) {
            return ['status' => 'error', 'message' => 'Message not found.'];
        }
        if ($log->status === 'sent') {
            return ['status' => 'error', 'message' => 'This message was already sent successfully.'];
        }

        $result = Yii::$app->whatsAppGreetingSender->resendOne($log);

        return ['status' => $result['success'] ? 'success' : 'error', 'message' => $result['message']];
    }

    /**
     * AJAX: manually trigger today's greetings for this institution right now,
     * regardless of send_mode/send_time — the same underlying sender the cron uses.
     */
    public function actionSendNow(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->request->isAjax) {
            return ['status' => 'error', 'message' => 'Invalid request'];
        }

        $institutionId = $this->currentUser()->institutionid;

        $config = ExtendedInstitutionWhatsappConfig::getActiveConfig($institutionId);
        if ($config === null) {
            return ['status' => 'error', 'message' => 'Save and enable your WhatsApp settings first.'];
        }

        $institution = ExtendedInstitution::findOne($institutionId);
        if ($institution !== null && !empty($institution->timezone)) {
            date_default_timezone_set(trim($institution->timezone));
        }

        $summary = Yii::$app->whatsAppGreetingSender->processInstitution($config, date('Y-m-d'));

        return ['status' => 'success', 'summary' => $summary];
    }
}
