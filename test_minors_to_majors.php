<?php
// Test file for Minors to Majors functionality

require_once 'config.php';
require_once 'minors_to_majors_handler.php';

echo "<h1>Testing Minors to Majors Functions</h1>\n";

try {
    $pdo = getDBConnection();
    echo "<p>✅ Database connection successful</p>\n";
    
    // Test 1: Get main report
    echo "<h2>Test 1: Get Minors to Majors Report</h2>\n";
    $reportResult = getMinorsToMajorsReport($pdo);
    echo "<p>Result: " . ($reportResult['success'] ? 'Success' : 'Failed') . "</p>\n";
    if (!$reportResult['success']) {
        echo "<p>Error: " . htmlspecialchars($reportResult['message']) . "</p>\n";
    } else {
        echo "<p>Total Members: " . $reportResult['totalMembers'] . "</p>\n";
        echo "<p>Location Stats:</p>\n";
        echo "<pre>" . print_r($reportResult['locationStats'], true) . "</pre>\n";
        echo "<p>Age Stats:</p>\n";
        echo "<pre>" . print_r($reportResult['ageStats'], true) . "</pre>\n";
        
        if ($reportResult['totalMembers'] > 0) {
            echo "<p>Sample member data:</p>\n";
            echo "<pre>" . print_r(array_slice($reportResult['data'], 0, 2), true) . "</pre>\n";
        }
    }
    
    // Test 2: Get available locations
    echo "<h2>Test 2: Get Available Locations</h2>\n";
    $locationsResult = getAvailableLocations($pdo);
    echo "<p>Result: " . ($locationsResult['success'] ? 'Success' : 'Failed') . "</p>\n";
    if (!$locationsResult['success']) {
        echo "<p>Error: " . htmlspecialchars($locationsResult['message']) . "</p>\n";
    } else {
        echo "<p>Available Locations:</p>\n";
        echo "<pre>" . print_r($locationsResult['data'], true) . "</pre>\n";
        
        // Test 3: Filter by location (if locations exist)
        if (!empty($locationsResult['data'])) {
            $testLocation = $locationsResult['data'][0];
            echo "<h2>Test 3: Filter by Location ($testLocation)</h2>\n";
            $filteredResult = getMinorsToMajorsByLocation($pdo, $testLocation);
            echo "<p>Result: " . ($filteredResult['success'] ? 'Success' : 'Failed') . "</p>\n";
            if (!$filteredResult['success']) {
                echo "<p>Error: " . htmlspecialchars($filteredResult['message']) . "</p>\n";
            } else {
                echo "<p>Members at $testLocation: " . $filteredResult['count'] . "</p>\n";
                if ($filteredResult['count'] > 0) {
                    echo "<p>Sample filtered data:</p>\n";
                    echo "<pre>" . print_r(array_slice($filteredResult['data'], 0, 1), true) . "</pre>\n";
                }
            }
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}
?>
