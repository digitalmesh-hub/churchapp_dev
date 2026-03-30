<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use common\models\extendedmodels\ExtendedMember;

/**
 * Fix names from a specific CSV file
 * 
 * Usage:
 * php yii fix-names-from-csv/process --csv=path/to/file.csv --dry-run=1  (Safe mode)
 * php yii fix-names-from-csv/process --csv=path/to/file.csv               (Update DB)
 */
class FixNamesFromCsvController extends Controller
{
    public $csvFile = '';
    public $dryRun = false;
    
    public function options($actionID)
    {
        return ['csvFile', 'dryRun'];
    }
    
    public function optionAliases()
    {
        return [
            'csv' => 'csvFile',
            'd' => 'dryRun',
            'dry-run' => 'dryRun'
        ];
    }
    
    /**
     * Fix names from CSV file
     * 
     * @return int Exit code
     */
    public function actionProcess()
    {
        $this->stdout("\n");
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("Fix Names From CSV\n", Console::FG_CYAN, Console::BOLD);
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("\n");
        
        // Get CSV file path
        if (empty($this->csvFile)) {
            $this->csvFile = Yii::getAlias('@app') . '/runtime/name-split-2026-02-04-100519-live/long-names.csv';
        }
        
        // Convert relative path to absolute
        if (substr($this->csvFile, 0, 1) !== '/') {
            $this->csvFile = Yii::getAlias('@app') . '/runtime/' . $this->csvFile;
        }
        
        if (!file_exists($this->csvFile)) {
            $this->stderr("Error: CSV file not found at: {$this->csvFile}\n", Console::FG_RED);
            return ExitCode::NOINPUT;
        }
        
        $this->stdout("CSV File: {$this->csvFile}\n", Console::FG_GREEN);
        
        if ($this->dryRun) {
            $this->stdout("MODE: DRY RUN (No database updates)\n", Console::FG_YELLOW, Console::BOLD);
        } else {
            $this->stdout("MODE: LIVE (Database will be updated)\n", Console::FG_RED, Console::BOLD);
        }
        $this->stdout("\n");
        
        // Create output directory for CSV files
        $outputDir = Yii::getAlias('@app') . '/runtime/fix-from-csv-' . date('Y-m-d-His');
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
        
        // CSV files
        $previewFile = fopen($outputDir . '/preview-changes.csv', 'w');
        $updatedFile = fopen($outputDir . '/updated.csv', 'w');
        $errorFile = fopen($outputDir . '/errors.csv', 'w');
        
        // Write CSV headers
        fputcsv($previewFile, [
            'memberid', 'memberno', 'type', 'original_name', 'parts_count',
            'current_firstName', 'current_middleName', 'current_lastName',
            'new_firstName', 'new_middleName', 'new_lastName'
        ]);
        
        fputcsv($updatedFile, [
            'memberid', 'memberno', 'type', 'original_name', 'new_firstName', 'new_middleName', 'new_lastName'
        ]);
        
        fputcsv($errorFile, [
            'memberid', 'memberno', 'type', 'error_message'
        ]);
        
        // Statistics
        $stats = [
            'total_csv_rows' => 0,
            'member_to_fix' => 0,
            'spouse_to_fix' => 0,
            'member_fixed' => 0,
            'spouse_fixed' => 0,
            'not_found' => 0,
            'errors' => 0
        ];
        
        // Read CSV file
        $this->stdout("Reading CSV file...\n", Console::FG_CYAN);
        $csvHandle = fopen($this->csvFile, 'r');
        
        // Skip header row
        $header = fgetcsv($csvHandle);
        
        while (($row = fgetcsv($csvHandle)) !== false) {
            $stats['total_csv_rows']++;
            
            // Parse CSV row
            $memberid = $row[0];
            $memberno = $row[1];
            $type = $row[2];
            $originalName = trim($row[3], '"');
            
            $this->stdout("Processing: Member {$memberno} ({$type})...\r");
            
            // Find member record
            $member = ExtendedMember::findOne(['memberid' => $memberid]);
            
            if (!$member) {
                $stats['not_found']++;
                fputcsv($errorFile, [
                    $memberid,
                    $memberno,
                    $type,
                    "Member record not found"
                ]);
                continue;
            }
            
            // Split the original name correctly
            $newNames = $this->splitLongName($originalName);
            
            $csvRow = [
                $memberid,
                $memberno,
                $type,
                $originalName,
                count(explode(' ', $originalName))
            ];
            
            if ($type === 'Member') {
                $stats['member_to_fix']++;
                
                // Add current values
                $csvRow[] = $member->firstName;
                $csvRow[] = $member->middleName;
                $csvRow[] = $member->lastName;
                
                // Add new values
                $csvRow[] = $newNames['firstName'];
                $csvRow[] = $newNames['middleName'];
                $csvRow[] = $newNames['lastName'];
                
                fputcsv($previewFile, $csvRow);
                
                if (!$this->dryRun) {
                    $member->firstName = $newNames['firstName'];
                    $member->middleName = $newNames['middleName'];
                    $member->lastName = $newNames['lastName'];
                    
                    if ($member->save(false)) {
                        $stats['member_fixed']++;
                        fputcsv($updatedFile, [
                            $memberid,
                            $memberno,
                            $type,
                            $originalName,
                            $newNames['firstName'],
                            $newNames['middleName'],
                            $newNames['lastName']
                        ]);
                    } else {
                        $stats['errors']++;
                        fputcsv($errorFile, [
                            $memberid,
                            $memberno,
                            $type,
                            "Failed to save member"
                        ]);
                    }
                }
            } elseif ($type === 'Spouse') {
                $stats['spouse_to_fix']++;
                
                // Add current values
                $csvRow[] = $member->spouse_firstName;
                $csvRow[] = $member->spouse_middleName;
                $csvRow[] = $member->spouse_lastName;
                
                // Add new values
                $csvRow[] = $newNames['firstName'];
                $csvRow[] = $newNames['middleName'];
                $csvRow[] = $newNames['lastName'];
                
                fputcsv($previewFile, $csvRow);
                
                if (!$this->dryRun) {
                    $member->spouse_firstName = $newNames['firstName'];
                    $member->spouse_middleName = $newNames['middleName'];
                    $member->spouse_lastName = $newNames['lastName'];
                    
                    if ($member->save(false)) {
                        $stats['spouse_fixed']++;
                        fputcsv($updatedFile, [
                            $memberid,
                            $memberno,
                            $type,
                            $originalName,
                            $newNames['firstName'],
                            $newNames['middleName'],
                            $newNames['lastName']
                        ]);
                    } else {
                        $stats['errors']++;
                        fputcsv($errorFile, [
                            $memberid,
                            $memberno,
                            $type,
                            "Failed to save spouse"
                        ]);
                    }
                }
            }
        }
        
        fclose($csvHandle);
        
        // Close output CSV files
        fclose($previewFile);
        fclose($updatedFile);
        fclose($errorFile);
        
        // Display results
        $this->stdout("\n\n");
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("Results\n", Console::FG_CYAN, Console::BOLD);
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("Total CSV rows processed: {$stats['total_csv_rows']}\n");
        
        if ($this->dryRun) {
            $this->stdout("Members to be fixed: {$stats['member_to_fix']}\n", Console::FG_CYAN);
            $this->stdout("Spouses to be fixed: {$stats['spouse_to_fix']}\n", Console::FG_CYAN);
        } else {
            $this->stdout("Members fixed: {$stats['member_fixed']}\n", Console::FG_GREEN);
            $this->stdout("Spouses fixed: {$stats['spouse_fixed']}\n", Console::FG_GREEN);
        }
        
        $this->stdout("Not found: {$stats['not_found']}\n", $stats['not_found'] > 0 ? Console::FG_YELLOW : Console::FG_CYAN);
        $this->stdout("Errors: {$stats['errors']}\n", $stats['errors'] > 0 ? Console::FG_RED : Console::FG_CYAN);
        $this->stdout("\n");
        
        $this->stdout("Output directory: {$outputDir}\n", Console::FG_CYAN);
        $this->stdout("  - preview-changes.csv: Preview of all changes (before/after)\n");
        $this->stdout("  - errors.csv: Records that had errors\n");
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
     * Split long names correctly
     * 
     * Rules:
     * - firstName = first part (part[0])
     * - lastName = last part (part[n-1])
     * - middleName = all parts in between (part[1] to part[n-2])
     * 
     * Example: SUNIL KUMAR K B
     * - firstName: SUNIL
     * - middleName: KUMAR K
     * - lastName: B
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
