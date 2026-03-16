<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\SundayService;

/**
 * This is the extended model class for table "sunday_service".
 *
 * @property int $id
 * @property string $service_date
 * @property string $content
 * @property int $institution_id
 * @property int $active
 * @property int $created_by
 * @property string $created_at
 * @property int $updated_by
 * @property string $updated_at
 */
class ExtendedSundayService extends SundayService
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sunday_service';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_date', 'content', 'institution_id'], 'required'],
            [['service_date', 'created_at', 'updated_at'], 'safe'],
            [['content'], 'string'],
            [['institution_id', 'active', 'created_by', 'updated_by'], 'integer'],
            [['active'], 'integer', 'max' => 4],
            [['active'], 'default', 'value' => 1],
            [['service_date'], 'validateFutureDate'],
        ];
    }

    /**
     * Validate that service_date is not in the past
     */
    public function validateFutureDate($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $today = date('Y-m-d');
            $serviceDate = date('Y-m-d', strtotime($this->$attribute));
            
            if ($serviceDate < $today) {
                $this->addError($attribute, 'Service date cannot be in the past.');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_date' => 'Service Date',
            'content' => 'Service Content',
            'institution_id' => 'Institution',
            'active' => 'Active',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Get all active Sunday services for an institution
     * @param int $institutionId
     * @return array
     */
    public function getActiveSundayServices($institutionId)
    {
        $sql = "SELECT * FROM sunday_service WHERE institution_id=:institutionId AND active=1 ORDER BY service_date DESC;";
        
        $result = Yii::$app->db->createCommand($sql)
            ->bindValue(':institutionId', $institutionId)
            ->queryAll();
        
        return $result;
    }

    /**
     * Get active Sunday services sorted by date
     * @param int $institutionId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function getActiveSundayServicesPaginated($institutionId, $limit = null, $offset = 0)
    {
        $query = static::find()
            ->where(['institution_id' => $institutionId, 'active' => 1])
            ->orderBy(['service_date' => SORT_DESC]);
        
        if ($limit !== null) {
            $query->limit($limit)->offset($offset);
        }
        
        return $query->all();
    }

    /**
     * Get count of active Sunday services
     * @param int $institutionId
     * @return int
     */
    public static function getActiveSundayServicesCount($institutionId)
    {
        return static::find()
            ->where(['institution_id' => $institutionId, 'active' => 1])
            ->count();
    }
}
