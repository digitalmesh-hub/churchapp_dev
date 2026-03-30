<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedUserMember;
use common\models\extendedmodels\ExtendedMember;

/**
 * Phone Cleanup controller - Removes country codes from user credentials mobile numbers
 * 
 * Usage:
 * php yii phone-cleanup/fix-mobile-numbers --institution=1 --dry-run
 * php yii phone-cleanup/fix-mobile-numbers --institution=1
 */
class PhoneCleanupController extends Controller
{
    public $institutionId = 1;
    public $dryRun = false;
    
    // Statistics
    private $stats = [
        'total_credentials' => 0,
        'member_credentials_updated' => 0,
        'spouse_credentials_updated' => 0,
        'skipped_no_country_code' => 0,
        'skipped_no_match' => 0,
        'skipped_already_clean' => 0,
        'errors' => 0
    ];
    
    private $updateLog = [];
    private $errorLog = [];
    
    public function options($actionID)
    {
        return ['institutionId', 'dryRun'];
    }
    
    public function optionAliases()
    {
        return [
            'i' => 'institutionId',
            'd' => 'dryRun'
        ];
    }
    
    /**
     * Fix mobile numbers by removing country codes from user credentials
     * 
     * @return int Exit code
     */
    public function actionFixMobileNumbers()
    {
        $this->stdout("\n");
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("Phone Number Cleanup - Remove Country Codes from User Credentials\n", Console::FG_CYAN, Console::BOLD);
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("\n");
        
        // Validate institution
        if (empty($this->institutionId)) {
            $this->stderr("Error: Institution ID is required. Use --institution=ID\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }
        
        $this->stdout("Institution ID: ", Console::FG_YELLOW);
        $this->stdout("{$this->institutionId}\n");
        $this->stdout("Mode: ", Console::FG_YELLOW);
        if ($this->dryRun) {
            $this->stdout("DRY RUN (Preview Only - No Database Changes)\n", Console::FG_CYAN, Console::BOLD);
        } else {
            $this->stdout("LIVE (Will Update Database)\n", Console::FG_GREEN, Console::BOLD);
        }
        $this->stdout("\n");
        
        // Confirm before proceeding
        if (!$this->confirm("Do you want to proceed with the phone cleanup?")) {
            $this->stdout("Cleanup cancelled.\n", Console::FG_YELLOW);
            return ExitCode::OK;
        }
        
        $this->stdout("\n");
        
        // Initialize logs
        $this->updateLog[] = ['UserCredentialID', 'UserType', 'MemberID', 'MemberName', 'OldMobile', 'NewMobile', 'Status'];
        $this->errorLog[] = ['UserCredentialID', 'UserType', 'MemberID', 'Issue', 'Details'];
        
        // Get all user credentials for this institution
        $userCredentials = ExtendedUserCredentials::find()
            ->where(['institutionid' => $this->institutionId])
            ->andWhere(['IS NOT', 'mobileno', null])
            ->andWhere(['<>', 'mobileno', ''])
            ->all();
        
        $this->stats['total_credentials'] = count($userCredentials);
        
        $this->stdout("Found " . Console::ansiFormat($this->stats['total_credentials'], [Console::FG_GREEN, Console::BOLD]) . " user credentials with mobile numbers\n");
        $this->stdout("\n");
        
        // Process each user credential
        $processedCount = 0;
        foreach ($userCredentials as $userCredential) {
            $processedCount++;
            
            $this->stdout("[{$processedCount}/{$this->stats['total_credentials']}] ");
            $this->stdout("Processing UserCredential ID: ", Console::FG_CYAN);
            $this->stdout("{$userCredential->id} ");
            $this->stdout("(Mobile: {$userCredential->mobileno})\n");
            
            $result = $this->processUserCredential($userCredential);
            
            if ($result['status'] === 'updated') {
                $this->stdout("  ✓ ", Console::FG_GREEN, Console::BOLD);
                $this->stdout("Updated: {$result['old']} → {$result['new']}\n\n");
            } elseif ($result['status'] === 'skipped') {
                $this->stdout("  ⊘ ", Console::FG_GREY);
                $this->stdout("SKIPPED: {$result['reason']}\n\n");
            } elseif ($result['status'] === 'error') {
                $this->stdout("  ✗ ", Console::FG_RED, Console::BOLD);
                $this->stdout("ERROR: {$result['error']}\n\n", Console::FG_RED);
            }
        }
        
        // Save logs
        $this->saveUpdateLog();
        $this->saveErrorLog();
        
        // Print statistics
        $this->printStatistics();
        
        return ExitCode::OK;
    }
    
    /**
     * Process a single user credential
     */
    private function processUserCredential($userCredential)
    {
        try {
            $mobileNo = $userCredential->mobileno;
            
            // Check if mobile number has a country code (starts with +)
            if (!$this->hasCountryCode($mobileNo)) {
                $this->stats['skipped_already_clean']++;
                return [
                    'status' => 'skipped',
                    'reason' => 'No country code found (already clean)'
                ];
            }
            
            // Find linked member through user_member table
            $userMembers = ExtendedUserMember::find()
                ->where(['userid' => $userCredential->id, 'institutionid' => $this->institutionId])
                ->all();
            
            if (empty($userMembers)) {
                $this->stats['skipped_no_match']++;
                $this->errorLog[] = [
                    $userCredential->id,
                    $userCredential->usertype ?? 'N/A',
                    'N/A',
                    'No User-Member Link',
                    'User credential not linked to any member'
                ];
                return [
                    'status' => 'skipped',
                    'reason' => 'No linked member found'
                ];
            }
            
            // Process each user member link
            $updated = false;
            foreach ($userMembers as $userMember) {
                $member = ExtendedMember::findOne($userMember->memberid);
                
                if (!$member) {
                    continue;
                }
                
                // Determine user type and get corresponding phone from member table
                $userType = $userMember->usertype;
                $countryCode = '';
                $phoneNumber = '';
                
                if ($userType === 'M') {
                    // Member phone
                    $countryCode = $member->member_mobile1_countrycode ?? '';
                    $phoneNumber = $member->member_mobile1 ?? '';
                } elseif ($userType === 'S') {
                    // Spouse phone
                    $countryCode = $member->spouse_mobile1_countrycode ?? '';
                    $phoneNumber = $member->spouse_mobile1 ?? '';
                }
                
                // Skip if no phone data in member table
                if (empty($phoneNumber)) {
                    continue;
                }
                
                // Combine country code and phone number from member table
                $expectedMobile = $this->normalizePhone($countryCode . $phoneNumber);
                $currentMobile = $this->normalizePhone($mobileNo);
                
                // Verify they match
                if ($expectedMobile !== $currentMobile) {
                    $this->stats['skipped_no_match']++;
                    $this->errorLog[] = [
                        $userCredential->id,
                        $userType,
                        $member->memberid,
                        'Phone Mismatch',
                        "User Credential: {$mobileNo}, Member Table: {$countryCode}{$phoneNumber}"
                    ];
                    return [
                        'status' => 'skipped',
                        'reason' => "Phone mismatch with member table"
                    ];
                }
                
                // Use the clean phone number directly from member table (already without country code)
                $cleanPhone = $phoneNumber;
                
                // Check if already clean (no country code in user_credentials)
                if ($mobileNo === $cleanPhone) {
                    // No change needed
                    $this->stats['skipped_already_clean']++;
                    return [
                        'status' => 'skipped',
                        'reason' => 'Phone already clean'
                    ];
                }
                
                // Log the update
                $memberName = $member->firstName ?? 'N/A';
                if ($userType === 'S' && !empty($member->spouse_firstName)) {
                    $memberName = $member->spouse_firstName . ' (Spouse)';
                }
                
                $this->updateLog[] = [
                    $userCredential->id,
                    $userType,
                    $member->memberid,
                    $memberName,
                    $mobileNo,
                    $cleanPhone,
                    $this->dryRun ? 'DRY RUN' : 'UPDATED'
                ];
                
                // Update in database if not dry run
                if (!$this->dryRun) {
                    $userCredential->mobileno = $cleanPhone;
                    if (!$userCredential->save(false)) {
                        $this->stats['errors']++;
                        $this->errorLog[] = [
                            $userCredential->id,
                            $userType,
                            $member->memberid,
                            'Update Failed',
                            'Failed to save user credential'
                        ];
                        return [
                            'status' => 'error',
                            'error' => 'Failed to save user credential'
                        ];
                    }
                }
                
                // Update statistics
                if ($userType === 'M') {
                    $this->stats['member_credentials_updated']++;
                } elseif ($userType === 'S') {
                    $this->stats['spouse_credentials_updated']++;
                }
                
                $updated = true;
                
                return [
                    'status' => 'updated',
                    'old' => $mobileNo,
                    'new' => $cleanPhone
                ];
            }
            
            if (!$updated) {
                $this->stats['skipped_no_match']++;
                return [
                    'status' => 'skipped',
                    'reason' => 'No matching phone data found in member table'
                ];
            }
            
        } catch (\Throwable $e) {
            $this->stats['errors']++;
            $this->errorLog[] = [
                $userCredential->id,
                $userCredential->usertype ?? 'N/A',
                'N/A',
                'Processing Error',
                $e->getMessage()
            ];
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Check if phone number has country code (starts with +)
     */
    private function hasCountryCode($phone)
    {
        return !empty($phone) && strpos($phone, '+') === 0;
    }
    
    /**
     * Remove country code from phone number
     * Removes + and all digits after it until we get to the 10-digit phone number
     * Examples: +919876543210 -> 9876543210, +971501234567 -> 501234567 (if 9 digits), +12345678901 -> 2345678901
     */
    private function removeCountryCode($phone)
    {
        if (!$this->hasCountryCode($phone)) {
            return $phone;
        }
        
        // Remove all non-digits first to get clean number
        $digitsOnly = preg_replace('/\D/', '', $phone);
        
        // If we have more than 10 digits, take the last 10 digits (assuming 10-digit phone numbers)
        // This handles variable length country codes (1-3 digits)
        if (strlen($digitsOnly) > 10) {
            return substr($digitsOnly, -10);
        }
        
        // If exactly 10 or less, return as is
        return $digitsOnly;
    }
    
    /**
     * Normalize phone number for comparison (remove all non-digits)
     */
    private function normalizePhone($phone)
    {
        return preg_replace('/\D/', '', $phone);
    }
    
    /**
     * Save update log to CSV
     */
    private function saveUpdateLog()
    {
        if (count($this->updateLog) > 1) {
            $logPath = Yii::getAlias('@app') . '/service/institution/PhoneCleanupLog_' . $this->institutionId . '_' . date('YmdHis') . '.csv';
            $logFile = fopen($logPath, 'w');
            
            foreach ($this->updateLog as $logRow) {
                fputcsv($logFile, $logRow);
            }
            
            fclose($logFile);
            
            $this->stdout("\nUpdate log saved to: ", Console::FG_YELLOW);
            $this->stdout("$logPath\n", Console::FG_GREEN);
        }
    }
    
    /**
     * Save error log to CSV
     */
    private function saveErrorLog()
    {
        if (count($this->errorLog) > 1) {
            $errorLogPath = Yii::getAlias('@app') . '/service/institution/PhoneCleanupErrors_' . $this->institutionId . '_' . date('YmdHis') . '.csv';
            $errorFile = fopen($errorLogPath, 'w');
            
            foreach ($this->errorLog as $errorRow) {
                fputcsv($errorFile, $errorRow);
            }
            
            fclose($errorFile);
            
            $this->stdout("Error log saved to: ", Console::FG_YELLOW);
            $this->stdout("$errorLogPath\n", Console::FG_GREEN);
        }
    }
    
    /**
     * Print statistics
     */
    private function printStatistics()
    {
        $this->stdout("\n");
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        if ($this->dryRun) {
            $this->stdout("Dry Run Complete!\n", Console::FG_CYAN, Console::BOLD);
        } else {
            $this->stdout("Phone Cleanup Complete!\n", Console::FG_GREEN, Console::BOLD);
        }
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        
        $this->stdout("Total credentials processed:     ");
        $this->stdout($this->stats['total_credentials'] . "\n", Console::FG_CYAN);
        
        if ($this->dryRun) {
            $this->stdout("Member credentials to update:    ");
        } else {
            $this->stdout("Member credentials updated:      ");
        }
        $this->stdout($this->stats['member_credentials_updated'] . "\n", Console::FG_GREEN);
        
        if ($this->dryRun) {
            $this->stdout("Spouse credentials to update:    ");
        } else {
            $this->stdout("Spouse credentials updated:      ");
        }
        $this->stdout($this->stats['spouse_credentials_updated'] . "\n", Console::FG_GREEN);
        
        $this->stdout("Skipped (already clean):         ");
        $this->stdout($this->stats['skipped_already_clean'] . "\n", Console::FG_GREY);
        
        $this->stdout("Skipped (no match/no link):      ");
        $this->stdout($this->stats['skipped_no_match'] . "\n", Console::FG_YELLOW);
        
        $this->stdout("Errors:                          ");
        $this->stdout($this->stats['errors'] . "\n", Console::FG_RED);
        
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("\n");
        
        if ($this->dryRun) {
            $this->stdout("This was a DRY RUN. No changes were made to the database.\n", Console::FG_CYAN);
            $this->stdout("Review the logs and run without --dry-run to apply changes.\n\n");
        }
    }
}
