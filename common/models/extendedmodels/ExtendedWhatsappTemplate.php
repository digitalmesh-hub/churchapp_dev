<?php

namespace common\models\extendedmodels;

use common\models\basemodels\WhatsappTemplate;

/**
 * This is the extended model class for table "whatsapp_template".
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
 */
class ExtendedWhatsappTemplate extends WhatsappTemplate
{
    /**
     * Get the active template for an institution/event type, preferring the given
     * language and falling back to any other active language configured.
     *
     * @param int $institutionId
     * @param string $eventType 'birthday' or 'anniversary'
     * @param string $languageCode
     * @return ExtendedWhatsappTemplate|null
     */
    public static function getActiveTemplate(int $institutionId, string $eventType, string $languageCode = 'en'): ?ExtendedWhatsappTemplate
    {
        $template = self::find()
            ->where([
                'institutionid' => $institutionId,
                'event_type' => $eventType,
                'language_code' => $languageCode,
                'is_active' => 1,
            ])
            ->one();

        if ($template !== null) {
            return $template;
        }

        return self::find()
            ->where([
                'institutionid' => $institutionId,
                'event_type' => $eventType,
                'is_active' => 1,
            ])
            ->one();
    }

    /**
     * Get every template configured for an institution, keyed by event_type + language_code,
     * for the admin template-mapping screen.
     *
     * @param int $institutionId
     * @return ExtendedWhatsappTemplate[]
     */
    public static function getForInstitution(int $institutionId): array
    {
        return self::find()
            ->where(['institutionid' => $institutionId])
            ->orderBy(['event_type' => SORT_ASC, 'language_code' => SORT_ASC])
            ->all();
    }
}
