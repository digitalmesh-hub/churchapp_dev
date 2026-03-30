<?php
/**
 * One-time script to import family members from CSV
 * 
 * Logic:
 * 1. Import HEAD OF FAMILY as primary member
 * 2. Import WIFE/HUSBAND as spouse (verify by marital status)
 * 3. Use PersonID as member ID (remove suffix like -1, -2)
 * 4. Find dependant spouses by marriage date
 * 5. Group and create records accordingly
 * 6. Validate marriage dates match for couples
 * 7. Log issues to separate CSV file
 */

require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/common/config/bootstrap.php');
require(__DIR__ . '/backend/config/bootstrap.php');

use common\models\extendedmodels\ExtendedMember;
use common\models\extendedmodels\ExtendedDependant;
use common\models\extendedmodels\ExtendedTitle;

// Configuration
$csvFilePath = __DIR__ . '/service/institution/FamilyMembers.csv';
$errorLogPath = __DIR__ . '/service/institution/ImportErrors.csv';
$institutionId = 1; // Set your institution ID

// Initialize error log
$errorLog = [];
$errorLog[] = ['Issue', 'FamilyID', 'PersonID', 'Name', 'Relation', 'Details'];

// Statistics
$stats = [
    'total_rows' => 0,
    'members_created' => 0,
    'spouses_added' => 0,
    'dependants_created' => 0,
    'errors' => 0,
    'skipped' => 0
];

echo "Starting Family Members Import...\n";
echo str_repeat("=", 80) . "\n";

// Read and parse CSV
if (!file_exists($csvFilePath)) {
    die("Error: CSV file not found at $csvFilePath\n");
}

$csvData = array_map('str_getcsv', file($csvFilePath));
$headers = array_shift($csvData); // Remove header row

// Group by FamilyID
$families = [];
foreach ($csvData as $row) {
    if (count($row) < 16) continue; // Skip incomplete rows
    
    $familyId = trim($row[0]);
    if (empty($familyId)) continue;
    
    if (!isset($families[$familyId])) {
        $families[$familyId] = [];
    }
    
    $families[$familyId][] = [
        'FamilyID' => trim($row[0]),
        'PersonID' => trim($row[1]),
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
    
    $stats['total_rows']++;
}

echo "Found " . count($families) . " families to process\n\n";

// Get all titles
$titleModel = new ExtendedTitle();
$titles = $titleModel->getActiveTitles($institutionId);
$titleMap = [];
foreach ($titles as $title) {
    $titleMap[strtolower($title->Description)] = $title->TitleId;
}

// Helper function to extract member ID from person ID
function getMemberIdFromPersonId($personId) {
    // Remove suffix like -1, -2, -3, etc.
    return preg_replace('/-\d+$/', '', $personId);
}

// Helper function to parse date
function parseDate($dateStr) {
    if (empty($dateStr)) return null;
    
    try {
        // Try different date formats
        $formats = ['d-m-Y', 'd/m/Y', 'Y-m-d', 'm/d/Y'];
        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $dateStr);
            if ($date !== false) {
                return $date->format('Y-m-d');
            }
        }
        
        // Try strtotime as fallback
        $timestamp = strtotime($dateStr);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }
    } catch (Exception $e) {
        return null;
    }
    
    return null;
}

// Helper function to get title ID
function getTitleId($prefix, $titleMap) {
    $prefix = strtolower(trim($prefix, '.'));
    return $titleMap[$prefix] ?? null;
}

// Helper function to parse phone number
function parsePhoneNumber($phone) {
    if (empty($phone)) return ['code' => '', 'number' => ''];
    
    // Remove scientific notation and convert to string
    $phone = sprintf("%.0f", $phone);
    
    // Assuming Indian numbers starting with country code
    if (strlen($phone) >= 10) {
        if (strlen($phone) > 10) {
            // Has country code
            $code = '+' . substr($phone, 0, strlen($phone) - 10);
            $number = substr($phone, -10);
        } else {
            $code = '+91';
            $number = $phone;
        }
        return ['code' => $code, 'number' => $number];
    }
    
    return ['code' => '', 'number' => $phone];
}

// Process each family
foreach ($families as $familyId => $members) {
    echo "Processing Family ID: $familyId\n";
    
    // Find head of family
    $headOfFamily = null;
    $spouse = null;
    $dependants = [];
    
    foreach ($members as $member) {
        $relation = $member['Relation'];
        
        if ($relation === 'HEAD OF FAMILY') {
            $headOfFamily = $member;
        } elseif (in_array($relation, ['WIFE', 'HUSBAND'])) {
            $spouse = $member;
        } else {
            $dependants[] = $member;
        }
    }
    
    // Validate head of family exists
    if (!$headOfFamily) {
        echo "  ⚠ WARNING: No head of family found, checking for alternatives...\n";
        
        // Look for other primary relations
        foreach ($members as $member) {
            $relation = $member['Relation'];
            if (in_array($relation, ['MOTHER', 'FATHER', 'OTHER']) && $member['Active'] === 'Y') {
                $headOfFamily = $member;
                echo "  ℹ Using {$relation} as head of family: {$member['Name']}\n";
                break;
            }
        }
        
        if (!$headOfFamily) {
            $errorLog[] = [
                'Missing Head of Family',
                $familyId,
                '',
                '',
                '',
                'No primary member found in family'
            ];
            $stats['errors']++;
            echo "  ✗ ERROR: No suitable head of family found\n\n";
            continue;
        }
    }
    
    // Validate spouse marriage date matches head of family
    if ($spouse && $headOfFamily['DOM'] !== $spouse['DOM']) {
        $errorLog[] = [
            'Marriage Date Mismatch',
            $familyId,
            $spouse['PersonID'],
            $spouse['Name'],
            $spouse['Relation'],
            "Head: {$headOfFamily['DOM']}, Spouse: {$spouse['DOM']}"
        ];
        echo "  ⚠ WARNING: Marriage dates don't match - Head: {$headOfFamily['DOM']}, Spouse: {$spouse['DOM']}\n";
        $spouse = null; // Don't import spouse with mismatched date
    }
    
    // Skip if marked as inactive or deceased
    if ($headOfFamily['Active'] !== 'Y') {
        echo "  ⊘ SKIPPED: Head of family is inactive\n\n";
        $stats['skipped']++;
        continue;
    }
    
    if ($headOfFamily['DeathStatus'] === 'Yes') {
        echo "  ⊘ SKIPPED: Head of family is deceased\n\n";
        $stats['skipped']++;
        continue;
    }
    
    // Extract member ID from person ID
    $memberId = getMemberIdFromPersonId($headOfFamily['PersonID']);
    
    // Check if member already exists
    $existingMember = ExtendedMember::findOne(['memberno' => $memberId, 'institutionid' => $institutionId]);
    if ($existingMember) {
        echo "  ⊘ SKIPPED: Member already exists with ID: $memberId\n\n";
        $stats['skipped']++;
        continue;
    }
    
    // Create primary member
    $transaction = Yii::$app->db->beginTransaction();
    
    try {
        $member = new ExtendedMember();
        $member->institutionid = $institutionId;
        $member->memberno = $memberId;
        $member->membershiptype = 'Regular'; // Default
        $member->firstName = $headOfFamily['Name'];
        $member->middleName = '';
        $member->lastName = '';
        $member->membernickname = $headOfFamily['Nickname'];
        $member->member_dob = parseDate($headOfFamily['DOB']);
        $member->memberdate = date('Y-m-d'); // Current date as member since
        $member->member_occupation = $headOfFamily['Occupation'];
        
        // Parse phone
        $phone = parsePhoneNumber($headOfFamily['Phone']);
        $member->member_mobile_country_code = $phone['code'];
        $member->member_mobile = $phone['number'];
        
        $member->member_email = $headOfFamily['Email'];
        $member->sex = $headOfFamily['Sex'] === 'Male' ? 'm' : 'f';
        
        // Get title ID
        $titleId = getTitleId($headOfFamily['Prefix'], $titleMap);
        if ($titleId) {
            $member->titleid = $titleId;
        }
        
        // Add spouse information if available
        if ($spouse) {
            $member->spouse_name = $spouse['Name'];
            $member->spousenickname = $spouse['Nickname'];
            $member->spouse_dob = parseDate($spouse['DOB']);
            $member->dom = parseDate($spouse['DOM']);
            $member->spouse_occupation = $spouse['Occupation'];
            
            $spousePhone = parsePhoneNumber($spouse['Phone']);
            $member->spouse_mobile_country_code = $spousePhone['code'];
            $member->spouse_mobile = $spousePhone['number'];
            
            $member->spouse_email = $spouse['Email'];
            
            $spouseTitleId = getTitleId($spouse['Prefix'], $titleMap);
            if ($spouseTitleId) {
                $member->spouse_titleid = $spouseTitleId;
            }
        }
        
        if (!$member->save()) {
            throw new Exception("Failed to save member: " . json_encode($member->errors));
        }
        
        echo "  ✓ Created member: {$headOfFamily['Name']} (ID: $memberId)\n";
        $stats['members_created']++;
        
        if ($spouse) {
            echo "  ✓ Added spouse: {$spouse['Name']}\n";
            $stats['spouses_added']++;
        }
        
        // Process dependants
        foreach ($dependants as $dependantData) {
            // Skip if inactive or deceased
            if ($dependantData['Active'] !== 'Y' || $dependantData['DeathStatus'] === 'Yes') {
                echo "  ⊘ Skipped dependant: {$dependantData['Name']} (inactive/deceased)\n";
                continue;
            }
            
            // Map relation to system values
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
            $dependant->memberid = $member->id;
            $dependant->dependantname = $dependantData['Name'];
            $dependant->relation = $relation;
            $dependant->dob = parseDate($dependantData['DOB']);
            
            $depPhone = parsePhoneNumber($dependantData['Phone']);
            $dependant->dependantmobilecountrycode = $depPhone['code'];
            $dependant->dependantmobile = $depPhone['number'];
            
            $depTitleId = getTitleId($dependantData['Prefix'], $titleMap);
            if ($depTitleId) {
                $dependant->titleid = $depTitleId;
            }
            
            // Determine marital status
            $isMarried = 1; // Single
            if (in_array($dependantData['MaritalStatus'], ['Married', 'Widow'])) {
                $isMarried = 2; // Married
            }
            $dependant->ismarried = $isMarried;
            
            if (!$dependant->save()) {
                throw new Exception("Failed to save dependant {$dependantData['Name']}: " . json_encode($dependant->errors));
            }
            
            echo "  ✓ Added dependant: {$dependantData['Name']} ({$relation})\n";
            $stats['dependants_created']++;
            
            // Check if dependant has a spouse
            if ($isMarried === 2 && !empty($dependantData['DOM'])) {
                // Look for spouse in the same family
                $dependantSpouseData = null;
                $marriageDate = $dependantData['DOM'];
                
                foreach ($members as $potentialSpouse) {
                    // Check if same marriage date and opposite relation pattern
                    if ($potentialSpouse['DOM'] === $marriageDate && 
                        $potentialSpouse['PersonID'] !== $dependantData['PersonID']) {
                        
                        // Check relation patterns (e.g., SON + DAUGHTER IN LAW)
                        $isSpouseMatch = false;
                        if (strpos($potentialSpouse['Relation'], 'IN LAW') !== false ||
                            strpos($potentialSpouse['Relation'], 'WIFE') !== false ||
                            strpos($potentialSpouse['Relation'], 'HUSBAND') !== false) {
                            $isSpouseMatch = true;
                        }
                        
                        if ($isSpouseMatch) {
                            $dependantSpouseData = $potentialSpouse;
                            break;
                        }
                    }
                }
                
                if ($dependantSpouseData) {
                    // Insert dependant spouse directly into dependant table
                    $weddingDate = parseDate($marriageDate);
                    $spouseDob = parseDate($dependantSpouseData['DOB']);
                    $spPhone = parsePhoneNumber($dependantSpouseData['Phone']);
                    $spTitleId = getTitleId($dependantSpouseData['Prefix'], $titleMap);
                    
                    $sql = 'INSERT INTO dependant 
                            (memberid, titleid, dependantname, dependantmobilecountrycode, dependantmobile, 
                             dob, dependantid, weddinganniversary) 
                            VALUES (:memberid, :titleid, :dependantname, :mobilecode, :mobile, 
                                    :dob, :dependantid, :weddingdate)';
                    
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(':memberid', $member->id)
                        ->bindValue(':titleid', $spTitleId)
                        ->bindValue(':dependantname', $dependantSpouseData['Name'])
                        ->bindValue(':mobilecode', $spPhone['code'])
                        ->bindValue(':mobile', $spPhone['number'])
                        ->bindValue(':dob', $spouseDob)
                        ->bindValue(':dependantid', $dependant->id)
                        ->bindValue(':weddingdate', $weddingDate)
                        ->execute();
                    
                    echo "    ✓ Added dependant spouse: {$dependantSpouseData['Name']}\n";
                }
            }
        }
        
        $transaction->commit();
        echo "  ✓ Family processed successfully\n\n";
        
    } catch (Exception $e) {
        $transaction->rollBack();
        $errorLog[] = [
            'Processing Error',
            $familyId,
            $headOfFamily['PersonID'],
            $headOfFamily['Name'],
            $headOfFamily['Relation'],
            $e->getMessage()
        ];
        echo "  ✗ ERROR: " . $e->getMessage() . "\n\n";
        $stats['errors']++;
    }
}

// Write error log to CSV
$errorFile = fopen($errorLogPath, 'w');
foreach ($errorLog as $errorRow) {
    fputcsv($errorFile, $errorRow);
}
fclose($errorFile);

// Print statistics
echo "\n" . str_repeat("=", 80) . "\n";
echo "Import Complete!\n";
echo str_repeat("=", 80) . "\n";
echo "Total rows processed:    {$stats['total_rows']}\n";
echo "Members created:         {$stats['members_created']}\n";
echo "Spouses added:           {$stats['spouses_added']}\n";
echo "Dependants created:      {$stats['dependants_created']}\n";
echo "Skipped:                 {$stats['skipped']}\n";
echo "Errors:                  {$stats['errors']}\n";
echo "\nError log saved to: $errorLogPath\n";
echo str_repeat("=", 80) . "\n";
