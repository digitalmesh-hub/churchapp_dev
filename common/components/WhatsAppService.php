<?php

namespace common\components;

use Yii;
use yii\base\Component;

/**
 * Thin client for the WhatsApp Cloud API (Meta Graph API). Sends pre-approved
 * template messages — free-form text is not allowed for business-initiated
 * messages outside Meta's 24-hour customer-service window, so every send here
 * (including admin "test message" sends) goes through a template.
 */
class WhatsAppService extends Component
{
    /**
     * Send a Meta-approved template message.
     *
     * @param string $phoneNumberId Meta Phone Number ID (the sender)
     * @param string $accessToken Decrypted WABA access token
     * @param string $apiVersion e.g. 'v20.0'
     * @param string $toNumber E.164 recipient number, digits only (no leading '+')
     * @param string $templateName Meta-approved template name
     * @param string $languageCode e.g. 'en'
     * @param array $variables Positional body variables, e.g. ['John', 'Grace Church']
     * @return array ['success' => bool, 'wamid' => string|null, 'errorCode' => string|null, 'errorDetail' => string|null]
     */
    public function sendTemplateMessage(string $phoneNumberId, string $accessToken, string $apiVersion, string $toNumber, string $templateName, string $languageCode, array $variables = []): array
    {
        $result = ['success' => false, 'wamid' => null, 'errorCode' => null, 'errorDetail' => null];

        try {
            $url = "https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}/messages";

            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $toNumber,
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => ['code' => $languageCode],
                ],
            ];

            if (!empty($variables)) {
                $payload['template']['components'] = [[
                    'type' => 'body',
                    'parameters' => array_map(function (mixed $value): array {
                        return ['type' => 'text', 'text' => (string) $value];
                    }, $variables),
                ]];
            }

            $ch = curl_init($url);
            if (!$ch) {
                Yii::error('curl init failed', __METHOD__);
                $result['errorDetail'] = 'curl init failed';
                return $result;
            }

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ]);
            if (!empty(Yii::$app->params['proxyEnabled'])) {
                curl_setopt($ch, CURLOPT_PROXY, Yii::$app->params['proxy']['host'] ?? '');
                curl_setopt($ch, CURLOPT_PROXYPORT, Yii::$app->params['proxy']['port'] ?? '');
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($response === false) {
                $result['errorDetail'] = curl_error($ch);
                Yii::error('WhatsApp send curl error: ' . $result['errorDetail'], __METHOD__);
                curl_close($ch);
                return $result;
            }
            curl_close($ch);

            Yii::info('WhatsApp send response (' . $httpCode . '): ' . $response, __METHOD__);
            $decoded = json_decode($response, true);

            if ($httpCode >= 200 && $httpCode < 300 && !empty($decoded['messages'][0]['id'])) {
                $result['success'] = true;
                $result['wamid'] = $decoded['messages'][0]['id'];
            } else {
                $result['errorCode'] = isset($decoded['error']['code']) ? (string) $decoded['error']['code'] : (string) $httpCode;
                $result['errorDetail'] = $decoded['error']['message'] ?? $response;
            }
        } catch (\Exception $e) {
            Yii::error('WhatsApp send exception: ' . $e->getMessage(), __METHOD__);
            $result['errorDetail'] = $e->getMessage();
        }

        return $result;
    }
}
