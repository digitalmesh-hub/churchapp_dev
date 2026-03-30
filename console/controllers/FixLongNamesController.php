<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use common\models\extendedmodels\ExtendedMember;

/**
 * Fix Long Names controller for correcting 4+ part names
 * 
 * Usage:
 * php yii fix-long-names/process --institution=1 --dry-run=1  (Safe mode - no DB updates)
 * php yii fix-long-names/process --institution=1               (Update DB)
 */
class FixLongNamesController extends Controller
{
    public $institutionId = 1;
    public $dryRun = false;
    
    public function options($actionID)
    {
        return ['institutionId', 'dryRun'];
    }
    
    public function optionAliases()
    {
        return [
            'i' => 'institutionId',
            'institution' => 'institutionId',
            'd' => 'dryRun',
            'dry-run' => 'dryRun'
        ];
    }
    
    /**
     * Fix long names (4+ parts) with correct splitting logic
     * 
     * @return int Exit code
     */
    public function actionProcess()
    {
        $this->stdout("\n");
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("Fix Long Names (4+ parts)\n", Console::FG_CYAN, Console::BOLD);
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("\n");
        
        // Validate institution
        if (empty($this->institutionId)) {
            $this->stderr("Error: Institution ID is required. Use --institution=ID\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }
        
        $this->stdout("Institution ID: {$this->institutionId}\n", Console::FG_GREEN);
        
        if ($this->dryRun) {
            $this->stdout("MODE: DRY RUN (No database updates)\n", Console::FG_YELLOW, Console::BOLD);
        } else {
            $this->stdout("MODE: LIVE (Database will be updated)\n", Console::FG_RED, Console::BOLD);
        }
        $this->stdout("\n");
        
        // Create output directory for CSV files
        $outputDir = Yii::getAlias('@app') . '/runtime/fix-long-names-' . date('Y-m-d-His');
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
        
        // CSV files
        $longNamesFile = fopen($outputDir . '/long-names-to-fix.csv', 'w');
        $previewFile = fopen($outputDir . '/preview-changes.csv', 'w');
        $updatedFile = fopen($outputDir . '/updated.csv', 'w');
        
        // Write CSV headers
        fputcsv($longNamesFile, [
            'memberid', 'memberno', 'type', 'full_name', 'parts_count',
            'current_firstName', 'current_middleName', 'current_lastName',
            'new_firstName', 'new_middleName', 'new_lastName'
        ]);
        
        fputcsv($previewFile, [
            'memberid', 'memberno', 'type', 'full_name', 'parts_count',
            'current_firstName', 'current_middleName', 'current_lastName',
            'new_firstName', 'new_middleName', 'new_lastName'
        ]);
        
        fputcsv($updatedFile, [
            'memberid', 'memberno', 'type', 'full_name', 'new_firstName', 'new_middleName', 'new_lastName'
        ]);
        
        // Statistics
        $stats = [
            'total' => 0,
            'member_to_fix' => 0,
            'spouse_to_fix' => 0,
            'member_fixed' => 0,
            'spouse_fixed' => 0,
            'errors' => 0
        ];
        
        // Fetch all members for the institution
        $query = ExtendedMember::find()
            ->where(['institutionid' => $this->institutionId])
            ->orderBy(['memberid' => SORT_ASC]);
        
        $totalMembers = $query->count();
        $this->stdout("Total members to process: {$totalMembers}\n\n", Console::FG_CYAN);
        
        $processedCount = 0;
        
        foreach ($query->batch(100) as $members) {
            foreach ($members as $member) {
                $stats['total']++;
                $processedCount++;
                
                if ($processedCount % 50 == 0) {
                    $this->stdout("Processed: {$processedCount}/{$totalMembers}\r");
                }
                
                $memberNeedsUpdate = false;
                $spouseNeedsUpdate = false;
                
                // Check member name
                if (!empty($member->firstName) || !empty($member->middleName) || !empty($member->lastName)) {
                    $currentFullName = trim($member->firstName . ' ' . $member->middleName . ' ' . $member->lastName);
                    $currentFullName = preg_replace('/\s+/', ' ', $currentFullName);
                    
                    if (!empty($currentFullName)) {
                        $parts = explode(' ', $currentFullName);
                        $partsCount = count($parts);
                        
                        // Only process names with 4+ parts
                        if ($partsCount >= 4) {
                            $newNames = $this->splitLongName($currentFullName);
                            
                            // Check if the split would be different
                            if ($member->firstName !== $newNames['firstName'] ||
                                $member->middleName !== $newNames['middleName'] ||
                                $member->lastName !== $newNames['lastName']) {
                                
                                $stats['member_to_fix']++;
                                
                                // Log to CSV
                                $csvRow = [
                                    $member->memberid,
                                    $member->memberno,
                                    'Member',
                                    $currentFullName,
                                    $partsCount,
                                    $member->firstName,
                                    $member->middleName,
                                    $member->lastName,
                                    $newNames['firstName'],
                                    $newNames['middleName'],
                                    $newNames['lastName']
                                ];
                                
                                fputcsv($longNamesFile, $csvRow);
                                fputcsv($previewFile, $csvRow);
                                
                                if (!$this->dryRun) {
                                    $member->firstName = $newNames['firstName'];
                                    $member->middleName = $newNames['middleName'];
                                    $member->lastName = $newNames['lastName'];
                                    $memberNeedsUpdate = true;
                                    
                                    fputcsv($updatedFile, [
                                        $member->memberid,
                                        $member->memberno,
                                        'Member',
                                        $currentFullName,
                                        $newNames['firstName'],
                                        $newNames['middleName'],
                                        $newNames['lastName']
                                    ]);
                                }
                            }
                        }
                    }
                }
                
                // Check spouse name
                if (!empty($member->spouse_firstName) || !empty($member->spouse_middleName) || !empty($member->spouse_lastName)) {
                    $currentSpouseFullName = trim($member->spouse_firstName . ' ' . $member->spouse_middleName . ' ' . $member->spouse_lastName);
                    $currentSpouseFullName = preg_replace('/\s+/', ' ', $currentSpouseFullName);
                    
                    if (!empty($currentSpouseFullName)) {
                        $spouseParts = explode(' ', $currentSpouseFullName);
                        $spousePartsCount = count($spouseParts);
                        
                        // Only process names with 4+ parts
                        if ($spousePartsCount >= 4) {
                            $newSpouseNames = $this->splitLongName($currentSpouseFullName);
                            
                            // Check if the split would be different
                            if ($member->spouse_firstName !== $newSpouseNames['firstName'] ||
                                $member->spouse_middleName !== $newSpouseNames['middleName'] ||
                                $member->spouse_lastName !== $newSpouseNames['lastName']) {
                                
                                $stats['spouse_to_fix']++;
                                
                                // Log to CSV
                                $csvRow = [
                                    $member->memberid,
                                    $member->memberno,
                                    'Spouse',
                                    $currentSpouseFullName,
                                    $spousePartsCount,
                                    $member->spouse_firstName,
                                    $member->spouse_middleName,
                                    $member->spouse_lastName,
                                    $newSpouseNames['firstName'],
                                    $newSpouseNames['middleName'],
                                    $newSpouseNames['lastName']
                                ];
                                
                                fputcsv($longNamesFile, $csvRow);
                                fputcsv($previewFile, $csvRow);
                                
                                if (!$this->dryRun) {
                                    $member->spouse_firstName = $newSpouseNames['firstName'];
                                    $member->spouse_middleName = $newSpouseNames['middleName'];
                                    $member->spouse_lastName = $newSpouseNames['lastName'];
                                    $spouseNeedsUpdate = true;
                                    
                                    fputcsv($updatedFile, [
                                        $member->memberid,
                                        $member->memberno,
                                        'Spouse',
                                        $currentSpouseFullName,
                                        $newSpouseNames['firstName'],
                                        $newSpouseNames['middleName'],
                                        $newSpouseNames['lastName']
                                    ]);
                                }
                            }
                        }
                    }
                }
                
                // Save changes if any updates were made (only in live mode)
                if (!$this->dryRun && ($memberNeedsUpdate || $spouseNeedsUpdate)) {
                    if ($member->save(false)) {
                        if ($memberNeedsUpdate) $stats['member_fixed']++;
                        if ($spouseNeedsUpdate) $stats['spouse_fixed']++;
                    } else {
                        $stats['errors']++;
                        $this->stderr("Error saving member ID: {$member->memberid}\n", Console::FG_RED);
                    }
                }
            }
        }
        
        // Close CSV files
        fclose($longNamesFile);
        fclose($previewFile);
        fclose($updatedFile);
        
        // Display results
        $this->stdout("\n\n");
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("Results\n", Console::FG_CYAN, Console::BOLD);
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("Total members processed: {$stats['total']}\n");
        
        if ($this->dryRun) {
            $this->stdout("Members to be fixed: {$stats['member_to_fix']}\n", Console::FG_CYAN);
            $this->stdout("Spouses to be fixed: {$stats['spouse_to_fix']}\n", Console::FG_CYAN);
        } else {
            $this->stdout("Members fixed: {$stats['member_fixed']}\n", Console::FG_GREEN);
            $this->stdout("Spouses fixed: {$stats['spouse_fixed']}\n", Console::FG_GREEN);
        }
        
        $this->stdout("Errors: {$stats['errors']}\n");
        $this->stdout("\n");
        
        $this->stdout("Output directory: {$outputDir}\n", Console::FG_CYAN);
        $this->stdout("  - long-names-to-fix.csv: All names with 4+ parts that need fixing\n");
        $this->stdout("  - preview-changes.csv: Preview of all changes (before/after)\n");
        if (!$this->dryRun) {
            $this->stdout("  - updated.csv: All records that were updated\n");
        }
        $this->stdout("\n");
        
        if ($this->dryRun) {
            $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
            $this->stdout("DRY RUN COMPLETE - No changes made to database\n", Console::FG_YELLOW, Console::BOLD);
            $this->stdout("Review the preview-changes.csv file and run without --dry-run to apply changes\n", Console::FG_YELLOW);
            $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        } else {
            $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
            $this->stdout("LIVE RUN COMPLETE - Database has been updated\n", Console::FG_GREEN, Console::BOLD);
            $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        }
        
        $this->stdout("\n");
        
        return ExitCode::OK;
    }
    
    /**
     * Split long names (4+ parts) correctly
     * 
     * Rules:
     * - firstName = first part (part[0])
     * - lastName = last part (part[n-1])
     * - middleName = all parts in between (part[1] to part[n-2])
     * 
     * Example: joe doe e thomas
     * - firstName: joe
     * - middleName: doe e
     * - lastName: thomas
     * 
     * @param string $fullName Full name to split
     * @return array ['firstName' => string, 'middleName' => string, 'lastName' => string]
     */
    private function splitLongName($fullName)
    {
        // Trim and remove extra spaces
        $fullName = trim($fullName);
        $fullName = preg_replace('/\s+/', ' ', $fullName);
        
        // Split by spaces
        $parts = explode(' ', $fullName);
        $partsCount = count($parts);
        
        $firstName = '';
        $middleName = '';
        $lastName = '';
        
        if ($partsCount == 1) {
            // Single name - treat as first name
            $firstName = $parts[0];
        } elseif ($partsCount == 2) {
            // Two parts: firstName + lastName
            $firstName = $parts[0];
            $lastName = $parts[1];
        } elseif ($partsCount == 3) {
            // Three parts: firstName + middleName + lastName
            $firstName = $parts[0];
            $middleName = $parts[1];
            $lastName = $parts[2];
        } else {
            // Four or more parts:
            // firstName = first part (part[0])
            // lastName = last part (part[n-1])
            // middleName = all parts in between (part[1] to part[n-2])
            $firstName = $parts[0];
            $lastName = $parts[$partsCount - 1];
            $middleName = implode(' ', array_slice($parts, 1, $partsCount - 2));
        }
        
        return [
            'firstName' => $firstName,
            'middleName' => $middleName,
            'lastName' => $lastName
        ];
    }
}
