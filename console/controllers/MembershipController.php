<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use common\models\extendedmodels\ExtendedMember;
use common\models\extendedmodels\ExtendedZone;

/**
 * Membership management console commands
 * 
 * Convert members from CSV to FM numbers:
 * php yii membership/convert-fm-from-csv --institutionId=1 --dryRun=1  (preview)
 * php yii membership/convert-fm-from-csv --institutionId=1              (apply)
 * php yii membership/convert-fm-from-csv -i 1 -d 1                      (short form)
 * 
 * Update all membership numbers based on zone:
 * php yii membership/update-numbers --institutionId=1 --dryRun=1  (preview)
 * php yii membership/update-numbers --institutionId=1              (apply)
 * php yii membership/update-numbers -i 1 -d 1                      (short form)
 */
class MembershipController extends Controller
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
            'd' => 'dryRun'
        ];
    }
    
    /**
     * Convert members from CSV to FM numbers
     * Reads console/FM List.csv and assigns FM numbers starting from highest existing + 1
     * 
     * Usage:
     * php yii membership/convert-fm-from-csv --institutionId=1 --dryRun=1 (preview)
     * php yii membership/convert-fm-from-csv --institutionId=1 (apply)
     * php yii membership/convert-fm-from-csv -i 1 -d 1 (short form)
     * 
     * @return int Exit code
     */
    public function actionConvertFmFromCsv()
    {
        $this->stdout("\n");
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("Convert Members from CSV to FM Numbers\n", Console::FG_CYAN, Console::BOLD);
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("\n");
        
        // Validate institution
        if (empty($this->institutionId)) {
            $this->stderr("Error: Institution ID is required. Use --institutionId=ID or -i ID\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }
        
        // Show mode
        if ($this->dryRun) {
            $this->stdout("MODE: DRY RUN (No changes will be made)\n", Console::FG_YELLOW, Console::BOLD);
        } else {
            $this->stdout("MODE: LIVE (Database will be updated)\n", Console::FG_GREEN, Console::BOLD);
        }
        $this->stdout("\n");
        
        // Check CSV file exists
        $csvPath = Yii::getAlias('@console') . '/FM List.csv';
        if (!file_exists($csvPath)) {
            $this->stderr("Error: CSV file not found at {$csvPath}\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }
        
        $this->stdout("Reading CSV file: {$csvPath}\n", Console::FG_CYAN);
        
        // Read CSV file
        $csvData = [];
        if (($handle = fopen($csvPath, 'r')) !== false) {
            $headers = fgetcsv($handle); // Skip header row
            $rowNum = 1;
            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;
                if (isset($row[1]) && !empty(trim($row[1]))) { // member no column
                    $csvData[] = [
                        'row' => $rowNum,
                        'current_memberno' => trim($row[1]),
                        'name' => isset($row[2]) ? trim($row[2]) : '',
                        'dir_no' => isset($row[3]) ? trim($row[3]) : '',
                    ];
                }
            }
            fclose($handle);
        }
        
        $this->stdout("Found " . count($csvData) . " members in CSV\n", Console::FG_GREEN);
        $this->stdout("\n");
        
        // Find highest existing FM number
        $highestFM = ExtendedMember::find()
            ->where(['institutionid' => $this->institutionId])
            ->andWhere(['LIKE', 'memberno', 'FM-%', false])
            ->orderBy(['CAST(SUBSTRING(memberno, 4) AS UNSIGNED)' => SORT_DESC])
            ->one();
        
        $startNumber = 1001; // Default starting number
        if ($highestFM && preg_match('/FM-(\d+)/', $highestFM->memberno, $matches)) {
            $startNumber = intval($matches[1]) + 1;
            $this->stdout("Highest existing FM number: {$highestFM->memberno}\n", Console::FG_CYAN);
            $this->stdout("Starting new FM numbers from: FM-{$startNumber}\n", Console::FG_GREEN);
        } else {
            $this->stdout("No existing FM numbers found. Starting from: FM-{$startNumber}\n", Console::FG_YELLOW);
        }
        $this->stdout("\n");
        
        // Prepare changes
        $changes = [];
        $notFound = [];
        $fmCounter = $startNumber;
        
        foreach ($csvData as $csvRow) {
            // Find member by current membership number
            $member = ExtendedMember::find()
                ->where([
                    'institutionid' => $this->institutionId,
                    'memberno' => $csvRow['current_memberno'],
                    'active' => 1
                ])
                ->one();
            
            if ($member) {
                $newMemberNo = 'FM-' . $fmCounter;
                $changes[] = [
                    'memberid' => $member->memberid,
                    'name' => trim($member->firstName . ' ' . $member->lastName),
                    'csv_name' => $csvRow['name'],
                    'old_memberno' => $member->memberno,
                    'new_memberno' => $newMemberNo,
                    'dir_no' => $csvRow['dir_no'],
                ];
                $fmCounter++;
            } else {
                $notFound[] = [
                    'memberno' => $csvRow['current_memberno'],
                    'csv_name' => $csvRow['name'],
                    'row' => $csvRow['row'],
                ];
            }
        }
        
        $this->stdout("Members to update: " . count($changes) . "\n", Console::FG_GREEN);
        if (count($notFound) > 0) {
            $this->stdout("Members not found in database: " . count($notFound) . "\n", Console::FG_YELLOW);
        }
        $this->stdout("\n");
        
        // Show not found members
        if (count($notFound) > 0) {
            $this->stdout("Members not found in database:\n", Console::FG_YELLOW, Console::BOLD);
            $this->stdout(str_repeat("-", 80) . "\n");
            foreach ($notFound as $nf) {
                $this->stdout("Row {$nf['row']}: {$nf['memberno']} - {$nf['csv_name']}\n", Console::FG_YELLOW);
            }
            $this->stdout(str_repeat("-", 80) . "\n");
            $this->stdout("\n");
        }
        
        // Show preview
        if (count($changes) > 0) {
            $this->stdout("Preview of changes:\n", Console::FG_CYAN, Console::BOLD);
            $this->stdout(str_repeat("-", 80) . "\n");
            $this->stdout(sprintf(
                "%-8s %-25s %-15s %-15s %-15s\n",
                "ID",
                "Name",
                "Old Number",
                "New Number",
                "Dir. No"
            ), Console::BOLD);
            $this->stdout(str_repeat("-", 80) . "\n");
            
            $previewLimit = 10;
            $displayedCount = 0;
            foreach ($changes as $change) {
                if ($displayedCount < $previewLimit) {
                    $this->stdout(sprintf(
                        "%-8s %-25s %-15s %-15s %-15s\n",
                        $change['memberid'],
                        substr($change['name'], 0, 23),
                        $change['old_memberno'],
                        $change['new_memberno'],
                        substr($change['dir_no'], 0, 13)
                    ));
                    $displayedCount++;
                }
            }
            
            if (count($changes) > $previewLimit) {
                $this->stdout("... and " . (count($changes) - $previewLimit) . " more\n", Console::FG_YELLOW);
            }
            $this->stdout(str_repeat("-", 80) . "\n");
            $this->stdout("\n");
        }
        
        // Export to CSV
        $csvExportPath = Yii::getAlias('@console') . '/runtime/fm_conversion_changes_' . date('Y-m-d_His') . '.csv';
        $csvDir = dirname($csvExportPath);
        if (!is_dir($csvDir)) {
            mkdir($csvDir, 0755, true);
        }
        
        $fp = fopen($csvExportPath, 'w');
        fputcsv($fp, ['Member ID', 'Name', 'CSV Name', 'Old Membership Number', 'New Membership Number', 'Directory Number']);
        foreach ($changes as $change) {
            fputcsv($fp, [
                $change['memberid'],
                $change['name'],
                $change['csv_name'],
                $change['old_memberno'],
                $change['new_memberno'],
                $change['dir_no']
            ]);
        }
        
        // Add not found section
        if (count($notFound) > 0) {
            fputcsv($fp, []);
            fputcsv($fp, ['NOT FOUND IN DATABASE']);
            fputcsv($fp, ['CSV Row', 'Membership Number', 'Name']);
            foreach ($notFound as $nf) {
                fputcsv($fp, [$nf['row'], $nf['memberno'], $nf['csv_name']]);
            }
        }
        fclose($fp);
        
        $this->stdout("CSV export saved to: {$csvExportPath}\n", Console::FG_GREEN, Console::BOLD);
        $this->stdout("\n");
        
        // Apply changes if not dry run
        if (!$this->dryRun && count($changes) > 0) {
            $this->stdout("Applying changes...\n", Console::FG_YELLOW, Console::BOLD);
            
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $updated = 0;
                $errors = 0;
                
                foreach ($changes as $change) {
                    $member = ExtendedMember::findOne($change['memberid']);
                    if ($member) {
                        $member->memberno = $change['new_memberno'];
                        if ($member->save(false)) {
                            $updated++;
                            $this->stdout(".", Console::FG_GREEN);
                        } else {
                            $errors++;
                            $this->stdout("E", Console::FG_RED);
                            $this->stderr("\nError updating member ID {$change['memberid']}: " . json_encode($member->errors) . "\n", Console::FG_RED);
                        }
                    }
                }
                
                $transaction->commit();
                
                $this->stdout("\n\n");
                $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
                $this->stdout("Conversion Complete!\n", Console::FG_GREEN, Console::BOLD);
                $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
                $this->stdout("Successfully updated: {$updated}\n", Console::FG_GREEN);
                if ($errors > 0) {
                    $this->stdout("Errors: {$errors}\n", Console::FG_RED);
                }
                $this->stdout("CSV tracking file: {$csvExportPath}\n", Console::FG_CYAN);
                $this->stdout("\n");
                
            } catch (\Exception $e) {
                $transaction->rollBack();
                $this->stderr("Error during update: " . $e->getMessage() . "\n", Console::FG_RED);
                $this->stderr("Stack trace: " . $e->getTraceAsString() . "\n", Console::FG_RED);
                return ExitCode::SOFTWARE;
            }
        } else {
            $this->stdout("DRY RUN complete. No changes were made.\n", Console::FG_YELLOW, Console::BOLD);
            $this->stdout("Review the CSV file to verify changes.\n", Console::FG_CYAN);
            $this->stdout("To apply changes, run without --dryRun flag:\n", Console::FG_CYAN);
            $this->stdout("  php yii membership/convert-fm-from-csv --institutionId={$this->institutionId}\n");
            $this->stdout("\n");
        }
        
        return ExitCode::OK;
    }
    
    /**
     * Update membership numbers based on zone criteria
     * FM zone members get FM-1001, FM-1002, etc.
     * Other zone members get RM-1001, RM-1002, etc.
     * 
     * Usage:
     * php yii membership/update-numbers --institutionId=1 --dryRun=1 (preview changes)
     * php yii membership/update-numbers --institutionId=1 (apply changes)
     * php yii membership/update-numbers -i 1 -d 1 (short form)
     * 
     * @return int Exit code
     */
    public function actionUpdateNumbers()
    {
        $this->stdout("\n");
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("Update Membership Numbers Based on Zone\n", Console::FG_CYAN, Console::BOLD);
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("\n");
        
        // Validate institution
        if (empty($this->institutionId)) {
            $this->stderr("Error: Institution ID is required. Use --institutionId=ID or -i ID\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }
        
        // Show mode
        if ($this->dryRun) {
            $this->stdout("MODE: DRY RUN (No changes will be made)\n", Console::FG_YELLOW, Console::BOLD);
        } else {
            $this->stdout("MODE: LIVE (Database will be updated)\n", Console::FG_GREEN, Console::BOLD);
        }
        $this->stdout("\n");
        
        // Find FM zone
        $fmZone = ExtendedZone::find()
            ->where(['institutionid' => $this->institutionId, 'active' => 1])
            ->andWhere(['LIKE', 'description', 'FM'])
            ->one();
        
        if (!$fmZone) {
            $this->stdout("Warning: FM zone not found. All members will be assigned RM prefix.\n", Console::FG_YELLOW);
        } else {
            $this->stdout("FM Zone found: {$fmZone->description} (ID: {$fmZone->zoneid})\n", Console::FG_GREEN);
        }
        $this->stdout("\n");
        
        // Get all active members for this institution
        $members = ExtendedMember::find()
            ->where(['institutionid' => $this->institutionId, 'active' => 1])
            ->orderBy(['memberid' => SORT_ASC])
            ->all();
        
        $this->stdout("Found " . count($members) . " active members\n", Console::FG_CYAN);
        $this->stdout("\n");
        
        // Separate members by zone type
        $fmMembers = [];
        $rmMembers = [];
        
        foreach ($members as $member) {
            if ($fmZone && $member->zone_id == $fmZone->zoneid) {
                $fmMembers[] = $member;
            } else {
                $rmMembers[] = $member;
            }
        }
        
        $this->stdout("FM Zone Members: " . count($fmMembers) . "\n", Console::FG_CYAN);
        $this->stdout("RM Zone Members: " . count($rmMembers) . "\n", Console::FG_CYAN);
        $this->stdout("\n");
        
        // Prepare changes
        $changes = [];
        $fmCounter = 1001;
        $rmCounter = 1001;
        
        // Assign FM numbers
        foreach ($fmMembers as $member) {
            $newMemberNo = 'FM-' . $fmCounter;
            $changes[] = [
                'memberid' => $member->memberid,
                'name' => trim($member->firstName . ' ' . $member->lastName),
                'zone' => $member->zone ? $member->zone->description : 'N/A',
                'old_memberno' => $member->memberno ? $member->memberno : '(empty)',
                'new_memberno' => $newMemberNo,
            ];
            $fmCounter++;
        }
        
        // Assign RM numbers
        foreach ($rmMembers as $member) {
            $newMemberNo = 'RM-' . $rmCounter;
            $changes[] = [
                'memberid' => $member->memberid,
                'name' => trim($member->firstName . ' ' . $member->lastName),
                'zone' => $member->zone ? $member->zone->description : 'N/A',
                'old_memberno' => $member->memberno ? $member->memberno : '(empty)',
                'new_memberno' => $newMemberNo,
            ];
            $rmCounter++;
        }
        
        // Show preview
        $this->stdout("Preview of changes:\n", Console::FG_CYAN, Console::BOLD);
        $this->stdout(str_repeat("-", 80) . "\n");
        $this->stdout(sprintf(
            "%-8s %-30s %-15s %-15s %-15s\n",
            "ID",
            "Name",
            "Zone",
            "Old Number",
            "New Number"
        ), Console::BOLD);
        $this->stdout(str_repeat("-", 80) . "\n");
        
        $previewLimit = 10;
        $displayedCount = 0;
        foreach ($changes as $change) {
            if ($displayedCount < $previewLimit) {
                $this->stdout(sprintf(
                    "%-8s %-30s %-15s %-15s %-15s\n",
                    $change['memberid'],
                    substr($change['name'], 0, 28),
                    substr($change['zone'], 0, 13),
                    $change['old_memberno'],
                    $change['new_memberno']
                ));
                $displayedCount++;
            }
        }
        
        if (count($changes) > $previewLimit) {
            $this->stdout("... and " . (count($changes) - $previewLimit) . " more\n", Console::FG_YELLOW);
        }
        $this->stdout(str_repeat("-", 80) . "\n");
        $this->stdout("\n");
        
        // Export to CSV
        $csvPath = Yii::getAlias('@console') . '/runtime/membership_number_changes_' . date('Y-m-d_His') . '.csv';
        $csvDir = dirname($csvPath);
        if (!is_dir($csvDir)) {
            mkdir($csvDir, 0755, true);
        }
        
        $fp = fopen($csvPath, 'w');
        fputcsv($fp, ['Member ID', 'Name', 'Zone', 'Old Membership Number', 'New Membership Number']);
        foreach ($changes as $change) {
            fputcsv($fp, [
                $change['memberid'],
                $change['name'],
                $change['zone'],
                $change['old_memberno'],
                $change['new_memberno']
            ]);
        }
        fclose($fp);
        
        $this->stdout("CSV export saved to: {$csvPath}\n", Console::FG_GREEN, Console::BOLD);
        $this->stdout("\n");
        
        // Apply changes if not dry run
        if (!$this->dryRun) {
            $this->stdout("Applying changes...\n", Console::FG_YELLOW, Console::BOLD);
            
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $updated = 0;
                $errors = 0;
                
                foreach ($changes as $change) {
                    $member = ExtendedMember::findOne($change['memberid']);
                    if ($member) {
                        $member->memberno = $change['new_memberno'];
                        if ($member->save(false)) {
                            $updated++;
                            $this->stdout(".", Console::FG_GREEN);
                        } else {
                            $errors++;
                            $this->stdout("E", Console::FG_RED);
                            $this->stderr("\nError updating member ID {$change['memberid']}: " . json_encode($member->errors) . "\n", Console::FG_RED);
                        }
                    }
                }
                
                $transaction->commit();
                
                $this->stdout("\n\n");
                $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
                $this->stdout("Update Complete!\n", Console::FG_GREEN, Console::BOLD);
                $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
                $this->stdout("Successfully updated: {$updated}\n", Console::FG_GREEN);
                if ($errors > 0) {
                    $this->stdout("Errors: {$errors}\n", Console::FG_RED);
                }
                $this->stdout("CSV tracking file: {$csvPath}\n", Console::FG_CYAN);
                $this->stdout("\n");
                
            } catch (\Exception $e) {
                $transaction->rollBack();
                $this->stderr("Error during update: " . $e->getMessage() . "\n", Console::FG_RED);
                $this->stderr("Stack trace: " . $e->getTraceAsString() . "\n", Console::FG_RED);
                return ExitCode::SOFTWARE;
            }
        } else {
            $this->stdout("DRY RUN complete. No changes were made.\n", Console::FG_YELLOW, Console::BOLD);
            $this->stdout("Review the CSV file to verify changes.\n", Console::FG_CYAN);
            $this->stdout("To apply changes, run without --dryRun flag:\n", Console::FG_CYAN);
            $this->stdout("  php yii membership/update-numbers --institutionId={$this->institutionId}\n");
            $this->stdout("\n");
        }
        
        return ExitCode::OK;
    }
}
