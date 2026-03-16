<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\VicarPositions;
use Exception;

/**
 * This is the extended model class for table "vicar_positions".
 *
 * @property int $id
 * @property string $position_name
 * @property string $position_description
 * @property int $is_main_vicar
 * @property int $display_order
 * @property int $institutionid
 * @property int $active
 * @property int $createdby
 * @property int $modifiedby
 * @property string $created_at
 * @property string $updated_at
 */
class ExtendedVicarPositions extends VicarPositions
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['position_name', 'institutionid', 'createdby'], 'required'],
            [['position_name'], 'trim'],
            [['is_main_vicar', 'display_order', 'institutionid', 'active', 'createdby', 'modifiedby'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['position_name'], 'string', 'max' => 100],
            [['position_description'], 'string', 'max' => 255],
            [['active'], 'default', 'value' => 1],
            [['is_main_vicar'], 'default', 'value' => 0],
            [['display_order'], 'default', 'value' => 0],
        ];
    }

    /**
     * Get all active positions for an institution
     * @param int $institutionId
     * @return array
     */
    public static function getActivePositions($institutionId)
    {
        try {
            $positions = self::find()
                ->where(['institutionid' => $institutionId, 'active' => 1])
                ->orderBy(['display_order' => SORT_ASC, 'position_name' => SORT_ASC])
                ->all();
            return $positions;
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return [];
        }
    }

    /**
     * Get main vicar position for an institution
     * @param int $institutionId
     * @return VicarPositions|null
     */
    public static function getMainVicarPosition($institutionId)
    {
        try {
            $position = self::find()
                ->where(['institutionid' => $institutionId, 'is_main_vicar' => 1, 'active' => 1])
                ->one();
            return $position;
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return null;
        }
    }

    /**
     * Check if position name already exists for institution
     * @param string $positionName
     * @param int $institutionId
     * @param int|null $excludeId
     * @return bool
     */
    public static function positionExists($positionName, $institutionId, $excludeId = null)
    {
        try {
            $query = self::find()
                ->where(['position_name' => $positionName, 'institutionid' => $institutionId]);
            
            if ($excludeId) {
                $query->andWhere(['!=', 'id', $excludeId]);
            }
            
            return $query->exists();
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return false;
        }
    }
}
