<?php
// Secondary Family Info Handler - Get secondary family member and associated club members

function searchFamilyMember($pdo, $firstName, $lastName) {
    try {
        // Check if family member exists in the family table
        $searchQuery = "
            SELECT DISTINCT fm.familyMemID, p.firstName, p.lastName, p.pID
            FROM FamilyMember fm
            JOIN Person p ON fm.familyMemID = p.pID
            WHERE LOWER(p.firstName) LIKE LOWER(?) 
            AND LOWER(p.lastName) LIKE LOWER(?)
            AND fm.SecondaryMemID IS NOT NULL
        ";
        
        $searchStmt = $pdo->prepare($searchQuery);
        $searchStmt->execute(["%$firstName%", "%$lastName%"]);
        $familyMembers = $searchStmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'data' => $familyMembers,
            'count' => count($familyMembers)
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error searching family members: ' . $e->getMessage(),
            'data' => []
        ];
    }
}

function getSecondaryFamilyInfo($pdo, $familyMemID) {
    try {
        // Get secondary family member details and all associated club members
        $infoQuery = "
            SELECT 
                -- Secondary family member details
                sp.firstName as secondaryFirstName,
                sp.lastName as secondaryLastName,
                sp.phone as secondaryPhone,
                
                -- Club member details
                cm.memberID as clubMembershipNumber,
                cp.firstName as memberFirstName,
                cp.lastName as memberLastName,
                cp.dob as memberDateOfBirth,
                cp.ssn as memberSSN,
                cp.medicare as memberMedicare,
                cp.phone as memberPhone,
                cp.address as memberAddress,
                pai.city as memberCity,
                pai.province as memberProvince,
                cp.postalCode as memberPostalCode,
                
                -- Basic relationship description
                CONCAT('Emergency contact (', fm.primarySecondaryRelationship, ' of primary guardian)') as relationshipWithSecondary
                
            FROM FamilyMember fm
            JOIN Person sp ON fm.SecondaryMemID = sp.pID
            JOIN FamilyHistory fh ON fm.familyMemID = fh.familyID  
            JOIN ClubMember cm ON fh.memberID = cm.memberID
            JOIN Person cp ON cm.memberID = cp.pID
            JOIN PostalAreaInfo pai ON cp.postalCode = pai.postalCode

            WHERE fm.familyMemID = ?
              AND fm.SecondaryMemID IS NOT NULL
              AND (fh.endDate IS NULL OR fh.endDate > CURDATE())

            ORDER BY cm.memberID
        ";
        
        $infoStmt = $pdo->prepare($infoQuery);
        $infoStmt->execute([$familyMemID]);
        $familyInfo = $infoStmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($familyInfo)) {
            return [
                'success' => false,
                'message' => 'No secondary family member or associated club members found for this family member.',
                'data' => []
            ];
        }
        
        // Extract secondary member info (same for all rows)
        $secondaryMember = [
            'firstName' => $familyInfo[0]['secondaryFirstName'],
            'lastName' => $familyInfo[0]['secondaryLastName'],
            'phone' => $familyInfo[0]['secondaryPhone']
        ];
        
        // Extract club members info
        $clubMembers = [];
        foreach ($familyInfo as $row) {
            $clubMembers[] = [
                'clubMembershipNumber' => $row['clubMembershipNumber'],
                'firstName' => $row['memberFirstName'],
                'lastName' => $row['memberLastName'],
                'dateOfBirth' => $row['memberDateOfBirth'],
                'ssn' => $row['memberSSN'],
                'medicare' => $row['memberMedicare'],
                'phone' => $row['memberPhone'],
                'address' => $row['memberAddress'],
                'city' => $row['memberCity'],
                'province' => $row['memberProvince'],
                'postalCode' => $row['memberPostalCode'],
                'relationshipWithSecondary' => $row['relationshipWithSecondary']
            ];
        }
        
        return [
            'success' => true,
            'data' => [
                'secondaryMember' => $secondaryMember,
                'clubMembers' => $clubMembers
            ],
            'clubMemberCount' => count($clubMembers)
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error retrieving secondary family information: ' . $e->getMessage(),
            'data' => []
        ];
    }
}

function getAllSecondaryFamilyInfo($pdo) {
    try {
        // Get all family members with secondary members
        $allFamiliesQuery = "
            SELECT DISTINCT fm.familyMemID, p.firstName, p.lastName
            FROM FamilyMember fm
            JOIN Person p ON fm.familyMemID = p.pID
            WHERE fm.SecondaryMemID IS NOT NULL
            ORDER BY p.lastName, p.firstName
        ";
        
        $allFamiliesStmt = $pdo->prepare($allFamiliesQuery);
        $allFamiliesStmt->execute();
        $allFamilies = $allFamiliesStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $allResults = [];
        
        foreach ($allFamilies as $family) {
            $familyInfo = getSecondaryFamilyInfo($pdo, $family['familyMemID']);
            if ($familyInfo['success']) {
                $allResults[] = [
                    'primaryMember' => [
                        'familyMemID' => $family['familyMemID'],
                        'firstName' => $family['firstName'],
                        'lastName' => $family['lastName']
                    ],
                    'secondaryMember' => $familyInfo['data']['secondaryMember'],
                    'clubMembers' => $familyInfo['data']['clubMembers']
                ];
            }
        }
        
        return [
            'success' => true,
            'data' => $allResults,
            'count' => count($allResults)
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error retrieving all secondary family information: ' . $e->getMessage(),
            'data' => []
        ];
    }
}

// Function to export secondary family data to CSV
function exportSecondaryFamilyCSV($pdo, $familyMemID = null) {
    if ($familyMemID) {
        $result = getSecondaryFamilyInfo($pdo, $familyMemID);
        $exportData = [$result['data']];
    } else {
        $result = getAllSecondaryFamilyInfo($pdo);
        $exportData = $result['data'];
    }
    
    if (!$result['success']) {
        return $result;
    }
    
    $filename = 'secondary_family_info_' . date('Y-m-d_H-i-s') . '.csv';
    $filepath = __DIR__ . '/exports/' . $filename;
    
    // Create exports directory if it doesn't exist
    if (!is_dir(__DIR__ . '/exports')) {
        mkdir(__DIR__ . '/exports', 0755, true);
    }
    
    $file = fopen($filepath, 'w');
    
    // CSV Headers
    $headers = [
        'Primary Member First Name', 'Primary Member Last Name',
        'Secondary Member First Name', 'Secondary Member Last Name', 'Secondary Member Phone',
        'Club Membership Number', 'Member First Name', 'Member Last Name', 'Date of Birth',
        'SSN', 'Medicare', 'Member Phone', 'Address', 'City', 'Province', 'Postal Code',
        'Relationship with Secondary'
    ];
    
    fputcsv($file, $headers);
    
    // CSV Data
    foreach ($exportData as $familyData) {
        $primaryFirst = isset($familyData['primaryMember']) ? $familyData['primaryMember']['firstName'] : '';
        $primaryLast = isset($familyData['primaryMember']) ? $familyData['primaryMember']['lastName'] : '';
        
        foreach ($familyData['clubMembers'] as $clubMember) {
            fputcsv($file, [
                $primaryFirst,
                $primaryLast,
                $familyData['secondaryMember']['firstName'],
                $familyData['secondaryMember']['lastName'],
                $familyData['secondaryMember']['phone'],
                $clubMember['clubMembershipNumber'],
                $clubMember['firstName'],
                $clubMember['lastName'],
                $clubMember['dateOfBirth'],
                $clubMember['ssn'],
                $clubMember['medicare'],
                $clubMember['phone'],
                $clubMember['address'],
                $clubMember['city'],
                $clubMember['province'],
                $clubMember['postalCode'],
                $clubMember['relationshipWithSecondary']
            ]);
        }
    }
    
    fclose($file);
    
    return [
        'success' => true,
        'message' => 'Secondary family info exported successfully',
        'filename' => $filename,
        'filepath' => $filepath
    ];
}
?>
