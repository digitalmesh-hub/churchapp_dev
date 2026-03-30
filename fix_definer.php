<?php
// Simple fix for stored procedure definer issue
define('YII_DEBUG', true);
define('YII_ENV', 'dev');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/common/config/bootstrap.php';
require __DIR__ . '/backend/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/common/config/main.php',
    require __DIR__ . '/common/config/main-local.php',
    require __DIR__ . '/backend/config/main.php',
    require __DIR__ . '/backend/config/main-local.php'
);

try {
    $application = new yii\web\Application($config);
    
    echo "=== Fixing Stored Procedure Definer ===\n\n";
    
    $db = Yii::$app->db;
    
    // The issue is that procedures are defined with 'root'@'%' but only 'root'@'localhost' exists
    // Solution: Recreate procedures with 'root'@'localhost' as definer
    
    $procedures = [
        'Privileges_ByRole',
        'App_Privileges_ByRole',
        'get_dependants'  // Added the new problematic procedure
    ];
    
    foreach ($procedures as $procedureName) {
        echo "Fixing procedure: $procedureName\n";
        
        try {
            // Get the current procedure definition
            $showResult = $db->createCommand("SHOW CREATE PROCEDURE $procedureName")->queryOne();
            
            if ($showResult) {
                $createSQL = $showResult['Create Procedure'];
                
                // Replace the definer from root@% to root@localhost
                $fixedSQL = str_replace(
                    "DEFINER=`root`@`%`",
                    "DEFINER=`root`@`localhost`",
                    $createSQL
                );
                
                // Drop and recreate the procedure
                $db->createCommand("DROP PROCEDURE IF EXISTS $procedureName")->execute();
                echo "  ✓ Dropped existing procedure\n";
                
                $db->createCommand($fixedSQL)->execute();
                echo "  ✓ Recreated procedure with correct definer\n";
                
                // Test the procedure with appropriate parameters
                echo "  Testing procedure...\n";
                if ($procedureName === 'get_dependants') {
                    // get_dependants takes only one parameter (memberId)
                    $testResult = $db->createCommand("CALL $procedureName(1)")->queryAll();
                } else {
                    // Other procedures take roleId and institutionId
                    $testResult = $db->createCommand("CALL $procedureName('test', 1)")->queryAll();
                }
                echo "  ✓ Procedure test successful\n";
                
            } else {
                echo "  ❌ Could not get procedure definition\n";
            }
            
        } catch (Exception $e) {
            echo "  ❌ Error fixing $procedureName: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    echo "=== Fix Complete ===\n";
    echo "Stored procedures should now work correctly.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}