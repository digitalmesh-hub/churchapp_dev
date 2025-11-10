<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use common\models\extendedmodels\ExtendedMember;
use common\models\extendedmodels\ExtendedDependant;
use common\models\extendedmodels\ExtendedTitle;
use common\models\extendedmodels\ExtendedUserCredentials;
use common\models\extendedmodels\ExtendedUserMember;
use common\models\extendedmodels\ExtendedSettings;

/**
 * Import controller for CSV data imports
 * 
 * Usage:
 * php yii import/family-members --file=/path/to/file.csv --institution=1
 */
class ImportController extends Controller
{
    public $institutionId = 1;
    public $csvFile = '';
    public $defaultPassword = 'remember';
    public $resume = false;
    public $dryRun = false;
    
    // Statistics
    private $stats = [
        'total_rows' => 0,
        'members_created' => 0,
        'spouses_added' => 0,
        'dependants_created' => 0,
        'user_credentials_created' => 0,
        'errors' => 0,
        'skipped' => 0
    ];
    
    private $errorLog = [];
    private $progressFile = '';
    private $lastProcessedFamily = null;
    private $titleMap = [];
    private $previewData = [];
    
    public function options($actionID)
    {
        return ['institutionId', 'csvFile', 'defaultPassword', 'resume', 'dryRun'];
    }
    
    public function optionAliases()
    {
        return [
            'i' => 'institutionId',
            'f' => 'csvFile',
            'p' => 'defaultPassword',
            'r' => 'resume',
            'd' => 'dryRun'
        ];
    }
    
    /**
     * Import family members from CSV file
     * 
     * @return int Exit code
     */
    public function actionFamilyMembers()
    {
        $this->stdout("\n");
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("Family Members CSV Import\n", Console::FG_CYAN, Console::BOLD);
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("\n");
        
        // Validate institution
        if (empty($this->institutionId)) {
            $this->stderr("Error: Institution ID is required. Use --institution=ID\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }
        
        // Get CSV file path
        if (empty($this->csvFile)) {
            $this->csvFile = Yii::getAlias('@app') . '/service/institution/FamilyMembers.csv';
        }
        
        if (!file_exists($this->csvFile)) {
            $this->stderr("Error: CSV file not found at: {$this->csvFile}\n", Console::FG_RED);
            return ExitCode::NOINPUT;
        }
        
        // Set progress file path
        $this->progressFile = Yii::getAlias('@app') . '/runtime/import_progress_' . $this->institutionId . '.json';
        
        $this->stdout("Institution ID: ", Console::FG_YELLOW);
        $this->stdout("{$this->institutionId}\n");
        $this->stdout("CSV File: ", Console::FG_YELLOW);
        $this->stdout("{$this->csvFile}\n");
        $this->stdout("Default Password: ", Console::FG_YELLOW);
        $this->stdout("{$this->defaultPassword}\n");
        $this->stdout("Mode: ", Console::FG_YELLOW);
        if ($this->dryRun) {
            $this->stdout("DRY RUN (Preview Only - No Database Changes)\n", Console::FG_CYAN, Console::BOLD);
        } else {
            $this->stdout("LIVE (Will Insert to Database)\n", Console::FG_GREEN, Console::BOLD);
        }
        
        // Check for resume
        if ($this->resume && file_exists($this->progressFile)) {
            $progress = json_decode(file_get_contents($this->progressFile), true);
            $this->lastProcessedFamily = $progress['lastProcessedFamily'] ?? null;
            $this->stats = $progress['stats'] ?? $this->stats;
            
            $this->stdout("Resume Mode: ", Console::FG_CYAN);
            $this->stdout("ENABLED\n", Console::FG_GREEN, Console::BOLD);
            $this->stdout("Last Processed Family: ", Console::FG_YELLOW);
            $this->stdout($this->lastProcessedFamily ? $this->lastProcessedFamily : 'None' . "\n");
        } else {
            $this->stdout("Resume Mode: ", Console::FG_CYAN);
            $this->stdout("DISABLED (Fresh import)\n");
            // Clear old progress file if not resuming
            if (file_exists($this->progressFile)) {
                unlink($this->progressFile);
            }
        }
        
        $this->stdout("\n");
        
        // Confirm before proceeding
        if (!$this->confirm("Do you want to proceed with the import?")) {
            $this->stdout("Import cancelled.\n", Console::FG_YELLOW);
            return ExitCode::OK;
        }
        
        $this->stdout("\n");
        
        // Initialize error log
        $this->errorLog[] = ['Issue', 'FamilyID', 'PersonID', 'Name', 'Relation', 'Details'];
        
        // Read and parse CSV
        $csvData = array_map('str_getcsv', file($this->csvFile));
        $headers = array_shift($csvData); // Remove header row
        
        // Group by FamilyID
        $families = $this->groupByFamily($csvData);
        
        $totalFamilies = count($families);
        $this->stdout("Found " . Console::ansiFormat($totalFamilies, [Console::FG_GREEN, Console::BOLD]) . " families to process\n");
        
        // Initialize title map
        $this->initializeTitleMap();
        
        // Determine starting point
        $shouldSkip = $this->resume && $this->lastProcessedFamily !== null;
        $skippedCount = 0;
        
        if ($shouldSkip) {
            $this->stdout("Skipping already processed families...\n", Console::FG_CYAN);
        }
        
        $this->stdout("\n");
        
        // Process each family
        $processedCount = 0;
        foreach ($families as $familyKey => $members) {
            // Skip until we reach the last processed family
            if ($shouldSkip) {
                $skippedCount++;
                if ($familyKey == $this->lastProcessedFamily) {
                    $shouldSkip = false;
                    $this->stdout("Resuming from Family: ", Console::FG_CYAN, Console::BOLD);
                    $this->stdout($familyKey . "\n\n");
                }
                continue;
            }
            
            $processedCount++;
            $currentPosition = $skippedCount + $processedCount;
            
            // Extract display info from first member
            $firstMember = $members[0];
            $displayId = $firstMember['PersonIDPrefix'];
            
            $this->stdout("[{$currentPosition}/{$totalFamilies}] ");
            $this->stdout("Processing Family: ", Console::FG_CYAN);
            $this->stdout("{$displayId} ", Console::BOLD);
            $this->stdout("(FamilyID: {$firstMember['FamilyID']})\n");
            
            $result = $this->processFamily($familyKey, $members);
            
            if ($result['success']) {
                $this->stdout("  ✓ ", Console::FG_GREEN, Console::BOLD);
                $this->stdout("Family processed successfully\n\n");
            } else {
                $this->stdout("  ✗ ", Console::FG_RED, Console::BOLD);
                $this->stdout($result['error'] . "\n\n", Console::FG_RED);
            }
            
            // Save progress after each family
            $this->saveProgress($familyKey);
        }
        
        // Save error log
        $this->saveErrorLog();
        
        // Save preview data if dry run
        if ($this->dryRun) {
            $this->savePreviewData();
        }
        
        // Print statistics
        $this->printStatistics();
        
        // Clean up progress file on successful completion
        if (file_exists($this->progressFile)) {
            unlink($this->progressFile);
            $this->stdout("Progress file cleaned up.\n", Console::FG_GREEN);
        }
        
        return ExitCode::OK;
    }
    
    /**
     * Group CSV rows by PersonID prefix (actual family grouping)
     * PersonID format: A-04-1, A-04-2, etc.
     * Family key will be: FamilyID + PersonID prefix (e.g., "4_A-04")
     */
    private function groupByFamily($csvData)
    {
        $families = [];
        
        foreach ($csvData as $row) {
            if (count($row) < 16) continue;
            
            $familyId = trim($row[0]);
            $personId = trim($row[1]);
            
            if (empty($familyId) || empty($personId)) continue;
            
            // Extract PersonID prefix (e.g., "A-04" from "A-04-1")
            // Match pattern: letters/numbers followed by dash and numbers, before the last dash
            if (preg_match('/^(.+-\d+)-\d+$/', $personId, $matches)) {
                $personIdPrefix = $matches[1]; // e.g., "A-04"
            } else {
                // Fallback: use the personId as-is if pattern doesn't match
                $personIdPrefix = $personId;
            }
            
            // Create unique family key: FamilyID_PersonIDPrefix
            $familyKey = $familyId . '_' . $personIdPrefix;
            
            if (!isset($families[$familyKey])) {
                $families[$familyKey] = [];
            }
            
            $families[$familyKey][] = [
                'FamilyID' => $familyId,
                'PersonID' => $personId,
                'PersonIDPrefix' => $personIdPrefix,
                'Prefix' => trim($row[2]),
                'Name' => trim($row[3]),
                'Nickname' => trim($row[4]),
                'Relation' => strtoupper(trim($row[5])),
                'Sex' => trim($row[6]),
                'MaritalStatus' => trim($row[7]),
                'Confirmed' => trim($row[8]),
                'DOB' => trim($row[9]),
                'DOM' => trim($row[10]),
                'Occupation' => trim($row[11]),
                'Phone' => trim($row[12]),
                'Email' => trim($row[13]),
                'DeathStatus' => trim($row[14]),
                'Active' => trim($row[15])
            ];
            
            $this->stats['total_rows']++;
        }
        
        return $families;
    }
    
    /**
     * Process a single family
     * @param string $familyKey Unique key (FamilyID_PersonIDPrefix)
     * @param array $members Array of family members
     */
    private function processFamily($familyKey, $members)
    {
        // Extract actual FamilyID and PersonIDPrefix from first member
        $familyId = $members[0]['FamilyID'];
        $personIdPrefix = $members[0]['PersonIDPrefix'];
        
        // Find head of family, spouse, and dependants
        $headOfFamily = null;
        $spouse = null;
        $dependants = [];
        $husbandCount = 0;
        $wifeCount = 0;
        $headCount = 0;
        
        foreach ($members as $member) {
            $relation = $member['Relation'];
            
            if ($relation === 'HEAD OF FAMILY') {
                $headOfFamily = $member;
                $headCount++;
            } elseif ($relation === 'HUSBAND') {
                $husbandCount++;
                if (!$spouse) $spouse = $member;
            } elseif ($relation === 'WIFE') {
                $wifeCount++;
                if (!$spouse) $spouse = $member;
            } else {
                $dependants[] = $member;
            }
        }
        
        // Validate family structure
        // Check for invalid combinations
        if ($headCount > 1) {
            $this->stdout("  ⊘ SKIPPED: Multiple HEAD OF FAMILY members found\n", Console::FG_GREY);
            $this->errorLog[] = [
                'Invalid Family Structure',
                $personIdPrefix,
                '',
                '',
                '',
                "Multiple HEAD OF FAMILY members found ($headCount)"
            ];
            $this->stats['skipped']++;
            return ['success' => true];
        }
        
        // Check for multiple spouses (both HUSBAND and WIFE, or multiple of same type)
        if ($husbandCount > 0 && $wifeCount > 0) {
            $this->stdout("  ⊘ SKIPPED: Both HUSBAND and WIFE entries found\n", Console::FG_GREY);
            $this->errorLog[] = [
                'Conflicting Relation Data',
                $personIdPrefix,
                '',
                '',
                '',
                "Cannot have both HUSBAND($husbandCount) and WIFE($wifeCount) in same family. Use only one spouse type."
            ];
            $this->stats['skipped']++;
            return ['success' => true];
        }
        
        if ($husbandCount > 1) {
            $this->stdout("  ⊘ SKIPPED: Multiple HUSBAND entries found\n", Console::FG_GREY);
            $this->errorLog[] = [
                'Invalid Family Structure',
                $personIdPrefix,
                '',
                '',
                '',
                "Multiple HUSBAND entries found ($husbandCount)"
            ];
            $this->stats['skipped']++;
            return ['success' => true];
        }
        
        if ($wifeCount > 1) {
            $this->stdout("  ⊘ SKIPPED: Multiple WIFE entries found\n", Console::FG_GREY);
            $this->errorLog[] = [
                'Invalid Family Structure',
                $personIdPrefix,
                '',
                '',
                '',
                "Multiple WIFE entries found ($wifeCount)"
            ];
            $this->stats['skipped']++;
            return ['success' => true];
        }
        
        // Validate head of family
        if (!$headOfFamily) {
            $this->stdout("  ⊘ SKIPPED: No head of family found\n", Console::FG_GREY);
            $this->errorLog[] = [
                'Missing Head of Family',
                $personIdPrefix,
                '',
                '',
                '',
                'No HEAD OF FAMILY found in the family'
            ];
            $this->stats['skipped']++;
            return ['success' => true];
        }
        
        // Validate spouse marriage date
        if ($spouse) {
            // Check if both have marriage dates
            $headDOM = trim($headOfFamily['DOM']);
            $spouseDOM = trim($spouse['DOM']);
            
            if (empty($headDOM) && empty($spouseDOM)) {
                // Both missing marriage date
                $this->stdout("  ⚠ ", Console::FG_YELLOW, Console::BOLD);
                $this->stdout("WARNING: Both head and spouse missing marriage date\n");
            } elseif (empty($headDOM) || empty($spouseDOM)) {
                // One missing marriage date
                $this->stdout("  ⊘ SKIPPED: Incomplete marriage data\n", Console::FG_GREY);
                $this->errorLog[] = [
                    'Incomplete Marriage Data',
                    $personIdPrefix,
                    $spouse['PersonID'],
                    $spouse['Name'],
                    $spouse['Relation'],
                    "Head DOM: " . ($headDOM ?: 'missing') . ", Spouse DOM: " . ($spouseDOM ?: 'missing')
                ];
                $this->stats['skipped']++;
                return ['success' => true];
            } elseif ($headDOM !== $spouseDOM) {
                // Marriage dates don't match
                $this->stdout("  ⊘ SKIPPED: Marriage dates don't match\n", Console::FG_GREY);
                $this->errorLog[] = [
                    'Marriage Date Mismatch',
                    $personIdPrefix,
                    $spouse['PersonID'],
                    $spouse['Name'],
                    $spouse['Relation'],
                    "Head DOM: {$headDOM}, Spouse DOM: {$spouseDOM} - dates must match exactly"
                ];
                $this->stats['skipped']++;
                return ['success' => true];
            }
        }
        
        // Skip deceased HEAD OF FAMILY
        /* if ($headOfFamily['DeathStatus'] === 'Yes') {
            $this->stdout("  ⊘ SKIPPED: HEAD OF FAMILY is deceased\n", Console::FG_GREY);
            $this->errorLog[] = [
                'Deceased Member',
                $personIdPrefix,
                $headOfFamily['PersonID'],
                $headOfFamily['Name'],
                $headOfFamily['Relation'],
                'HEAD OF FAMILY is deceased'
            ];
            $this->stats['skipped']++;
            return ['success' => true];
        } */
        
        // Skip deceased spouse
        /* if ($spouse && $spouse['DeathStatus'] === 'Yes') {
            $this->stdout("  ⊘ SKIPPED: Spouse is deceased\n", Console::FG_GREY);
            $this->errorLog[] = [
                'Deceased Spouse',
                $personIdPrefix,
                $spouse['PersonID'],
                $spouse['Name'],
                $spouse['Relation'],
                'Spouse is deceased'
            ];
            $this->stats['skipped']++;
            return ['success' => true];
        } */
        
        // Extract member ID
        $memberId = $this->getMemberIdFromPersonId($headOfFamily['PersonID']);
        
        // Check if exists
        $existingMember = ExtendedMember::findOne(['memberno' => $memberId, 'institutionid' => $this->institutionId]);
        if ($existingMember) {
            $this->stdout("  ⊘ SKIPPED: Member already exists with ID: $memberId\n", Console::FG_GREY);
            $this->errorLog[] = [
                'Duplicate Member',
                $personIdPrefix,
                $headOfFamily['PersonID'],
                $headOfFamily['Name'],
                $headOfFamily['Relation'],
                "Member ID $memberId already exists"
            ];
            $this->stats['skipped']++;
            return ['success' => true];
        }
        
        // Dry run mode - collect data without database transaction
        if ($this->dryRun) {
            try {
                $familyPreview = $this->collectFamilyData($personIdPrefix, $headOfFamily, $spouse, $dependants, $memberId, $members);
                $this->previewData[] = $familyPreview;
                
                $this->stdout("  ✓ Collected data for: ", Console::FG_CYAN);
                $this->stdout("{$headOfFamily['Name']} (ID: $memberId)\n");
                $this->stats['members_created']++;
                
                if ($spouse) {
                    $this->stdout("  ✓ Spouse data collected: ", Console::FG_CYAN);
                    $this->stdout("{$spouse['Name']}\n");
                    $this->stats['spouses_added']++;
                }
                
                foreach ($dependants as $dependantData) {
                    // Skip deceased dependants in preview too
                    /* if ($dependantData['DeathStatus'] === 'Yes') {
                        $this->stdout("  ⊘ SKIPPED Dependant (Deceased): ", Console::FG_GREY);
                        $this->stdout("{$dependantData['Name']}\n");
                        continue;
                    } */
                    
                    $this->stdout("  ✓ Dependant data collected: ", Console::FG_CYAN);
                    $this->stdout("{$dependantData['Name']}");
                    
                    // Show status indicators
                    $statusInfo = [];
                    if ($dependantData['Active'] !== 'Y') {
                        $statusInfo[] = 'Inactive';
                    }
                    if (!empty($statusInfo)) {
                        $this->stdout(" [" . implode(', ', $statusInfo) . "]", Console::FG_YELLOW);
                    }
                    $this->stdout("\n");
                    
                    $this->stats['dependants_created']++;
                }
                
                return ['success' => true];
                
            } catch (\Throwable $e) {
                $this->errorLog[] = [
                    'Data Collection Error',
                    $personIdPrefix,
                    $headOfFamily['PersonID'] ?? '',
                    $headOfFamily['Name'] ?? '',
                    $headOfFamily['Relation'] ?? '',
                    $e->getMessage()
                ];
                $this->stats['errors']++;
                return ['success' => false, 'error' => $e->getMessage()];
            }
        }
        
        // Live mode - insert to database with transaction
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // Create user credentials for primary member only if phone or email exists
            $memberUserCredentialId = null;
            if (!empty($headOfFamily['Phone'])/*  || !empty($headOfFamily['Email']) */) {
                $memberUserCredentialId = $this->createUserCredentials(
                    $headOfFamily['Email'],
                    $headOfFamily['Phone'],
                    'M'
                );
            }
            
            // Create primary member
            $member = $this->createMember($headOfFamily, $spouse, $memberId);
            
            if (!$member) {
                throw new \Exception("Failed to create member");
            }
            
            $this->stdout("  ✓ Created member: ", Console::FG_GREEN);
            $this->stdout("{$headOfFamily['Name']} (ID: $memberId)");
            
            // Show status indicators for head of family
            if ($headOfFamily['Active'] !== 'Y') {
                $this->stdout(" [Inactive]", Console::FG_YELLOW);
            }
            $this->stdout("\n");
            
            $this->stats['members_created']++;
            
            // Link user credential to member only if credentials were created
            if ($memberUserCredentialId) {
                $this->createUserMember($memberUserCredentialId, $member->memberid, 'M');
            }
            
            if ($spouse) {
                $this->stdout("  ✓ Added spouse: ", Console::FG_GREEN);
                $this->stdout("{$spouse['Name']}");
                
                // Show status indicators for spouse
                if ($spouse['Active'] !== 'Y') {
                    $this->stdout(" [Inactive]", Console::FG_YELLOW);
                }
                $this->stdout("\n");
                
                $this->stats['spouses_added']++;
                
                // Create user credentials for spouse only if they have phone or email
                if (!empty($spouse['Phone'])/*  || !empty($spouse['Email']) */) {
                    $spouseUserCredentialId = $this->createUserCredentials(
                        $spouse['Email'],
                        $spouse['Phone'],
                        'S'
                    );
                    if ($spouseUserCredentialId) {
                        $this->createUserMember($spouseUserCredentialId, $member->memberid, 'S');
                    }
                }
            }
            
            // Process dependants
            foreach ($dependants as $dependantData) {
                // Skip deceased dependants
                /* if ($dependantData['DeathStatus'] === 'Yes') {
                    $this->stdout("  ⊘ SKIPPED Dependant (Deceased): ", Console::FG_GREY);
                    $this->stdout("{$dependantData['Name']}\n");
                    continue;
                } */
                
                $dependant = $this->createDependant($member->memberid, $dependantData, $members);
                
                if ($dependant) {
                    $this->stdout("  ✓ Added dependant: ", Console::FG_GREEN);
                    $this->stdout("{$dependantData['Name']}");
                    
                    // Show status indicators
                    $statusInfo = [];
                    if ($dependantData['Active'] !== 'Y') {
                        $statusInfo[] = 'Inactive';
                    }
                    if (!empty($statusInfo)) {
                        $this->stdout(" [" . implode(', ', $statusInfo) . "]", Console::FG_YELLOW);
                    }
                    $this->stdout("\n");
                    
                    $this->stats['dependants_created']++;
                }
            }
            
            $transaction->commit();
            return ['success' => true];
            
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->errorLog[] = [
                'Processing Error',
                $personIdPrefix,
                $headOfFamily['PersonID'] ?? '',
                $headOfFamily['Name'] ?? '',
                $headOfFamily['Relation'] ?? '',
                $e->getMessage()
            ];
            $this->stats['errors']++;
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Save progress to file
     */
    private function saveProgress($familyId)
    {
        $progress = [
            'lastProcessedFamily' => $familyId,
            'stats' => $this->stats,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        file_put_contents($this->progressFile, json_encode($progress, JSON_PRETTY_PRINT));
    }
    
    /**
     * Collect family data for preview (dry run mode)
     * @param string $personIdPrefix The PersonID prefix (e.g., A-04)
     */
    private function collectFamilyData($personIdPrefix, $headOfFamily, $spouse, $dependants, $memberId, $allMembers)
    {
        $phone = $this->parsePhoneNumber($headOfFamily['Phone']);
        
        $familyData = [
            'family_id' => $personIdPrefix,
            'original_family_id' => $headOfFamily['FamilyID'],
            'member' => [
                'member_no' => $memberId,
                'person_id' => $headOfFamily['PersonID'],
                'person_id_prefix' => $personIdPrefix,
                'membership_type' => 'Regular',
                'title' => $headOfFamily['Prefix'],
                'title_id' => $this->getTitleId($headOfFamily['Prefix']),
                'first_name' => $headOfFamily['Name'],
                'nickname' => $headOfFamily['Nickname'],
                'sex' => $headOfFamily['Sex'] === 'Male' ? 'm' : 'f',
                'dob' => $this->parseDate($headOfFamily['DOB']),
                'occupation' => $headOfFamily['Occupation'],
                'mobile_country_code' => $phone['code'],
                'mobile' => $phone['number'],
                'email' => $headOfFamily['Email'],
                'member_since' => date('Y-m-d'),
                'confirmed' => $headOfFamily['Confirmed'],
                'confirmed_value' => $headOfFamily['Confirmed'] === 'Yes' ? 1 : 0,
                'active' => $headOfFamily['Active'],
                'active_value' => $headOfFamily['Active'] === 'Y' ? 1 : 0,
                'death_status' => $headOfFamily['DeathStatus'],
                'marital_status' => $headOfFamily['MaritalStatus']
            ],
            'user_credentials' => [
                'email' => $headOfFamily['Email'],
                'mobile' => $phone['code'] . $phone['number'],
                'user_type' => 'M',
                'password_hash' => '[HASHED: ' . $this->defaultPassword . ']'
            ]
        ];
        
        // Add spouse data
        if ($spouse) {
            $spousePhone = $this->parsePhoneNumber($spouse['Phone']);
            $familyData['spouse'] = [
                'person_id' => $spouse['PersonID'],
                'title' => $spouse['Prefix'],
                'title_id' => $this->getTitleId($spouse['Prefix']),
                'name' => $spouse['Name'],
                'nickname' => $spouse['Nickname'],
                'dob' => $this->parseDate($spouse['DOB']),
                'dom' => $this->parseDate($spouse['DOM']),
                'occupation' => $spouse['Occupation'],
                'mobile_country_code' => $spousePhone['code'],
                'mobile' => $spousePhone['number'],
                'email' => $spouse['Email'],
                'relation' => $spouse['Relation'],
                'active_spouse' => $spouse['Active'],
                'active_spouse_value' => $spouse['Active'] === 'Y' ? 1 : 0,
                'confirmed' => $spouse['Confirmed'],
                'confirmed_spouse' => $spouse['Confirmed'] === 'Yes' ? 1 : 0,
                'death_status' => $spouse['DeathStatus']
            ];
            
            if (!empty($spouse['Phone']) || !empty($spouse['Email'])) {
                $familyData['spouse_user_credentials'] = [
                    'email' => $spouse['Email'],
                    'mobile' => $spousePhone['code'] . $spousePhone['number'],
                    'user_type' => 'S',
                    'password_hash' => '[HASHED: ' . $this->defaultPassword . ']'
                ];
            }
        }
        
        // Add dependants - skip deceased dependants
        $familyData['dependants'] = [];
        
        foreach ($dependants as $dependantData) {
            // Skip deceased dependants
            /* if ($dependantData['DeathStatus'] === 'Yes') {
                continue;
            } */
            
            $relationMap = [
                'SON' => 'Son',
                'DAUGHTER' => 'Daughter',
                'FATHER' => 'Father',
                'MOTHER' => 'Mother',
                'BROTHER' => 'Brother',
                'SISTER' => 'Sister',
                'GRAND SON' => 'Grand son',
                'GRAND DAUGHTER' => 'Grand daughter',
                'GRAND FATHER' => 'Grand father',
                'GRAND MOTHER' => 'Grand mother'
            ];
            
            $relation = $relationMap[$dependantData['Relation']] ?? $dependantData['Relation'];
            $dependantPhone = $this->parsePhoneNumber($dependantData['Phone']);
            
            $isMarried = 1;
            if (in_array($dependantData['MaritalStatus'], ['Married', 'Widow'])) {
                $isMarried = 2;
            }
            
            $dependantInfo = [
                'person_id' => $dependantData['PersonID'],
                'title' => $dependantData['Prefix'],
                'title_id' => $this->getTitleId($dependantData['Prefix']),
                'name' => $dependantData['Name'],
                'relation' => $relation,
                'dob' => $this->parseDate($dependantData['DOB']),
                'occupation' => $dependantData['Occupation'],
                'mobile_country_code' => $dependantPhone['code'],
                'mobile' => $dependantPhone['number'],
                'marital_status' => $dependantData['MaritalStatus'],
                'is_married' => $isMarried,
                'active' => $dependantData['Active'],
                'active_value' => $dependantData['Active'] === 'Y' ? 1 : 0,
                'confirmed' => $dependantData['Confirmed'],
                'confirmed_value' => $dependantData['Confirmed'] === 'Yes' ? 1 : 0,
                'death_status' => $dependantData['DeathStatus']
            ];
            
            // Check for dependant spouse
            if ($isMarried === 2 && !empty($dependantData['DOM'])) {
                $marriageDate = $dependantData['DOM'];
                foreach ($allMembers as $potentialSpouse) {
                    if ($potentialSpouse['DOM'] === $marriageDate && 
                        $potentialSpouse['PersonID'] !== $dependantData['PersonID']) {
                        
                        if (strpos($potentialSpouse['Relation'], 'IN LAW') !== false ||
                            strpos($potentialSpouse['Relation'], 'WIFE') !== false ||
                            strpos($potentialSpouse['Relation'], 'HUSBAND') !== false) {
                            
                            $spousePhone = $this->parsePhoneNumber($potentialSpouse['Phone']);
                            $dependantInfo['spouse'] = [
                                'person_id' => $potentialSpouse['PersonID'],
                                'title' => $potentialSpouse['Prefix'],
                                'title_id' => $this->getTitleId($potentialSpouse['Prefix']),
                                'name' => $potentialSpouse['Name'],
                                'dob' => $this->parseDate($potentialSpouse['DOB']),
                                'mobile_country_code' => $spousePhone['code'],
                                'mobile' => $spousePhone['number'],
                                'wedding_anniversary' => $this->parseDate($marriageDate),
                                'active' => $potentialSpouse['Active'],
                                'death_status' => $potentialSpouse['DeathStatus']
                            ];
                            break;
                        }
                    }
                }
            }
            
            $familyData['dependants'][] = $dependantInfo;
        }
        
        return $familyData;
    }
    
    /**
     * Save preview data to JSON file
     */
    private function savePreviewData()
    {
        $previewFile = Yii::getAlias('@app') . '/service/institution/ImportPreview_' . $this->institutionId . '_' . date('YmdHis') . '.json';
        
        $previewOutput = [
            'generated_at' => date('Y-m-d H:i:s'),
            'institution_id' => $this->institutionId,
            'csv_file' => $this->csvFile,
            'total_families' => count($this->previewData),
            'statistics' => $this->stats,
            'families' => $this->previewData
        ];
        
        file_put_contents($previewFile, json_encode($previewOutput, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->stdout("\n");
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("Preview Data Saved!\n", Console::FG_CYAN, Console::BOLD);
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("File: ", Console::FG_YELLOW);
        $this->stdout("$previewFile\n", Console::FG_GREEN);
        $this->stdout("\nYou can review this JSON file to verify the data before running the actual import.\n");
        $this->stdout("To perform actual import, run the command without --dry-run option.\n\n");
    }
    
    /**
     * Create user credentials
     */
    private function createUserCredentials($email, $phone, $userType)
    {
        // Skip if both phone and email are empty
        if (empty($phone) && empty($email)) {
            return null;
        }
        
        $phone = $this->parsePhoneNumber($phone);
        $mobileNo = $phone['code'] . $phone['number'];
        
        // Skip if parsed phone is empty and email is empty
        if (empty($mobileNo) && empty($email)) {
            return null;
        }
        
        // Check if already exists
        $userCredentialModel = new ExtendedUserCredentials();
        $existing = $userCredentialModel->memberCredentialExist($mobileNo, $email);
        
        if (!empty($existing)) {
            return $existing['id'];
        }
        
        // Create new
        $userCredential = new ExtendedUserCredentials();
        $userCredential->institutionid = $this->institutionId;
        $userCredential->emailid = $email;
        $userCredential->password = Yii::$app->getSecurity()->generatePasswordHash($this->defaultPassword);
        $userCredential->initiallogin = false;
        $userCredential->usertype = $userType;
        $userCredential->mobileno = $mobileNo;
        $userCredential->created_at = date('Y-m-d H:i:s');
        $userCredential->generateAuthKey();
        
        if ($userCredential->save(false)) {
            $this->stats['user_credentials_created']++;
            return $userCredential->id;
        }
        
        return null;
    }
    
    /**
     * Create user member link
     */
    private function createUserMember($userCredentialId, $memberId, $userType)
    {
        $userMemberModel = new ExtendedUserMember();
        
        // Check if already exists
        $existing = $userMemberModel->userMemberExist($userCredentialId, $memberId, $this->institutionId, $userType);
        if (!empty($existing)) {
            return true;
        }
        
        $userMember = new ExtendedUserMember();
        $userMember->userid = $userCredentialId;
        $userMember->memberid = $memberId;
        $userMember->institutionid = $this->institutionId;
        $userMember->usertype = $userType;
        
        return $userMember->save(false);
    }
    
    /**
     * Create member record
     */
    private function createMember($headOfFamily, $spouse, $memberId)
    {
        $member = new ExtendedMember();
        $member->institutionid = $this->institutionId;
        $member->memberno = $memberId;
        $member->membershiptype = 'Regular';
        $member->firstName = $headOfFamily['Name'];
        $member->middleName = '';
        $member->lastName = '';
        $member->membernickname = $headOfFamily['Nickname'];
        $member->member_dob = $this->parseDate($headOfFamily['DOB']);
        // $member->membersince = date('Y-m-d');
        $member->occupation = $headOfFamily['Occupation'];
        
        $phone = $this->parsePhoneNumber($headOfFamily['Phone']);
        $member->member_mobile1_countrycode = $phone['code'];
        $member->member_mobile1 = $phone['number'];
        
        $member->member_email = $headOfFamily['Email'];
        
        // Set new fields for member - store actual status from CSV
        $member->active = $headOfFamily['Active'] === 'Y' ? 1 : 0;
        $member->confirmed = $headOfFamily['Confirmed'] === 'Yes' ? 1 : 0;
        
        $titleId = $this->getTitleId($headOfFamily['Prefix']);
        if ($titleId) {
            $member->membertitle = $titleId;
        }
        
        // Add spouse info
        if ($spouse) {
            $member->spouse_firstName = $spouse['Name'];
            $member->spousenickname = $spouse['Nickname'];
            $member->spouse_dob = $this->parseDate($spouse['DOB']);
            $member->dom = $this->parseDate($spouse['DOM']);
            $member->spouseoccupation = $spouse['Occupation'];
            
            // Set spouse active and confirmed status from CSV
            $member->active_spouse = $spouse['Active'] === 'Y' ? 1 : 0;
            $member->confirmed_spouse = $spouse['Confirmed'] === 'Yes' ? 1 : 0;
            
            $spousePhone = $this->parsePhoneNumber($spouse['Phone']);
            $member->spouse_mobile1_countrycode = $spousePhone['code'];
            $member->spouse_mobile1 = $spousePhone['number'];
            
            $member->spouse_email = $spouse['Email'];
            
            $spouseTitleId = $this->getTitleId($spouse['Prefix']);
            if ($spouseTitleId) {
                $member->spousetitle = $spouseTitleId;
            }
        }
        
        if ($member->save(false)) {
            return $member;
        }
        
        return null;
    }
    
    /**
     * Create dependant record
     */
    private function createDependant($memberId, $dependantData, $allMembers)
    {
        $relationMap = [
            'SON' => 'Son',
            'DAUGHTER' => 'Daughter',
            'FATHER' => 'Father',
            'MOTHER' => 'Mother',
            'BROTHER' => 'Brother',
            'SISTER' => 'Sister',
            'GRAND SON' => 'Grand son',
            'GRAND DAUGHTER' => 'Grand daughter',
            'GRAND FATHER' => 'Grand father',
            'GRAND MOTHER' => 'Grand mother'
        ];
        
        $relation = $relationMap[$dependantData['Relation']] ?? $dependantData['Relation'];
        
        $dependant = new ExtendedDependant();
        $dependant->memberid = $memberId;
        $dependant->dependantname = $dependantData['Name'];
        $dependant->relation = $relation;
        $dependant->dob = $this->parseDate($dependantData['DOB']);
        $dependant->occupation = $dependantData['Occupation'];
        
        $phone = $this->parsePhoneNumber($dependantData['Phone']);
        $dependant->dependantmobilecountrycode = $phone['code'];
        $dependant->dependantmobile = $phone['number'];
        
        $titleId = $this->getTitleId($dependantData['Prefix']);
        if ($titleId) {
            $dependant->titleid = $titleId;
        }
        
        // Set new fields for dependant - store actual status from CSV
        $dependant->active = $dependantData['Active'] === 'Y' ? 1 : 0;
        $dependant->confirmed = $dependantData['Confirmed'] === 'Yes' ? 1 : 0;
        
        $isMarried = 1; // Single
        if (in_array($dependantData['MaritalStatus'], ['Married', 'Widow'])) {
            $isMarried = 2;
        }
        $dependant->ismarried = $isMarried;
        
        if ($dependant->save()) {
            // Check for dependant spouse
            if ($isMarried === 2 && !empty($dependantData['DOM'])) {
                $this->createDependantSpouse($memberId, $dependant->id, $dependantData, $allMembers);
            }
            return $dependant;
        }
        
        return null;
    }
    
    /**
     * Create dependant spouse
     */
    private function createDependantSpouse($memberId, $dependantId, $dependantData, $allMembers)
    {
        $marriageDate = $dependantData['DOM'];
        
        // Find spouse in family
        foreach ($allMembers as $potentialSpouse) {
            if ($potentialSpouse['DOM'] === $marriageDate && 
                $potentialSpouse['PersonID'] !== $dependantData['PersonID']) {
                
                if (strpos($potentialSpouse['Relation'], 'IN LAW') !== false ||
                    strpos($potentialSpouse['Relation'], 'WIFE') !== false ||
                    strpos($potentialSpouse['Relation'], 'HUSBAND') !== false) {
                    
                    $weddingDate = $this->parseDate($marriageDate);
                    $spouseDob = $this->parseDate($potentialSpouse['DOB']);
                    $phone = $this->parsePhoneNumber($potentialSpouse['Phone']);
                    $titleId = $this->getTitleId($potentialSpouse['Prefix']);
                    
                    $sql = 'INSERT INTO dependant 
                            (memberid, titleid, dependantname, dependantmobilecountrycode, dependantmobile, 
                             dob, dependantid, weddinganniversary) 
                            VALUES (:memberid, :titleid, :dependantname, :mobilecode, :mobile, 
                                    :dob, :dependantid, :weddingdate)';
                    
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(':memberid', $memberId)
                        ->bindValue(':titleid', $titleId)
                        ->bindValue(':dependantname', $potentialSpouse['Name'])
                        ->bindValue(':mobilecode', $phone['code'])
                        ->bindValue(':mobile', $phone['number'])
                        ->bindValue(':dob', $spouseDob)
                        ->bindValue(':dependantid', $dependantId)
                        ->bindValue(':weddingdate', $weddingDate)
                        ->execute();
                    
                    $this->stdout("    ✓ Added dependant spouse: ", Console::FG_GREEN);
                    $this->stdout("{$potentialSpouse['Name']}\n");
                    break;
                }
            }
        }
    }
    
    /**
     * Helper functions
     */
    private function getMemberIdFromPersonId($personId)
    {
        return preg_replace('/-\d+$/', '', $personId);
    }
    
    private function parseDate($dateStr)
    {
        if (empty($dateStr)) return null;
        
        try {
            $formats = ['d-m-Y', 'd/m/Y', 'Y-m-d', 'm/d/Y'];
            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateStr);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }
            
            $timestamp = strtotime($dateStr);
            if ($timestamp !== false) {
                return date('Y-m-d', $timestamp);
            }
        } catch (\Exception $e) {
            return null;
        }
        
        return null;
    }
    
    private function parsePhoneNumber($phone)
    {
        if (empty($phone)) return ['code' => '', 'number' => ''];
        
        // Convert to string and remove any decimals
        $phone = sprintf("%.0f", $phone);
        
        // Remove any non-digit characters
        $phone = preg_replace('/\D/', '', $phone);
        
        if (strlen($phone) >= 10) {
            if (strlen($phone) > 10) {
                // Extract country code (max 3 digits)
                $extraDigits = strlen($phone) - 10;
                
                // Limit country code to max 3 digits
                if ($extraDigits > 3) {
                    // Phone number is malformed, try to salvage it
                    // Take last 10 digits as the number and use default country code
                    $code = '+91';
                    $number = substr($phone, -10);
                } else {
                    $code = '+' . substr($phone, 0, $extraDigits);
                    $number = substr($phone, $extraDigits);
                }
            } else {
                $code = '+91';
                $number = $phone;
            }
            return ['code' => $code, 'number' => $number];
        }
        
        return ['code' => '', 'number' => $phone];
    }
    
    private function getTitleMap()
    {
        $titleModel = new ExtendedTitle();
        $titles = $titleModel->getActiveTitles($this->institutionId);
        $titleMap = [];
        foreach ($titles as $title) {
            // Clean the title description: remove dots, trim, then lowercase for key
            $cleanDescription = trim($title['Description'], '. ');
            $titleMap[strtolower($cleanDescription)] = $title['TitleId'];
        }
        return $titleMap;
    }
    
    private function initializeTitleMap()
    {
        $this->titleMap = $this->getTitleMap();
    }
    
    private function getTitleId($prefix)
    {
        if (empty($prefix)) return null;
        
        // Clean and normalize prefix (remove dots, trim, lowercase for comparison)
        $cleanPrefix = trim($prefix, '. ');
        $normalizedPrefix = strtolower($cleanPrefix);
        
        // Check if title exists (case-insensitive)
        if (isset($this->titleMap[$normalizedPrefix])) {
            return $this->titleMap[$normalizedPrefix];
        }
        
        // Title doesn't exist, create it
        $newTitle = new ExtendedTitle();
        $newTitle->institutionid = $this->institutionId;
        $newTitle->Description = $prefix; // Use cleaned prefix with original case
        
        if ($newTitle->save(false)) {
            // Add to cache with lowercase key
            $this->titleMap[$normalizedPrefix] = $newTitle->TitleId;
            
            $this->stdout("    ℹ Created new title: ", Console::FG_BLUE);
            $this->stdout("{$cleanPrefix}\n");
            
            return $newTitle->TitleId;
        }
        
        return null;
    }
    
    private function saveErrorLog()
    {
        if (count($this->errorLog) > 1) {
            $errorLogPath = Yii::getAlias('@app') . '/service/institution/ImportErrors.csv';
            $errorFile = fopen($errorLogPath, 'w');
            foreach ($this->errorLog as $errorRow) {
                fputcsv($errorFile, $errorRow);
            }
            fclose($errorFile);
            
            $this->stdout("\nError log saved to: ", Console::FG_YELLOW);
            $this->stdout("$errorLogPath\n");
        }
    }
    
    private function printStatistics()
    {
        $this->stdout("\n");
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        if ($this->dryRun) {
            $this->stdout("Dry Run Complete!\n", Console::FG_CYAN, Console::BOLD);
        } else {
            $this->stdout("Import Complete!\n", Console::FG_GREEN, Console::BOLD);
        }
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        
        $this->stdout("Total rows processed:       ");
        $this->stdout($this->stats['total_rows'] . "\n", Console::FG_CYAN);
        
        if ($this->dryRun) {
            $this->stdout("Families collected:         ");
        } else {
            $this->stdout("Members created:            ");
        }
        $this->stdout($this->stats['members_created'] . "\n", Console::FG_GREEN);
        
        if ($this->dryRun) {
            $this->stdout("Spouse data collected:      ");
        } else {
            $this->stdout("Spouses added:              ");
        }
        $this->stdout($this->stats['spouses_added'] . "\n", Console::FG_GREEN);
        
        if ($this->dryRun) {
            $this->stdout("Dependant data collected:   ");
        } else {
            $this->stdout("Dependants created:         ");
        }
        $this->stdout($this->stats['dependants_created'] . "\n", Console::FG_GREEN);
        
        if (!$this->dryRun) {
            $this->stdout("User credentials created:   ");
            $this->stdout($this->stats['user_credentials_created'] . "\n", Console::FG_GREEN);
        }
        
        $this->stdout("Skipped:                    ");
        $this->stdout($this->stats['skipped'] . "\n", Console::FG_YELLOW);
        
        $this->stdout("Errors:                     ");
        $this->stdout($this->stats['errors'] . "\n", Console::FG_RED);
        
        $this->stdout(str_repeat("=", 80) . "\n", Console::BOLD);
        $this->stdout("\n");
    }
}
