<?php

namespace common\helpers;

use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedUserMember;

/**
 * UserHelper provides utility methods for user-related operations
 */
class UserHelper
{
    /**
     * Get display name for a user by user ID
     * 
     * This method resolves the user's display name by checking multiple sources:
     * 1. If user is a member/spouse - uses member name fields
     * 2. If user has a profile - uses userprofile name fields
     * 3. Falls back to emailid
     * 
     * @param int|null $userId The user ID from usercredentials table
     * @return string The user's display name or 'N/A' if user not found
     */
    public static function getUserDisplayName($userId)
    {
        if (!$userId) {
            return 'N/A';
        }
        
        $user = ExtendedUserCredentials::findOne($userId);
        if (!$user) {
            return 'N/A';
        }
        
        $userName = $user->emailid; // default to email
        
        // Try to get the member's name through UserMember
        $usermember = ExtendedUserMember::find()
            ->where(['userid' => $user->id])
            ->one();
            
        if ($usermember && $usermember->member) {
            $member = $usermember->member;
            // Check if user is spouse or member
            if ($usermember->usertype === 'S') {
                // Spouse user - use spouse name fields
                $userName = trim(implode(' ', array_filter([
                    $member->spouse_firstName,
                    $member->spouse_middleName,
                    $member->spouse_lastName
                ])));
            } else {
                // Member user - use primary member name fields
                $userName = trim(implode(' ', array_filter([
                    $member->firstName,
                    $member->middleName,
                    $member->lastName
                ])));
            }
        } elseif ($user->userprofile) {
            // If not a member, try userprofile
            $userName = trim(implode(' ', array_filter([
                $user->userprofile->firstname,
                $user->userprofile->middlename,
                $user->userprofile->lastname
            ])));
        }
        
        return $userName ?: $user->emailid; // Return emailid if name is empty
    }
}
