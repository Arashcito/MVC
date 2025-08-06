<?php
// Test file for Secondary Family functionality

require_once 'config.php';
require_once 'secondary_family_handler.php';

echo "<h1>Testing Secondary Family Functions</h1>\n";

try {
    $pdo = getDBConnection();
    echo "<p>✅ Database connection successful</p>\n";
    
    // Test 1: Get all secondary family info
    echo "<h2>Test 1: Get All Secondary Family Info</h2>\n";
    $allFamilies = getAllSecondaryFamilyInfo($pdo);
    echo "<p>Result: " . ($allFamilies['success'] ? 'Success' : 'Failed') . "</p>\n";
    echo "<p>Count: " . $allFamilies['count'] . "</p>\n";
    if (!$allFamilies['success']) {
        echo "<p>Error: " . htmlspecialchars($allFamilies['message']) . "</p>\n";
    } else {
        echo "<p>Sample data:</p>\n";
        echo "<pre>" . print_r(array_slice($allFamilies['data'], 0, 2), true) . "</pre>\n";
    }
    
    // Test 2: Search for a family member
    echo "<h2>Test 2: Search Family Members</h2>\n";
    $searchResults = searchFamilyMember($pdo, 'John', 'Doe');
    echo "<p>Search Result: " . ($searchResults['success'] ? 'Success' : 'Failed') . "</p>\n";
    echo "<p>Count: " . $searchResults['count'] . "</p>\n";
    if (!$searchResults['success']) {
        echo "<p>Error: " . htmlspecialchars($searchResults['message']) . "</p>\n";
    } else {
        echo "<p>Search data:</p>\n";
        echo "<pre>" . print_r($searchResults['data'], true) . "</pre>\n";
        
        // Test 3: Get family info for first result
        if ($searchResults['count'] > 0) {
            $familyMemID = $searchResults['data'][0]['familyMemID'];
            echo "<h2>Test 3: Get Specific Family Info (ID: $familyMemID)</h2>\n";
            $familyInfo = getSecondaryFamilyInfo($pdo, $familyMemID);
            echo "<p>Family Info Result: " . ($familyInfo['success'] ? 'Success' : 'Failed') . "</p>\n";
            if (!$familyInfo['success']) {
                echo "<p>Error: " . htmlspecialchars($familyInfo['message']) . "</p>\n";
            } else {
                echo "<p>Club Member Count: " . $familyInfo['clubMemberCount'] . "</p>\n";
                echo "<p>Family info data:</p>\n";
                echo "<pre>" . print_r($familyInfo['data'], true) . "</pre>\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}
?>
