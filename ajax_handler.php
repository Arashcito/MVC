<?php
// AJAX Handler for delete and edit operations
header('Content-Type: application/json');

// Database configuration
$host = 'ytc353.encs.concordia.ca';
$dbname = 'ytc353_1';
$username = 'ytc353_1';
$password = 'Adm1n001';
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'message' => 'Invalid action'];
    
    switch ($action) {
        case 'delete_location':
            $response = deleteLocation($pdo, $_POST['id']);
            break;
        case 'delete_personnel':
            $response = deletePersonnel($pdo, $_POST['id']);
            break;
        case 'delete_family':
            $response = deleteFamily($pdo, $_POST['id']);
            break;
        case 'delete_member':
            $response = deleteMember($pdo, $_POST['id']);
            break;
        case 'delete_payment':
            $response = deletePayment($pdo, $_POST['id']);
            break;
        case 'delete_team':
            $response = deleteTeam($pdo, $_POST['id']);
            break;
        case 'delete_session':
            $response = deleteSession($pdo, $_POST['id']);
            break;
        case 'delete_hobby':
            $response = deleteHobby($pdo, $_POST['hobbyName']);
            break;
        case 'delete_workinfo':
            $response = deleteWorkInfo($pdo, $_POST['pID'], $_POST['locationID'], $_POST['startDate']);
            break;
        case 'remove_member_hobby':
            $response = removeMemberHobby($pdo, $_POST['memberID'], $_POST['hobbyName']);
            break;
        case 'delete_email':
            $response = deleteEmail($pdo, $_POST['emailID']);
            break;
        case 'delete_postalarea':
            $response = deletePostalArea($pdo, $_POST['postalCode']);
            break;
        case 'delete_locationphone':
            $response = deleteLocationPhone($pdo, $_POST['locationID'], $_POST['phone']);
            break;
        case 'delete_familyhistory':
            $response = deleteFamilyHistory($pdo, $_POST['memberID'], $_POST['familyMemID'], $_POST['startDate']);
            break;
        case 'delete_teammember':
            $response = deleteTeamMember($pdo, $_POST['teamID'], $_POST['memberID']);
            break;
        case 'get_location':
            $response = getLocation($pdo, $_POST['id']);
            break;
        case 'get_postalarea':
            $response = getPostalArea($pdo, $_POST['postalCode']);
            break;
        case 'get_locationphone':
            $response = getLocationPhone($pdo, $_POST['locationID'], $_POST['phone']);
            break;
        case 'get_familyhistory':
            $response = getFamilyHistoryById($pdo, $_POST['memberID'], $_POST['familyMemID'], $_POST['startDate']);
            break;
        case 'get_teammember':
            $response = getTeamMemberById($pdo, $_POST['teamID'], $_POST['memberID']);
            break;
        case 'get_personnel':
            $response = getPersonnelById($pdo, $_POST['id']);
            break;
        case 'get_family':
            $response = getFamilyById($pdo, $_POST['id']);
            break;
        case 'get_member':
            $response = getMemberById($pdo, $_POST['id']);
            break;
        case 'get_payment':
            $response = getPaymentById($pdo, $_POST['id']);
            break;
        case 'get_team':
            $response = getTeamById($pdo, $_POST['id']);
            break;
        case 'get_session':
            $response = getSessionById($pdo, $_POST['id']);
            break;
        case 'get_hobby':
            $response = getHobbyByName($pdo, $_POST['hobbyName']);
            break;
        case 'get_workinfo':
            $response = getWorkInfoById($pdo, $_POST['pID'], $_POST['locationID'], $_POST['startDate']);
            break;
        case 'search':
            $response = performSearch($pdo, $_POST['section'], $_POST['query']);
            break;
        case 'generate_report':
            $response = generateReport($pdo, $_POST['reportType'], $_POST['locationFilter'] ?? '');
            break;
    }
    
    echo json_encode($response);
}

// Delete functions
function deleteLocation($pdo, $id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM Location WHERE locationID = ?");
        $stmt->execute([$id]);
        return ['success' => true, 'message' => 'Location deleted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to delete location: ' . $e->getMessage()];
    }
}

function deletePersonnel($pdo, $id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM Personnel WHERE employeeID = ?");
        $stmt->execute([$id]);
        return ['success' => true, 'message' => 'Personnel deleted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to delete personnel: ' . $e->getMessage()];
    }
}

function deleteFamily($pdo, $id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM FamilyMember WHERE familyMemID = ?");
        $stmt->execute([$id]);
        return ['success' => true, 'message' => 'Family member deleted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to delete family member: ' . $e->getMessage()];
    }
}

function deleteMember($pdo, $id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM ClubMember WHERE memberID = ?");
        $stmt->execute([$id]);
        return ['success' => true, 'message' => 'Member deleted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to delete member: ' . $e->getMessage()];
    }
}

function deletePayment($pdo, $id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM Payment WHERE memberID = ?");
        $stmt->execute([$id]);
        return ['success' => true, 'message' => 'Payment deleted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to delete payment: ' . $e->getMessage()];
    }
}

function deleteTeam($pdo, $id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM Team WHERE teamID = ?");
        $stmt->execute([$id]);
        return ['success' => true, 'message' => 'Team deleted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to delete team: ' . $e->getMessage()];
    }
}

function deleteSession($pdo, $id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM Session WHERE sessionID = ?");
        $stmt->execute([$id]);
        return ['success' => true, 'message' => 'Session deleted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to delete session: ' . $e->getMessage()];
    }
}

function deleteHobby($pdo, $hobbyName) {
    try {
        $stmt = $pdo->prepare("DELETE FROM Hobby WHERE hobbyName = ?");
        $stmt->execute([$hobbyName]);
        return ['success' => true, 'message' => 'Hobby deleted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to delete hobby: ' . $e->getMessage()];
    }
}

function deleteWorkInfo($pdo, $pID, $locationID, $startDate) {
    try {
        $stmt = $pdo->prepare("DELETE FROM WorkInfo WHERE pID = ? AND locationID = ? AND startDate = ?");
        $stmt->execute([$pID, $locationID, $startDate]);
        return ['success' => true, 'message' => 'Work assignment deleted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to delete work assignment: ' . $e->getMessage()];
    }
}

function removeMemberHobby($pdo, $memberID, $hobbyName) {
    try {
        $stmt = $pdo->prepare("DELETE FROM MemberHobby WHERE memberID = ? AND hobbyName = ?");
        $stmt->execute([$memberID, $hobbyName]);
        return ['success' => true, 'message' => 'Hobby removed from member successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to remove hobby: ' . $e->getMessage()];
    }
}

function deleteEmail($pdo, $emailID) {
    try {
        $stmt = $pdo->prepare("DELETE FROM Emails WHERE emailID = ?");
        $stmt->execute([$emailID]);
        return ['success' => true, 'message' => 'Email deleted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to delete email: ' . $e->getMessage()];
    }
}

function deletePostalArea($pdo, $postalCode) {
    try {
        $stmt = $pdo->prepare("DELETE FROM PostalAreaInfo WHERE postalCode = ?");
        $stmt->execute([$postalCode]);
        return ['success' => true, 'message' => 'Postal area deleted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to delete postal area: ' . $e->getMessage()];
    }
}

function deleteLocationPhone($pdo, $locationID, $phone) {
    try {
        $stmt = $pdo->prepare("DELETE FROM LocationPhone WHERE locationID = ? AND phone = ?");
        $stmt->execute([$locationID, $phone]);
        return ['success' => true, 'message' => 'Location phone deleted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to delete location phone: ' . $e->getMessage()];
    }
}

function deleteFamilyHistory($pdo, $memberID, $familyMemID, $startDate) {
    try {
        $stmt = $pdo->prepare("DELETE FROM FamilyHistory WHERE memberID = ? AND familyMemID = ? AND startDate = ?");
        $stmt->execute([$memberID, $familyMemID, $startDate]);
        return ['success' => true, 'message' => 'Family history deleted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to delete family history: ' . $e->getMessage()];
    }
}

function deleteTeamMember($pdo, $teamID, $memberID) {
    try {
        $stmt = $pdo->prepare("DELETE FROM TeamMember WHERE teamID = ? AND memberID = ?");
        $stmt->execute([$teamID, $memberID]);
        return ['success' => true, 'message' => 'Team member deleted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to delete team member: ' . $e->getMessage()];
    }
}

// Get functions for editing
function getLocation($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM Location WHERE locationID = ?");
        $stmt->execute([$id]);
        $location = $stmt->fetch(PDO::FETCH_ASSOC);
        return ['success' => true, 'data' => $location];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to get location: ' . $e->getMessage()];
    }
}

function getPostalArea($pdo, $postalCode) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM PostalAreaInfo WHERE postalCode = ?");
        $stmt->execute([$postalCode]);
        $postalArea = $stmt->fetch(PDO::FETCH_ASSOC);
        return ['success' => true, 'data' => $postalArea];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to get postal area: ' . $e->getMessage()];
    }
}

function getLocationPhone($pdo, $locationID, $phone) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM LocationPhone WHERE locationID = ? AND phone = ?");
        $stmt->execute([$locationID, $phone]);
        $locationPhone = $stmt->fetch(PDO::FETCH_ASSOC);
        return ['success' => true, 'data' => $locationPhone];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to get location phone: ' . $e->getMessage()];
    }
}

function getFamilyHistoryById($pdo, $memberID, $familyMemID, $startDate) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM FamilyHistory WHERE memberID = ? AND familyMemID = ? AND startDate = ?");
        $stmt->execute([$memberID, $familyMemID, $startDate]);
        $familyHistory = $stmt->fetch(PDO::FETCH_ASSOC);
        return ['success' => true, 'data' => $familyHistory];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to get family history: ' . $e->getMessage()];
    }
}

function getTeamMemberById($pdo, $teamID, $memberID) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM TeamMember WHERE teamID = ? AND memberID = ?");
        $stmt->execute([$teamID, $memberID]);
        $teamMember = $stmt->fetch(PDO::FETCH_ASSOC);
        return ['success' => true, 'data' => $teamMember];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to get team member: ' . $e->getMessage()];
    }
}

function getPersonnelById($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT p.*, per.* FROM Personnel p 
                              LEFT JOIN Person per ON p.employeeID = per.pID 
                              WHERE p.employeeID = ?");
        $stmt->execute([$id]);
        $personnel = $stmt->fetch(PDO::FETCH_ASSOC);
        return ['success' => true, 'data' => $personnel];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to get personnel: ' . $e->getMessage()];
    }
}

function getFamilyById($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT fm.*, per.* FROM FamilyMember fm 
                              LEFT JOIN Person per ON fm.familyMemID = per.pID 
                              WHERE fm.familyMemID = ?");
        $stmt->execute([$id]);
        $family = $stmt->fetch(PDO::FETCH_ASSOC);
        return ['success' => true, 'data' => $family];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to get family member: ' . $e->getMessage()];
    }
}

function getMemberById($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT cm.*, per.* FROM ClubMember cm 
                              LEFT JOIN Person per ON cm.memberID = per.pID 
                              WHERE cm.memberID = ?");
        $stmt->execute([$id]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);
        return ['success' => true, 'data' => $member];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to get member: ' . $e->getMessage()];
    }
}

function getPaymentById($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM Payment WHERE memberID = ?");
        $stmt->execute([$id]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);
        return ['success' => true, 'data' => $payment];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to get payment: ' . $e->getMessage()];
    }
}

function getTeamById($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM Team WHERE teamID = ?");
        $stmt->execute([$id]);
        $team = $stmt->fetch(PDO::FETCH_ASSOC);
        return ['success' => true, 'data' => $team];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to get team: ' . $e->getMessage()];
    }
}

function getSessionById($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM Session WHERE sessionID = ?");
        $stmt->execute([$id]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        return ['success' => true, 'data' => $session];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to get session: ' . $e->getMessage()];
    }
}

function getHobbyByName($pdo, $hobbyName) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM Hobby WHERE hobbyName = ?");
        $stmt->execute([$hobbyName]);
        $hobby = $stmt->fetch(PDO::FETCH_ASSOC);
        return ['success' => true, 'data' => $hobby];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to get hobby: ' . $e->getMessage()];
    }
}

function getWorkInfoById($pdo, $pID, $locationID, $startDate) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM WorkInfo WHERE pID = ? AND locationID = ? AND startDate = ?");
        $stmt->execute([$pID, $locationID, $startDate]);
        $workInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        return ['success' => true, 'data' => $workInfo];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to get work info: ' . $e->getMessage()];
    }
}

// Search functionality
function performSearch($pdo, $section, $query) {
    try {
        $results = [];
        $query = '%' . $query . '%';
        
        switch ($section) {
            case 'locations':
                $stmt = $pdo->prepare("SELECT * FROM Location WHERE name LIKE ? OR address LIKE ?");
                $stmt->execute([$query, $query]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
            case 'personnel':
                $stmt = $pdo->prepare("SELECT * FROM Personnel 
                                      WHERE firstName LIKE ? OR lastName LIKE ? OR email LIKE ?");
                $stmt->execute([$query, $query, $query]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
            case 'family':
                $stmt = $pdo->prepare("SELECT * FROM FamilyMembers 
                                      WHERE firstName LIKE ? OR lastName LIKE ? OR email LIKE ?");
                $stmt->execute([$query, $query, $query]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
            case 'members':
                $stmt = $pdo->prepare("SELECT * FROM ClubMembers 
                                      WHERE firstName LIKE ? OR lastName LIKE ?");
                $stmt->execute([$query, $query]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
        }
        
        return ['success' => true, 'data' => $results];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Search failed: ' . $e->getMessage()];
    }
}

// Report generation
function generateReport($pdo, $reportType, $locationFilter = '') {
    try {
        $results = [];
        $locationWhere = $locationFilter ? "WHERE l.locationID = ?" : "";
        $params = $locationFilter ? [$locationFilter] : [];
        
        switch ($reportType) {
            case 'members':
                $sql = "SELECT COUNT(*) as total_members, 
                        SUM(CASE WHEN cm.age < 18 THEN 1 ELSE 0 END) as minors,
                        SUM(CASE WHEN cm.age >= 18 THEN 1 ELSE 0 END) as adults
                        FROM ClubMembers cm 
                        LEFT JOIN Location l ON cm.locationID = l.locationID 
                        $locationWhere";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $results = $stmt->fetch(PDO::FETCH_ASSOC);
                break;
                
            case 'locations':
                $sql = "SELECT l.name, l.maxCapacity, COUNT(cm.memberID) as member_count
                        FROM Location l 
                        LEFT JOIN ClubMembers cm ON l.locationID = cm.locationID 
                        $locationWhere
                        GROUP BY l.locationID";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
                
            case 'inactive':
                $sql = "SELECT cm.firstName, cm.lastName, cm.dob, l.name as location_name
                        FROM ClubMembers cm 
                        LEFT JOIN Location l ON cm.locationID = l.locationID 
                        WHERE cm.status = 'Inactive' OR cm.status IS NULL
                        $locationWhere";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
        }
        
        return ['success' => true, 'data' => $results];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Report generation failed: ' . $e->getMessage()];
    }
}
?> 