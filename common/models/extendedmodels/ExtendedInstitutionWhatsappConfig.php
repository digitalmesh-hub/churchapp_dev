<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\InstitutionWhatsappConfig;

/**
 * This is the extended model class for table "institution_whatsapp_config".
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
 */
class ExtendedInstitutionWhatsappConfig extends InstitutionWhatsappConfig
{
    /**
     * Get the active WhatsApp config for an institution, with the access token decrypted
     * in place on the returned instance. For sending only — never call save() on the
     * object this returns, or the decrypted token gets written back to the DB. Admin
     * UI code that edits/saves the config must load its own fresh instance instead.
     *
     * Returns null if no config exists or the feature is disabled for this institution.
     *
     * @param int $institutionId
     * @return ExtendedInstitutionWhatsappConfig|null
     */
    public static function getActiveConfig(int $institutionId): ?ExtendedInstitutionWhatsappConfig
    {
        $config = self::find()
            ->where(['institutionid' => $institutionId, 'is_active' => 1])
            ->one();

        if ($config === null) {
            return null;
        }

        $config->access_token = self::decryptToken($config->access_token);

        return $config;
    }

    /**
     * Encrypt an access token for storage. Uses Yii2's Security component with a
     * dedicated secret (WHATSAPP_TOKEN_ENCRYPTION_KEY) rather than the app's
     * cookieValidationKey, so rotating one doesn't affect the other.
     *
     * @param string $plainToken
     * @return string
     */
    public static function encryptToken(string $plainToken): string
    {
        return Yii::$app->security->encryptByKey($plainToken, env('WHATSAPP_TOKEN_ENCRYPTION_KEY'));
    }

    /**
     * Decrypt an access token read from storage.
     *
     * @param string|null $encryptedToken
     * @return string|null Null if decryption fails (wrong/missing key, corrupted data).
     */
    public static function decryptToken(?string $encryptedToken): ?string
    {
        if (empty($encryptedToken)) {
            return null;
        }

        try {
            return Yii::$app->security->decryptByKey($encryptedToken, env('WHATSAPP_TOKEN_ENCRYPTION_KEY'));
        } catch (\Exception $e) {
            Yii::error('Failed to decrypt WhatsApp access token: ' . $e->getMessage(), __METHOD__);
            return null;
        }
    }
}
