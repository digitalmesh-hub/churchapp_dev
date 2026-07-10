<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\MemberConnection;
use common\models\basemodels\Member;

/**
 * This is the extended model class for table "member_connection".
 *
 * @property int $id
 * @property int $member_id
 * @property int $connected_member_id
 * @property string $created_at
 * @property int $created_by
 *
 * @property Member $member
 * @property Member $connectedMember
 */
class ExtendedMemberConnection extends MemberConnection
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_connection';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'connected_member_id'], 'required'],
            [['member_id', 'connected_member_id', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['member_id', 'connected_member_id'], 'unique', 'targetAttribute' => ['member_id', 'connected_member_id']],
            [['member_id'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['member_id' => 'memberid']],
            [['connected_member_id'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['connected_member_id' => 'memberid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'connected_member_id' => 'Connected Member ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'member_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConnectedMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'connected_member_id']);
    }

    /**
     * Sync member connections
     * Deletes connections not in the provided list and inserts new ones
     * 
     * @param int $memberId The member ID
     * @param array $connectedMemberIds Array of connected member IDs
     * @return array Array of synced connections
     */
    public static function syncConnections($memberId, $connectedMemberIds)
    {
        // Check if member is trying to connect to themselves
        if (in_array($memberId, $connectedMemberIds)) {
            return [
                'success' => false,
                'error' => 'A member cannot connect to themselves'
            ];
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // Remove duplicates
            $connectedMemberIds = array_unique($connectedMemberIds);
            
            // Delete connections that are not in the provided list
            if (!empty($connectedMemberIds)) {
                self::deleteAll([
                    'and',
                    ['member_id' => $memberId],
                    ['not in', 'connected_member_id', $connectedMemberIds]
                ]);
            } else {
                // If the list is empty, delete all connections for this member
                self::deleteAll(['member_id' => $memberId]);
            }
            
            // Insert new connections that don't exist
            $insertedIds = [];
            foreach ($connectedMemberIds as $connectedMemberId) {
                // Check if connection already exists
                $exists = self::find()
                    ->where([
                        'member_id' => $memberId,
                        'connected_member_id' => $connectedMemberId
                    ])
                    ->exists();
                
                if (!$exists) {
                    $connection = new self();
                    $connection->member_id = $memberId;
                    $connection->connected_member_id = $connectedMemberId;
                    
                    if ($connection->save()) {
                        $insertedIds[] = $connectedMemberId;
                    }
                }
            }
            
            // Get all current connections for this member
            $currentConnections = self::find()
                ->where(['member_id' => $memberId])
                ->all();
            
            $transaction->commit();
            
            return [
                'success' => true,
                'connections' => $currentConnections,
                'inserted_count' => count($insertedIds)
            ];
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get all connections for a member
     * 
     * @param int $memberId The member ID
     * @return array Array of connected member IDs
     */
    public static function getMemberConnections($memberId)
    {
        return self::find()
            ->where(['member_id' => $memberId])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
    }

    /**
     * Get member connections with membership details
     * 
     * @param int $memberId The member ID
     * @return array Array of connections with member details
     */
    public static function getMemberConnectionsWithDetails($memberId)
    {
        $sql = "SELECT mc.connected_member_id as memberId, m.memberno as membershipNumber
                FROM member_connection mc
                INNER JOIN member m ON mc.connected_member_id = m.memberid
                WHERE mc.member_id = :memberId
                ORDER BY mc.created_at DESC";
        
        $result = Yii::$app->db->createCommand($sql)
            ->bindValue(':memberId', $memberId)
            ->queryAll();

        return $result;
    }

    /**
     * Get a member's connections with display details, for the admin UI grid.
     * Scoped to the given institution so connections into other churches never surface.
     *
     * @param int $memberId The member ID whose connections to list
     * @param int $institutionId The admin's institution ID
     * @return array Array of connections with member display details
     */
    public static function getConnectionsForAdmin($memberId, $institutionId)
    {
        $sql = "SELECT mc.id, mc.connected_member_id as memberId, m.memberno as membershipNumber,
                    TRIM(CONCAT_WS(' ', m.firstName, m.middleName, m.lastName)) as memberName,
                    m.member_mobile1 as mobile, mc.created_at
                FROM member_connection mc
                INNER JOIN member m ON mc.connected_member_id = m.memberid
                WHERE mc.member_id = :memberId AND m.institutionid = :institutionId
                ORDER BY mc.created_at DESC";

        return Yii::$app->db->createCommand($sql)
            ->bindValue(':memberId', $memberId)
            ->bindValue(':institutionId', $institutionId)
            ->queryAll();
    }

    /**
     * Add a single one-directional connection from the admin UI.
     * Both members must belong to the given institution; mirrors the mobile app's
     * one-directional storage (adding A->B does not also add B->A).
     *
     * @param int $memberId The owning member
     * @param int $connectedMemberId The member to connect to
     * @param int $institutionId The admin's institution ID
     * @param int|null $createdBy The admin userid creating the connection
     * @return array ['success' => bool, 'error' => string|null]
     */
    public static function addConnection($memberId, $connectedMemberId, $institutionId, $createdBy = null)
    {
        if ($memberId == $connectedMemberId) {
            return [
                'success' => false,
                'error' => 'A member cannot connect to themselves'
            ];
        }

        $validMemberCount = Member::find()
            ->where(['memberid' => [$memberId, $connectedMemberId], 'institutionid' => $institutionId])
            ->count();
        if ((int) $validMemberCount !== 2) {
            return [
                'success' => false,
                'error' => 'Both members must belong to the same institution'
            ];
        }

        $exists = self::find()
            ->where(['member_id' => $memberId, 'connected_member_id' => $connectedMemberId])
            ->exists();
        if ($exists) {
            return [
                'success' => false,
                'error' => 'Connection already exists'
            ];
        }

        $connection = new self();
        $connection->member_id = $memberId;
        $connection->connected_member_id = $connectedMemberId;
        $connection->created_by = $createdBy;

        if ($connection->save()) {
            return ['success' => true, 'error' => null];
        }

        return [
            'success' => false,
            'error' => implode(', ', $connection->getFirstErrors())
        ];
    }

    /**
     * Remove a single connection from the admin UI, scoped to the given institution
     * so an admin cannot delete a connection row belonging to another church's member.
     *
     * @param int $memberId The owning member
     * @param int $connectedMemberId The connected member to unlink
     * @param int $institutionId The admin's institution ID
     * @return array ['success' => bool, 'error' => string|null]
     */
    public static function removeConnection($memberId, $connectedMemberId, $institutionId)
    {
        $ownerBelongs = Member::find()
            ->where(['memberid' => $memberId, 'institutionid' => $institutionId])
            ->exists();
        if (!$ownerBelongs) {
            return ['success' => false, 'error' => 'Member not found'];
        }

        $deleted = self::deleteAll([
            'member_id' => $memberId,
            'connected_member_id' => $connectedMemberId
        ]);

        return [
            'success' => (bool) $deleted,
            'error' => $deleted ? null : 'Connection not found'
        ];
    }
}
