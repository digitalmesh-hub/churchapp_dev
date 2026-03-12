<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\VicarDirectory;
use Exception;

/**
 * This is the extended model class for table "vicar_directory".
 *
 * @property int $id
 * @property int $member_id
 * @property int $vicar_position_id
 * @property int $institution_id
 * @property string $start_date
 * @property string $end_date
 * @property int $is_active
 * @property int $display_order
 * @property string $remarks
 * @property int $createdby
 * @property int $modifiedby
 * @property string $created_at
 * @property string $updated_at
 */
class ExtendedVicarDirectory extends VicarDirectory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'vicar_position_id', 'institution_id', 'start_date', 'createdby'], 'required'],
            [['member_id', 'vicar_position_id', 'institution_id', 'is_active', 'display_order', 'createdby', 'modifiedby'], 'integer'],
            [['start_date', 'end_date', 'created_at', 'updated_at'], 'safe'],
            [['remarks'], 'string'],
            [['is_active'], 'default', 'value' => 1],
            [['display_order'], 'default', 'value' => 0],
        ];
    }

    /**
     * Get all vicars for an institution with member and position details
     * @param int $institutionId
     * @param bool $activeOnly
     * @param int|null $limit
     * @param int|null $offset
     * @param array $filters Additional filters (search, status, position)
     * @return array
     */
    public static function getVicarDirectoryWithDetails($institutionId, $activeOnly = true, $limit = null, $offset = null, $filters = [])
    {
        try {
            $sql = "
                SELECT 
                    vd.id,
                    vd.member_id,
                    vd.vicar_position_id,
                    vd.institution_id,
                    vd.start_date,
                    vd.end_date,
                    vd.is_active,
                    vd.display_order,
                    vd.remarks,
                    vp.position_name,
                    vp.position_description,
                    vp.is_main_vicar,
                    vp.display_order as position_display_order,
                    m.memberid,
                    m.memberno,
                    m.firstName,
                    m.middleName,
                    m.lastName,
                    m.member_mobile1,
                    m.member_email,
                    m.memberImageThumbnail,
                    m.member_pic as memberImage,
                    t.Description as memberTitle
                FROM vicar_directory vd
                JOIN vicar_positions vp ON vd.vicar_position_id = vp.id
                JOIN member m ON vd.member_id = m.memberid
                LEFT JOIN title t ON m.membertitle = t.TitleId
                WHERE vd.institution_id = :institutionId
                " . ($activeOnly ? " AND vd.is_active = 1 AND vp.active = 1" : "");
            
            // Apply filters
            if (!empty($filters['search'])) {
                $sql .= " AND (CONCAT(COALESCE(t.Description, ''), ' ', m.firstName, ' ', COALESCE(m.middleName, ''), ' ', m.lastName) LIKE :search 
                          OR vp.position_name LIKE :search 
                          OR m.memberno LIKE :search)";
            }
            
            if (!empty($filters['status'])) {
                if ($filters['status'] === 'active') {
                    $sql .= " AND vd.is_active = 1";
                } elseif ($filters['status'] === 'inactive') {
                    $sql .= " AND vd.is_active = 0";
                }
            }
            
            if (!empty($filters['position'])) {
                $sql .= " AND vp.position_name = :position";
            }
            
            $sql .= " ORDER BY vd.is_active DESC, vp.display_order ASC, vd.display_order ASC
            ";
            
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
            }
            if ($offset !== null) {
                $sql .= " OFFSET :offset";
            }
            
            $command = Yii::$app->db->createCommand($sql)
                ->bindValue(':institutionId', $institutionId);
            
            // Bind filter values
            if (!empty($filters['search'])) {
                $command->bindValue(':search', '%' . $filters['search'] . '%');
            }
            if (!empty($filters['position'])) {
                $command->bindValue(':position', $filters['position']);
            }
            
            if ($limit !== null) {
                $command->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
            }
            if ($offset !== null) {
                $command->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
            }
            
            return $command->queryAll();
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return [];
        }
    }
    
    /**
     * Get total count of vicars for an institution
     * @param int $institutionId
     * @param bool $activeOnly
     * @param array $filters Additional filters (search, status, position)
     * @return int
     */
    public static function getVicarDirectoryCount($institutionId, $activeOnly = true, $filters = [])
    {
        try {
            $sql = "
                SELECT COUNT(*)
                FROM vicar_directory vd
                JOIN vicar_positions vp ON vd.vicar_position_id = vp.id
                JOIN member m ON vd.member_id = m.memberid
                LEFT JOIN title t ON m.membertitle = t.TitleId
                WHERE vd.institution_id = :institutionId
                " . ($activeOnly ? " AND vd.is_active = 1 AND vp.active = 1" : "");
            
            // Apply filters
            if (!empty($filters['search'])) {
                $sql .= " AND (CONCAT(COALESCE(t.Description, ''), ' ', m.firstName, ' ', COALESCE(m.middleName, ''), ' ', m.lastName) LIKE :search 
                          OR vp.position_name LIKE :search 
                          OR m.memberno LIKE :search)";
            }
            
            if (!empty($filters['status'])) {
                if ($filters['status'] === 'active') {
                    $sql .= " AND vd.is_active = 1";
                } elseif ($filters['status'] === 'inactive') {
                    $sql .= " AND vd.is_active = 0";
                }
            }
            
            if (!empty($filters['position'])) {
                $sql .= " AND vp.position_name = :position";
            }
            
            $command = Yii::$app->db->createCommand($sql)
                ->bindValue(':institutionId', $institutionId);
            
            // Bind filter values
            if (!empty($filters['search'])) {
                $command->bindValue(':search', '%' . $filters['search'] . '%');
            }
            if (!empty($filters['position'])) {
                $command->bindValue(':position', $filters['position']);
            }
            
            $query = $command->queryScalar();

            return (int)$query;
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return 0;
        }
    }

    /**
     * Get main vicar for an institution
     * @param int $institutionId
     * @return array|null
     */
    public static function getMainVicar($institutionId)
    {
        try {
            $query = Yii::$app->db->createCommand("
                SELECT 
                    vd.id,
                    vd.member_id,
                    vd.vicar_position_id,
                    vd.start_date,
                    vd.end_date,
                    vd.remarks,
                    vp.position_name,
                    vp.position_description,
                    m.memberid,
                    m.memberno,
                    m.firstName,
                    m.middleName,
                    m.lastName,
                    m.member_mobile1,
                    m.member_email,
                    m.memberImageThumbnail,
                    m.member_pic as memberImage,
                    t.Description as memberTitle
                FROM vicar_directory vd
                JOIN vicar_positions vp ON vd.vicar_position_id = vp.id
                JOIN member m ON vd.member_id = m.memberid
                LEFT JOIN title t ON m.membertitle = t.TitleId
                WHERE vd.institution_id = :institutionId
                AND vp.is_main_vicar = 1
                AND vd.is_active = 1
                AND vp.active = 1
                LIMIT 1
            ")->bindValue(':institutionId', $institutionId)->queryOne();

            return $query ?: null;
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return null;
        }
    }

    /**
     * Get assistant vicars for an institution
     * @param int $institutionId
     * @return array
     */
    public static function getAssistantVicars($institutionId)
    {
        try {
            $query = Yii::$app->db->createCommand("
                SELECT 
                    vd.id,
                    vd.member_id,
                    vd.vicar_position_id,
                    vd.start_date,
                    vd.end_date,
                    vd.display_order,
                    vd.remarks,
                    vp.position_name,
                    vp.position_description,
                    vp.display_order as position_display_order,
                    m.memberid,
                    m.memberno,
                    m.firstName,
                    m.middleName,
                    m.lastName,
                    m.member_mobile1,
                    m.member_email,
                    m.memberImageThumbnail,
                    m.member_pic as memberImage,
                    t.Description as memberTitle
                FROM vicar_directory vd
                JOIN vicar_positions vp ON vd.vicar_position_id = vp.id
                JOIN member m ON vd.member_id = m.memberid
                LEFT JOIN title t ON m.membertitle = t.TitleId
                WHERE vd.institution_id = :institutionId
                AND vp.is_main_vicar = 0
                AND vd.is_active = 1
                AND vp.active = 1
                ORDER BY vp.display_order ASC, vd.display_order ASC
            ")->bindValue(':institutionId', $institutionId)->queryAll();

            return $query;
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return [];
        }
    }

    /**
     * Deactivate existing vicar assignments for a position
     * @param int $positionId
     * @param int $institutionId
     * @return bool
     */
    public static function deactivateExistingVicars($positionId, $institutionId)
    {
        try {
            Yii::$app->db->createCommand("
                UPDATE vicar_directory 
                SET is_active = 0, end_date = :endDate
                WHERE vicar_position_id = :positionId 
                AND institution_id = :institutionId 
                AND is_active = 1
            ")->bindValues([
                ':endDate' => date('Y-m-d'),
                ':positionId' => $positionId,
                ':institutionId' => $institutionId
            ])->execute();

            return true;
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return false;
        }
    }
}
