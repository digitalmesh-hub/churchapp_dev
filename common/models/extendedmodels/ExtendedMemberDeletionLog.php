<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\MemberDeletionLog;

/**
 * This is the extended model class for table "member_deletion_log".
 */
class ExtendedMemberDeletionLog extends MemberDeletionLog
{
    /**
     * Log member or spouse deletion
     * 
     * @param int $institutionId Institution ID
     * @param int $memberId Member ID
     * @param string $membershipNo Membership Number
     * @param string $name Full name of member/spouse
     * @param string $email Email address
     * @param string $relation 'member' or 'spouse'
     * @param string $reason Reason for deletion
     * @param int $deletedBy User ID who performed deletion
     * @param string $deletedByName Name of user who deleted
     * @return bool
     */
    public static function logDeletion($institutionId, $memberId, $membershipNo, $name, $email, $relation, $reason, $deletedBy, $deletedByName = null)
    {
        try {
            $log = new self();
            $log->institution_id = $institutionId;
            $log->member_id = $memberId;
            $log->membership_no = $membershipNo;
            $log->name = $name;
            $log->email = $email;
            $log->relation = $relation;
            $log->reason = trim($reason);
            $log->deleted_by = $deletedBy;
            $log->deleted_by_name = $deletedByName;
            $log->deleted_at = gmdate('Y-m-d H:i:s');
            
            return $log->save();
        } catch (\Exception $e) {
            Yii::error('Failed to log deletion: ' . $e->getMessage());
            return false;
        }
    }
}
