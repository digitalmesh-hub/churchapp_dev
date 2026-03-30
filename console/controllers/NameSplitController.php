<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use common\models\extendedmodels\ExtendedMember;

/**
 * Name Split controller for splitting member and spouse names
 * 
 * Usage:
 * php yii name-split/process --institution=1 --dry-run=1  (Safe mode - no DB updates)
 * php yii name-split/process --institution=1               (Update DB)
 */
class NameSplitController extends Controller
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
     * Split member and spouse names into first, middle, and last names
     * 
     * @return int Exit code
     */
    public function actionProcess()
    {
        $this->stdout("\n");
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("Split Member and Spouse Names\n", Console::FG_CYAN, Console::BOLD);
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
        $outputDir = Yii::getAlias('@app') . '/runtime/name-split-' . date('Y-m-d-His');
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
        
        // CSV files
        $alreadySplitFile = fopen($outputDir . '/already-split.csv', 'w');
        $longNamesFile = fopen($outputDir . '/long-names.csv', 'w');
        $updatedFile = fopen($outputDir . '/updated.csv', 'w');
        $previewFile = fopen($outputDir . '/preview-changes.csv', 'w');
        $cleanedNamesFile = fopen($outputDir . '/cleaned-names.csv', 'w');
        
        // Write CSV headers
        fputcsv($alreadySplitFile, [
            'memberid', 'memberno', 'member_firstName', 'member_middleName', 'member_lastName',
            'spouse_firstName', 'spouse_middleName', 'spouse_lastName'
        ]);
        
        fputcsv($longNamesFile, [
            'memberid', 'memberno', 'type', 'original_name', 'parts_count', 'suggested_firstName',
            'suggested_middleName', 'suggested_lastName', 'requires_review'
        ]);
        
        fputcsv($updatedFile, [
            'memberid', 'memberno', 'type', 'original_name', 'firstName', 'middleName', 'lastName'
        ]);
        
        fputcsv($previewFile, [
            'memberid', 'memberno', 'type', 'current_firstName', 'current_middleName', 'current_lastName',
            'new_firstName', 'new_middleName', 'new_lastName', 'parts_count'
        ]);
        
        fputcsv($cleanedNamesFile, [
            'memberid', 'memberno', 'type', 'original_name', 'cleaned_name', 'removed_parts'
        ]);
        
        // Statistics
        $stats = [
            'total' => 0,
            'already_split' => 0,
            'member_to_update' => 0,
            'spouse_to_update' => 0,
            'long_names' => 0,
            'errors' => 0,
            'member_updated' => 0,
            'spouse_updated' => 0,
            'names_cleaned' => 0
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
                
                $hasAlreadySplit = false;
                $memberNeedsUpdate = false;
                $spouseNeedsUpdate = false;
                
                // Check if member name is already split
                if (!empty($member->firstName) && !empty($member->lastName)) {
                    $hasAlreadySplit = true;
                }
                
                // Check if spouse name is already split
                $spouseAlreadySplit = false;
                if (!empty($member->spouse_firstName) && !empty($member->spouse_lastName)) {
                    $spouseAlreadySplit = true;
                }
                
                // If both are already split, add to already-split CSV and continue
                if ($hasAlreadySplit && $spouseAlreadySplit) {
                    $stats['already_split']++;
                    fputcsv($alreadySplitFile, [
                        $member->memberid,
                        $member->memberno,
                        $member->firstName,
                        $member->middleName,
                        $member->lastName,
                        $member->spouse_firstName,
                        $member->spouse_middleName,
                        $member->spouse_lastName
                    ]);
                    continue;
                }
                
                // Store original values for preview
                $originalMemberFirstName = $member->firstName;
                $originalMemberMiddleName = $member->middleName;
                $originalMemberLastName = $member->lastName;
                $originalSpouseFirstName = $member->spouse_firstName;
                $originalSpouseMiddleName = $member->spouse_middleName;
                $originalSpouseLastName = $member->spouse_lastName;
                
                // Process member name if not already split
                if (!$hasAlreadySplit && !empty($member->firstName)) {
                    $originalName = trim($member->firstName . ' ' . $member->middleName . ' ' . $member->lastName);
                    $originalName = preg_replace('/\s+/', ' ', $originalName); // Remove extra spaces
                    
                    if (!empty($originalName)) {
                        // Clean the name (remove titles and parentheses)
                        $cleanResult = $this->cleanName($originalName);
                        $cleanedName = $cleanResult['cleaned'];
                        
                        // Log if name was cleaned
                        if ($cleanResult['was_cleaned']) {
                            $stats['names_cleaned']++;
                            fputcsv($cleanedNamesFile, [
                                $member->memberid,
                                $member->memberno,
                                'Member',
                                $originalName,
                                $cleanedName,
                                $cleanResult['removed']
                            ]);
                        }
                        
                        $nameParts = $this->splitName($cleanedName);
                        
                        if ($nameParts['parts_count'] > 3) {
                            $stats['long_names']++;
                            fputcsv($longNamesFile, [
                                $member->memberid,
                                $member->memberno,
                                'Member',
                                $originalName,
                                $nameParts['parts_count'],
                                $nameParts['firstName'],
                                $nameParts['middleName'],
                                $nameParts['lastName'],
                                'YES'
                            ]);
                        }
                        
                        // Preview changes
                        fputcsv($previewFile, [
                            $member->memberid,
                            $member->memberno,
                            'Member',
                            $originalMemberFirstName,
                            $originalMemberMiddleName,
                            $originalMemberLastName,
                            $nameParts['firstName'],
                            $nameParts['middleName'],
                            $nameParts['lastName'],
                            $nameParts['parts_count']
                        ]);
                        
                        // Update member (only if not dry run)
                        $stats['member_to_update']++;
                        if (!$this->dryRun) {
                            $member->firstName = $nameParts['firstName'];
                            $member->middleName = $nameParts['middleName'];
                            $member->lastName = $nameParts['lastName'];
                            $memberNeedsUpdate = true;
                            
                            fputcsv($updatedFile, [
                                $member->memberid,
                                $member->memberno,
                                'Member',
                                $originalName,
                                $nameParts['firstName'],
                                $nameParts['middleName'],
                                $nameParts['lastName']
                            ]);
                        }
                    }
                }
                
                // Process spouse name if not already split
                if (!$spouseAlreadySplit && !empty($member->spouse_firstName)) {
                    $originalSpouseName = trim($member->spouse_firstName . ' ' . $member->spouse_middleName . ' ' . $member->spouse_lastName);
                    $originalSpouseName = preg_replace('/\s+/', ' ', $originalSpouseName);
                    
                    if (!empty($originalSpouseName)) {
                        // Clean the name (remove titles and parentheses)
                        $spouseCleanResult = $this->cleanName($originalSpouseName);
                        $cleanedSpouseName = $spouseCleanResult['cleaned'];
                        
                        // Log if name was cleaned
                        if ($spouseCleanResult['was_cleaned']) {
                            $stats['names_cleaned']++;
                            fputcsv($cleanedNamesFile, [
                                $member->memberid,
                                $member->memberno,
                                'Spouse',
                                $originalSpouseName,
                                $cleanedSpouseName,
                                $spouseCleanResult['removed']
                            ]);
                        }
                        
                        $spouseNameParts = $this->splitName($cleanedSpouseName);
                        
                        if ($spouseNameParts['parts_count'] > 3) {
                            $stats['long_names']++;
                            fputcsv($longNamesFile, [
                                $member->memberid,
                                $member->memberno,
                                'Spouse',
                                $originalSpouseName,
                                $spouseNameParts['parts_count'],
                                $spouseNameParts['firstName'],
                                $spouseNameParts['middleName'],
                                $spouseNameParts['lastName'],
                                'YES'
                            ]);
                        }
                        
                        // Preview changes
                        fputcsv($previewFile, [
                            $member->memberid,
                            $member->memberno,
                            'Spouse',
                            $originalSpouseFirstName,
                            $originalSpouseMiddleName,
                            $originalSpouseLastName,
                            $spouseNameParts['firstName'],
                            $spouseNameParts['middleName'],
                            $spouseNameParts['lastName'],
                            $spouseNameParts['parts_count']
                        ]);
                        
                        // Update spouse (only if not dry run)
                        $stats['spouse_to_update']++;
                        if (!$this->dryRun) {
                            $member->spouse_firstName = $spouseNameParts['firstName'];
                            $member->spouse_middleName = $spouseNameParts['middleName'];
                            $member->spouse_lastName = $spouseNameParts['lastName'];
                            $spouseNeedsUpdate = true;
                            
                            fputcsv($updatedFile, [
                                $member->memberid,
                                $member->memberno,
                                'Spouse',
                                $originalSpouseName,
                                $spouseNameParts['firstName'],
                                $spouseNameParts['middleName'],
                                $spouseNameParts['lastName']
                            ]);
                        }
                    }
                }
                
                // Save changes if any updates were made (only in live mode)
                if (!$this->dryRun && ($memberNeedsUpdate || $spouseNeedsUpdate)) {
                    if ($member->save(false)) {
                        if ($memberNeedsUpdate) $stats['member_updated']++;
                        if ($spouseNeedsUpdate) $stats['spouse_updated']++;
                    } else {
                        $stats['errors']++;
                        $this->stderr("Error saving member ID: {$member->memberid}\n", Console::FG_RED);
                    }
                }
            }
        }
        
        // Close CSV files
        fclose($alreadySplitFile);
        fclose($longNamesFile);
        fclose($updatedFile);
        fclose($previewFile);
        fclose($cleanedNamesFile);
        
        // Display results
        $this->stdout("\n\n");
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("Results\n", Console::FG_CYAN, Console::BOLD);
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("Total members processed: {$stats['total']}\n");
        $this->stdout("Already split (skipped): {$stats['already_split']}\n", Console::FG_YELLOW);
        
        if ($this->dryRun) {
            $this->stdout("Members to be updated: {$stats['member_to_update']}\n", Console::FG_CYAN);
            $this->stdout("Spouses to be updated: {$stats['spouse_to_update']}\n", Console::FG_CYAN);
        } else {
            $this->stdout("Members updated: {$stats['member_updated']}\n", Console::FG_GREEN);
            $this->stdout("Spouses updated: {$stats['spouse_updated']}\n", Console::FG_GREEN);
        }
        
        $this->stdout("Long names (4+ parts): {$stats['long_names']}\n", Console::FG_YELLOW);        $this->stdout("Names cleaned (titles/parentheses removed): {$stats['names_cleaned']}\n", Console::FG_CYAN);        $this->stdout("Errors: {$stats['errors']}\n", $stats['errors'] > 0 ? Console::FG_RED : Console::NORMAL);
        $this->stdout("\n");
        
        $this->stdout("Output directory: {$outputDir}\n", Console::FG_CYAN);
        $this->stdout("  - already-split.csv: Members already having split names\n");
        $this->stdout("  - long-names.csv: Names with more than 3 parts (need review)\n");
        $this->stdout("  - preview-changes.csv: Preview of all changes (before/after)\n");
        $this->stdout("  - cleaned-names.csv: Names with titles/parentheses removed\n");
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
     * Clean name by removing titles and parentheses
     * 
     * @param string $name Name to clean
     * @return array ['cleaned' => string, 'was_cleaned' => bool, 'removed' => string]
     */
    private function cleanName($name)
    {
        $originalName = $name;
        $removedParts = [];
        
        // Remove content in parentheses (including the parentheses)
        if (preg_match_all('/\([^)]*\)/', $name, $matches)) {
            foreach ($matches[0] as $match) {
                $removedParts[] = $match;
            }
            $name = preg_replace('/\([^)]*\)/', '', $name);
        }
        
        // Remove unclosed parentheses - everything from '(' to the end if no closing ')'
        if (strpos($name, '(') !== false) {
            $unclosed = substr($name, strpos($name, '('));
            if ($unclosed) {
                $removedParts[] = $unclosed;
            }
            $name = substr($name, 0, strpos($name, '('));
        }
        
        // Remove common titles (case insensitive)
        $titles = [
            'Dr\.?', 'Dr', 'Adv\.?', 'Adv', 'Rev\.?', 'Rev', 
            'Cdr\.?', 'Cdr', 'Cmdr\.?', 'Cmdr', 'Capt\.?', 'Capt',
            'Mr\.?', 'Mr', 'Mrs\.?', 'Mrs', 'Ms\.?', 'Ms',
            'Prof\.?', 'Prof', 'Sr\.?', 'Sr', 'Jr\.?', 'Jr',
            'Ph\.?D\.?', 'PhD', 'Retd\.?', 'Retd', 'comdre'
        ];
        
        $titlePattern = '/\b(' . implode('|', $titles) . ')\b/i';
        if (preg_match_all($titlePattern, $name, $titleMatches)) {
            foreach ($titleMatches[0] as $match) {
                if (!in_array($match, $removedParts)) {
                    $removedParts[] = $match;
                }
            }
            $name = preg_replace($titlePattern, '', $name);
        }
        
        // Remove standalone dots and other punctuation left after title removal
        $name = preg_replace('/\s+\.\s*/', ' ', $name); // Remove dots with spaces
        $name = preg_replace('/\.+$/', '', $name); // Remove trailing dots
        $name = preg_replace('/^\.+/', '', $name); // Remove leading dots
        
        // Clean up extra spaces
        $name = preg_replace('/\s+/', ' ', $name);
        $name = trim($name);
        
        $wasCleaned = ($originalName !== $name);
        $removed = implode(', ', $removedParts);
        
        return [
            'cleaned' => $name,
            'was_cleaned' => $wasCleaned,
            'removed' => $removed
        ];
    }
    
    /**
     * Split a full name into first, middle, and last names
     * 
     * Rules:
     * - 1 part: firstName only
     * - 2 parts: firstName + lastName
     * - 3 parts: firstName + middleName + lastName
     * - 4+ parts: firstName (part 0) + lastName (part 2) + middleName (all other parts)
     *   Example: SUNIL KUMAR K B -> first: SUNIL, middle: KUMAR B, last: K
     * 
     * @param string $fullName Full name to split
     * @return array ['firstName' => string, 'middleName' => string, 'lastName' => string, 'parts_count' => int]
     */
    private function splitName($fullName)
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
            // firstName = part[0]
            // lastName = part[2]
            // middleName = part[1] + part[3] + part[4] + ... (all except parts 0 and 2)
            $firstName = $parts[0];
            $lastName = $parts[2];
            
            // Collect all parts except 0 and 2 for middle name
            $middleParts = [];
            for ($i = 1; $i < $partsCount; $i++) {
                if ($i != 2) {
                    $middleParts[] = $parts[$i];
                }
            }
            $middleName = implode(' ', $middleParts);
        }
        
        return [
            'firstName' => $firstName,
            'middleName' => $middleName,
            'lastName' => $lastName,
            'parts_count' => $partsCount
        ];
    }
}
