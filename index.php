<?php
// Include configuration file for database connection
require_once 'config.php';

// Define constant for included files
define('DB_CONNECTION_AVAILABLE', true);

// Include payment handler
require_once 'payment_handler.php';

// Include location info handler
require_once 'location_info_handler.php';

// Include secondary family handler
require_once 'secondary_family_handler.php';

// Include minors to majors handler
require_once 'minors_to_majors_handler.php';

// Include goalkeepers only handler
require_once 'goalkeepers_only_handler.php';

// Include all-rounder players handler
require_once 'all_rounder_players_handler.php';

// Get database connection from config
try {
    $pdo = getDBConnection();
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Initialize variables for secondary family system
$searchResults = null;
$familyInfo = null;
$searchPerformed = false;

// Initialize variables for minors to majors system
$minorsToMajorsResult = null;
$availableLocations = null;

// Initialize variables for goalkeepers only system
$goalkeepersOnlyResult = null;
$availableGoalkeeperLocations = null;

// Initialize variables for all-rounder players system
$allRounderPlayersResult = null;
$allRounderLocations = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Form submitted: " . print_r($_POST, true));
    
    // Simple test to see if any POST data is received
    if (empty($_POST)) {
        error_log("POST data is empty!");
    }
    
    if (isset($_POST['action'])) {
        $success = false;
        $message = '';
        
        switch ($_POST['action']) {
            case 'save_location':
                error_log("Processing save_location action");
                $success = saveLocation($pdo, $_POST);
                $message = $success ? 'Location saved successfully!' : 'Error saving location.';
                error_log("Location save result: " . ($success ? 'success' : 'failed'));
                break;
            case 'save_personnel':
                $success = savePersonnel($pdo, $_POST);
                $message = $success ? 'Personnel saved successfully!' : 'Error saving personnel.';
                break;
            case 'save_family':
                $success = saveFamily($pdo, $_POST);
                $message = $success ? 'Family member saved successfully!' : 'Error saving family member.';
                break;
            case 'save_member':
                $success = saveMember($pdo, $_POST);
                $message = $success ? 'Member saved successfully!' : 'Error saving member.';
                break;
            case 'save_payment':
                $success = savePayment($pdo, $_POST);
                $message = $success ? 'Payment saved successfully!' : 'Error saving payment.';
                break;
            case 'process_payment':
                $result = processPayment($pdo, $_POST);
                $success = $result['success'];
                $message = $result['message'];
                break;
            case 'save_team':
                $success = saveTeam($pdo, $_POST);
                $message = $success ? 'Team saved successfully!' : 'Error saving team.';
                break;
            case 'save_session':
                $success = saveSession($pdo, $_POST);
                $message = $success ? 'Session saved successfully!' : 'Error saving session.';
                break;
            case 'save_hobby':
                $success = saveHobby($pdo, $_POST);
                $message = $success ? 'Hobby saved successfully!' : 'Error saving hobby.';
                break;
            case 'save_member_hobby':
                $success = saveMemberHobby($pdo, $_POST);
                $message = $success ? 'Member hobby saved successfully!' : 'Error saving member hobby.';
                break;
            case 'save_workinfo':
                $success = saveWorkInfo($pdo, $_POST);
                $message = $success ? 'Work info saved successfully!' : 'Error saving work info.';
                break;
            case 'export_location_csv':
                $result = exportLocationInfoCSV($pdo);
                if ($result['success']) {
                    // Force download
                    header('Content-Type: application/csv');
                    header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
                    header('Pragma: no-cache');
                    header('Expires: 0');
                    readfile($result['filepath']);
                    exit();
                } else {
                    $success = false;
                    $message = $result['message'];
                }
                break;
            case 'export_secondary_family_csv':
                $familyId = isset($_GET['family_id']) ? $_GET['family_id'] : null;
                $result = exportSecondaryFamilyCSV($pdo, $familyId);
                if ($result['success']) {
                    // Force download
                    header('Content-Type: application/csv');
                    header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
                    header('Pragma: no-cache');
                    header('Expires: 0');
                    readfile($result['filepath']);
                    exit();
                } else {
                    $success = false;
                    $message = $result['message'];
                }
                break;
            case 'search_family':
                if (!empty($_POST['first_name']) && !empty($_POST['last_name'])) {
                    $searchResults = searchFamilyMember($pdo, $_POST['first_name'], $_POST['last_name']);
                    $searchPerformed = true;
                    $success = $searchResults['success'];
                    $message = $searchResults['success'] ? 
                        'Found ' . $searchResults['count'] . ' family member(s)' : 
                        $searchResults['message'];
                } else {
                    $success = false;
                    $message = 'Please enter both first and last name to search.';
                }
                break;
            case 'get_family_info':
                error_log("get_family_info action triggered");
                if (!empty($_POST['family_mem_id'])) {
                    error_log("Family member ID: " . $_POST['family_mem_id']);
                    $familyInfo = getSecondaryFamilyInfo($pdo, $_POST['family_mem_id']);
                    error_log("Family info result: " . print_r($familyInfo, true));
                    $success = $familyInfo['success'];
                    $message = $familyInfo['success'] ? 
                        'Family information loaded successfully' : 
                        $familyInfo['message'];
                } else {
                    error_log("No family member ID provided");
                    $success = false;
                    $message = 'Family member ID is required.';
                }
                break;
            case 'filter_minors_majors':
                $locationFilter = $_POST['location_filter'] ?? 'all';
                if ($locationFilter && $locationFilter !== 'all') {
                    $minorsToMajorsResult = getMinorsToMajorsByLocation($pdo, $locationFilter);
                } else {
                    $minorsToMajorsResult = getMinorsToMajorsReport($pdo);
                }
                $success = $minorsToMajorsResult['success'];
                $message = $minorsToMajorsResult['success'] ? 
                    'Report filtered successfully - found ' . count($minorsToMajorsResult['data']) . ' members' : 
                    $minorsToMajorsResult['message'];
                break;
            case 'export_minors_majors_csv':
                $locationFilter = $_GET['location'] ?? null;
                $result = exportMinorsToMajorsCSV($pdo, $locationFilter);
                if ($result['success']) {
                    // Force download
                    header('Content-Type: application/csv');
                    header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
                    header('Pragma: no-cache');
                    header('Expires: 0');
                    readfile($result['filepath']);
                    exit();
                } else {
                    $success = false;
                    $message = $result['message'];
                }
                break;
            case 'filter_goalkeepers':
                $locationFilter = $_POST['location_filter'] ?? 'all';
                if ($locationFilter && $locationFilter !== 'all') {
                    $goalkeepersOnlyResult = getGoalkeepersByLocation($pdo, $locationFilter);
                } else {
                    $goalkeepersOnlyResult = getGoalkeepersOnlyReport($pdo);
                }
                $success = $goalkeepersOnlyResult['success'];
                $message = $goalkeepersOnlyResult['success'] ? 
                    'Report filtered successfully - found ' . count($goalkeepersOnlyResult['data']) . ' goalkeepers' : 
                    $goalkeepersOnlyResult['message'];
                break;
            case 'export_goalkeepers_csv':
                $locationFilter = $_GET['location'] ?? null;
                $result = exportGoalkeepersOnlyCSV($pdo, $locationFilter);
                if ($result['success']) {
                    // Force download
                    header('Content-Type: application/csv');
                    header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
                    header('Pragma: no-cache');
                    header('Expires: 0');
                    readfile($result['filepath']);
                    exit();
                } else {
                    $success = false;
                    $message = $result['message'];
                }
                break;
            case 'filter_all_rounder_players':
                $locationFilter = $_POST['location_filter'] ?? 'all';
                if ($locationFilter && $locationFilter !== 'all') {
                    $allRounderPlayersResult = getAllRounderPlayersByLocation($pdo, $locationFilter);
                } else {
                    $allRounderPlayersResult = getAllRounderPlayersReport($pdo);
                }
                $success = $allRounderPlayersResult['success'];
                $message = $allRounderPlayersResult['success'] ? 
                    'Report filtered successfully - found ' . count($allRounderPlayersResult['data']) . ' all-rounder players' : 
                    $allRounderPlayersResult['message'];
                break;
            case 'export_all_rounder_players_csv':
                $locationFilter = $_GET['location'] ?? null;
                $result = exportAllRounderPlayersCSV($pdo, $locationFilter);
                if ($result['success']) {
                    // Force download
                    header('Content-Type: application/csv');
                    header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
                    header('Pragma: no-cache');
                    header('Expires: 0');
                    readfile($result['filepath']);
                    exit();
                } else {
                    $success = false;
                    $message = $result['message'];
                }
                break;
        }
        
        // Store message in session for display
        $_SESSION['message'] = $message;
        $_SESSION['success'] = $success;
        
        // Redirect to prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Database functions
function saveLocation($pdo, $data) {
    try {
        // Debug: Log the received data
        error_log("Location save data: " . print_r($data, true));
        
        // Check if we're editing an existing location
        if (isset($data['locationID']) && !empty($data['locationID'])) {
            // UPDATE existing location
            $stmt = $pdo->prepare("UPDATE Location SET name = ?, type = ?, address = ?, postalCode = ?, webAddress = ?, maxCapacity = ? WHERE locationID = ?");
            $stmt->execute([
                $data['name'],
                $data['type'],
                $data['address'],
                $data['postal_code'],
                $data['web_address'],
                $data['max_capacity'],
                $data['locationID']
            ]);
            error_log("Location updated successfully");
        } else {
            // INSERT new location
            $stmt = $pdo->prepare("INSERT INTO Location (name, type, address, postalCode, webAddress, maxCapacity) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['name'],
                $data['type'],
                $data['address'],
                $data['postal_code'],
                $data['web_address'],
                $data['max_capacity']
            ]);
            error_log("Location inserted successfully");
        }
        
        return true;
        
    } catch (PDOException $e) {
        error_log("Location save error: " . $e->getMessage());
        return false;
    }
}

function savePersonnel($pdo, $data) {
    try {
        // Debug: Log the received data
        error_log("Personnel save data: " . print_r($data, true));
        
        $pdo->beginTransaction();
        
        // Check if we're editing existing personnel
        if (isset($data['pID']) && !empty($data['pID'])) {
            // UPDATE existing person
            $stmt = $pdo->prepare("UPDATE Person SET firstName = ?, lastName = ?, dob = ?, ssn = ?, medicare = ?, address = ?, postalCode = ?, phone = ?, email = ? WHERE pID = ?");
            $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['dob'],
                $data['ssn'],
                $data['medicare'],
                $data['address'],
                $data['postal_code'],
                $data['phone'],
                $data['email'],
                $data['pID']
            ]);
            
            // UPDATE existing personnel
            $stmt = $pdo->prepare("UPDATE Personnel SET role = ?, mandate = ? WHERE employeeID = ?");
            $stmt->execute([
                $data['role'],
                $data['mandate'],
                $data['pID']
            ]);
        } else {
            // INSERT new person
            $stmt = $pdo->prepare("INSERT INTO Person (firstName, lastName, dob, ssn, medicare, address, postalCode, phone, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['dob'],
                $data['ssn'],
                $data['medicare'],
                $data['address'],
                $data['postal_code'],
                $data['phone'],
                $data['email']
            ]);
            
            $personID = $pdo->lastInsertId();
            
            // INSERT new personnel
            $stmt = $pdo->prepare("INSERT INTO Personnel (employeeID, role, mandate) VALUES (?, ?, ?)");
            $stmt->execute([
                $personID,
                $data['role'],
                $data['mandate']
            ]);
        }
        
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        return false;
    }
}

function saveFamily($pdo, $data) {
    try {
        // Debug: Log the received data
        error_log("Family save data: " . print_r($data, true));
        
        $pdo->beginTransaction();
        
        // Check if we're editing existing family member
        if (isset($data['familyMemID']) && !empty($data['familyMemID'])) {
            // UPDATE existing person
            $stmt = $pdo->prepare("UPDATE Person SET firstName = ?, lastName = ?, dob = ?, ssn = ?, medicare = ?, address = ?, postalCode = ?, phone = ?, email = ? WHERE pID = ?");
            $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['dob'],
                $data['ssn'],
                $data['medicare'],
                $data['address'],
                $data['postal_code'],
                $data['phone'],
                $data['email'],
                $data['familyMemID']
            ]);
            
            // UPDATE existing family member
            $stmt = $pdo->prepare("UPDATE FamilyMember SET primarySecondaryRelationship = ? WHERE familyMemID = ?");
            $stmt->execute([
                $data['relationshipType'],
                $data['familyMemID']
            ]);
        } else {
            // INSERT new person
            $stmt = $pdo->prepare("INSERT INTO Person (firstName, lastName, dob, ssn, medicare, address, postalCode, phone, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['dob'],
                $data['ssn'],
                $data['medicare'],
                $data['address'],
                $data['postal_code'],
                $data['phone'],
                $data['email']
            ]);
            
            $personID = $pdo->lastInsertId();
            
            // INSERT new family member
            $stmt = $pdo->prepare("INSERT INTO FamilyMember (familyMemID, primarySecondaryRelationship) VALUES (?, ?)");
            $stmt->execute([
                $personID,
                $data['relationshipType']
            ]);
        }
        
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        return false;
    }
}

function saveMember($pdo, $data) {
    try {
        // Debug: Log the received data
        error_log("Member save data: " . print_r($data, true));
        
        $pdo->beginTransaction();
        
        // Calculate member type based on age from DOB
        $dob = new DateTime($data['dob']);
        $today = new DateTime();
        $age = $today->diff($dob)->y;
        $memberType = ($age < 18) ? 'Minor' : 'Major';
        
        // Check if we're editing existing member
        if (isset($data['memberID']) && !empty($data['memberID'])) {
            // UPDATE existing person
            $stmt = $pdo->prepare("UPDATE Person SET firstName = ?, lastName = ?, dob = ?, ssn = ?, medicare = ?, address = ?, postalCode = ?, phone = ?, email = ? WHERE pID = ?");
            $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['dob'],
                $data['ssn'],
                $data['medicare'],
                $data['address'],
                $data['postal_code'],
                $data['phone'],
                $data['email'],
                $data['memberID']
            ]);
            
            // UPDATE existing member
            $stmt = $pdo->prepare("UPDATE ClubMember SET locationID = ?, memberType = ?, status = ?, height = ?, weight = ?, dateJoined = ?, familyMemID = ? WHERE memberID = ?");
            $stmt->execute([
                $data['location_id'],
                $memberType,
                $data['status'] ?? 'Active',
                $data['height'],
                $data['weight'],
                $data['dateJoined'] ?? date('Y-m-d'),
                $data['family_member_id'],
                $data['memberID']
            ]);
        } else {
            // INSERT new person
            $stmt = $pdo->prepare("INSERT INTO Person (firstName, lastName, dob, ssn, medicare, address, postalCode, phone, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['dob'],
                $data['ssn'],
                $data['medicare'],
                $data['address'],
                $data['postal_code'],
                $data['phone'],
                $data['email']
            ]);
            
            $personID = $pdo->lastInsertId();
            
            // INSERT new member
            $stmt = $pdo->prepare("INSERT INTO ClubMember (memberID, locationID, memberType, status, height, weight, dateJoined, familyMemID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $personID,
                $data['location_id'],
                $memberType,
                $data['status'] ?? 'Active',
                $data['height'],
                $data['weight'],
                $data['dateJoined'] ?? date('Y-m-d'),
                $data['family_member_id']
            ]);
        }
        
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        return false;
    }
}



function saveSession($pdo, $data) {
    try {
        // Debug: Log the received data
        error_log("Session save data: " . print_r($data, true));
        
        // Parse score if provided
        $team1Score = null;
        $team2Score = null;
        if (!empty($data['score'])) {
            $scoreParts = explode('-', $data['score']);
            if (count($scoreParts) == 2) {
                $team1Score = trim($scoreParts[0]);
                $team2Score = trim($scoreParts[1]);
            }
        }
        
        // Check if we're editing existing session
        if (isset($data['sessionID']) && !empty($data['sessionID'])) {
            // UPDATE existing session
            $stmt = $pdo->prepare("UPDATE Session SET sessionType = ?, sessionDate = ?, startTime = ?, locationID = ?, team1ID = ?, team2ID = ?, team1Score = ?, team2Score = ? WHERE sessionID = ?");
            $stmt->execute([
                $data['type'],
                $data['date'],
                $data['time'],
                $data['location_id'] ?? null,
                $data['team1_id'],
                $data['team2_id'] ?: null,
                $team1Score,
                $team2Score,
                $data['sessionID']
            ]);
        } else {
            // INSERT new session
            $stmt = $pdo->prepare("INSERT INTO Session (sessionType, sessionDate, startTime, locationID, team1ID, team2ID, team1Score, team2Score) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['type'],
                $data['date'],
                $data['time'],
                $data['location_id'] ?? null,
                $data['team1_id'],
                $data['team2_id'] ?: null,
                $team1Score,
                $team2Score
            ]);
        }
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Data retrieval functions
function getLocations($pdo) {
    $stmt = $pdo->query("SELECT * FROM Location ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPersonnel($pdo) {
    try {
        $stmt = $pdo->query("SELECT p.*, per.firstName, per.lastName, per.dob, per.ssn, per.medicare, 
                                   per.address, per.postalCode, per.phone, per.email, l.name as location_name 
                            FROM Personnel p 
                            LEFT JOIN Person per ON p.employeeID = per.pID 
                            LEFT JOIN Location l ON p.employeeID = l.managerID 
                            ORDER BY per.lastName");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getFamilyMembers($pdo) {
    try {
        $stmt = $pdo->query("SELECT fm.*, per.firstName, per.lastName, per.dob, per.ssn, per.medicare, 
                                   per.address, per.postalCode, per.phone, per.email, 
                                   fm.primarySecondaryRelationship as relationshipType
                            FROM FamilyMember fm 
                            LEFT JOIN Person per ON fm.familyMemID = per.pID 
                            ORDER BY per.lastName");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getMembers($pdo) {
    try {
        $stmt = $pdo->query("SELECT cm.*, per.firstName, per.lastName, per.dob, per.ssn, per.medicare, 
                                   per.address, per.postalCode, per.phone, per.email, l.name as location_name 
                            FROM ClubMember cm 
                            LEFT JOIN Person per ON cm.memberID = per.pID 
                            LEFT JOIN Location l ON cm.locationID = l.locationID 
                            ORDER BY per.lastName");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}



function getSessions($pdo) {
    try {
        $stmt = $pdo->query("SELECT s.*, t1.teamName as team1_name, t2.teamName as team2_name, 
                             CONCAT(per.firstName, ' ', per.lastName) as coach_name,
                             CONCAT(s.team1Score, '-', s.team2Score) as score,
                             l.name as location_name
                             FROM Session s 
                             LEFT JOIN Team t1 ON s.team1ID = t1.teamID 
                             LEFT JOIN Team t2 ON s.team2ID = t2.teamID 
                             LEFT JOIN Personnel p ON t1.headCoachID = p.employeeID 
                             LEFT JOIN Person per ON p.employeeID = per.pID 
                             LEFT JOIN Location l ON s.locationID = l.locationID 
                             ORDER BY s.sessionDate DESC, s.startTime DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getTeams($pdo) {
    try {
        $stmt = $pdo->query("SELECT t.*, 
                             CONCAT(per.firstName, ' ', per.lastName) as coach_name,
                             l.name as location_name
                             FROM Team t 
                             LEFT JOIN Personnel p ON t.headCoachID = p.employeeID 
                             LEFT JOIN Person per ON p.employeeID = per.pID 
                             LEFT JOIN Location l ON t.locationID = l.locationID 
                             ORDER BY t.teamName");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getCoaches($pdo) {
    try {
        $stmt = $pdo->query("SELECT p.*, per.firstName, per.lastName 
                            FROM Personnel p 
                            LEFT JOIN Person per ON p.employeeID = per.pID 
                            WHERE p.role IN ('Coach', 'AssistantCoach') 
                            ORDER BY per.lastName, per.firstName");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getHobbies($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM Hobby ORDER BY hobbyName");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}



function getWorkInfo($pdo) {
    try {
        $stmt = $pdo->query("SELECT wi.*, per.firstName, per.lastName, l.name as location_name 
                            FROM WorkInfo wi 
                            LEFT JOIN Personnel p ON wi.employeeID = p.employeeID 
                            LEFT JOIN Person per ON p.employeeID = per.pID 
                            LEFT JOIN Location l ON wi.locationID = l.locationID 
                            ORDER BY per.lastName, wi.startDate DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getYearlyPayments($pdo) {
    try {
        $stmt = $pdo->query("SELECT p.paymentID, p.memberID, p.paymentDate, p.amount, p.method, p.membershipYear, p.installmentNo,
                                   per.firstName, per.lastName 
                            FROM Payment p 
                            LEFT JOIN Person per ON p.memberID = per.pID 
                            ORDER BY p.paymentDate DESC, per.lastName");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}



function saveHobby($pdo, $data) {
    try {
        $stmt = $pdo->prepare("INSERT INTO Hobby (hobbyName) VALUES (?)");
        $stmt->execute([$data['hobbyName']]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function saveMemberHobby($pdo, $data) {
    try {
        $stmt = $pdo->prepare("INSERT INTO MemberHobby (memberID, hobbyName) VALUES (?, ?)");
        $stmt->execute([
            $data['memberID'],
            $data['hobbyName']
        ]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function saveWorkInfo($pdo, $data) {
    try {
        $stmt = $pdo->prepare("INSERT INTO WorkInfo (employeeID, locationID, startDate, endDate) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['employeeID'],
            $data['locationID'],
            $data['startDate'],
            $data['endDate'] ?: null
        ]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}



function savePayment($pdo, $data) {
    try {
        // Debug: Log the received data
        error_log("Payment save data: " . print_r($data, true));
        
        $stmt = $pdo->prepare("INSERT INTO Payment (memberID, amount, method, paymentDate, membershipYear, installmentNo) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['member_id'],
            $data['amount'],
            $data['payment_method'],
            $data['payment_date'],
            $data['year'],
            $data['installment_no'] ?? 1
        ]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function saveTeam($pdo, $data) {
    try {
        // Debug: Log the received data
        error_log("Team save data: " . print_r($data, true));
        
        $pdo->beginTransaction();
        
        // Check if we're editing existing team
        if (isset($data['teamID']) && !empty($data['teamID'])) {
            // UPDATE existing team
            $stmt = $pdo->prepare("UPDATE Team SET teamName = ?, gender = ?, headCoachID = ?, locationID = ? WHERE teamID = ?");
            $stmt->execute([
                $data['name'],
                $data['gender'],
                $data['head_coach_id'],
                $data['location_id'],
                $data['teamID']
            ]);
        } else {
            // INSERT new team
            $stmt = $pdo->prepare("INSERT INTO Team (teamName, gender, headCoachID, locationID) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['name'],
                $data['gender'],
                $data['head_coach_id'],
                $data['location_id']
            ]);
            
            $teamID = $pdo->lastInsertId();
            
            // Add team members if selected
            if (isset($data['players']) && is_array($data['players'])) {
                $stmt = $pdo->prepare("INSERT INTO TeamMember (teamID, memberID, join_date, status) VALUES (?, ?, CURDATE(), 'active')");
                foreach ($data['players'] as $playerID) {
                    $stmt->execute([$teamID, $playerID]);
                }
            }
        }
        
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        return false;
    }
}

// Load initial data for all-rounder players system if not already loaded
if ($allRounderPlayersResult === null) {
    $allRounderPlayersResult = getAllRounderPlayersReport($pdo);
}
if ($allRounderLocations === null) {
    $locationsResult = getAllRounderLocations($pdo);
    $allRounderLocations = $locationsResult['success'] ? $locationsResult['data'] : [];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MVC Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.4;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header-buttons {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }

        .btn-primary {
            background: #007cba;
            color: white;
        }

        .btn-primary:hover {
            background: #005a87;
        }

        .btn.active {
            background: #28a745 !important;
            color: white;
        }

        .nav-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 2px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            background: white;
        }

        .nav-tab {
            background: #f8f8f8;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-right: 1px solid #ddd;
        }

        .nav-tab:hover {
            background: #e8e8e8;
        }

        .nav-tab.active {
            background: #007cba;
            color: white;
        }

        .content {
            background: white;
            border: 1px solid #ddd;
            padding: 20px;
        }

        .section {
            display: none;
        }

        .section.active {
            display: block;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
        }

        .btn {
            background: #007cba;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            background: #005a87;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 3px;
            font-weight: bold;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #007cba;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }

        .data-table th {
            background: #f8f8f8;
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
        }

        .data-table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }

        .data-table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .action-buttons button {
            padding: 4px 8px;
            border: none;
            cursor: pointer;
            font-size: 12px;
        }

        .edit-btn {
            background: #28a745;
            color: white;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .view-btn {
            background: #6c757d;
            color: white;
        }

        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            min-width: 120px;
        }

        .filter-group label {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .filter-group select,
        .filter-group input {
            padding: 5px;
            border: 1px solid #ccc;
            font-size: 12px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }

        .modal-content {
            background: white;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .placeholder-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .placeholder-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }

        .placeholder-box h4 {
            color: #6c757d;
            margin-bottom: 10px;
        }

        .placeholder-box p {
            color: #6c757d;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-tabs {
                flex-direction: column;
            }
            
            .section-header {
                flex-direction: column;
                gap: 10px;
            }

            .reports-nav-tabs {
                display: none;
                flex-wrap: wrap;
                gap: 2px;
                margin-bottom: 20px;
                border: 1px solid #ddd;
                background: white;
            }

            .report-section {
                display: none;
                padding: 20px;
                background: white;
                border: 1px solid #ddd;
            }

            .report-section.active {
                display: block;
            }

            .report-form {
                background: #f8f9fa;
                padding: 20px;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                margin-bottom: 20px;
            }

            .report-results {
                margin-top: 20px;
            }

            .coming-soon {
                text-align: center;
                padding: 40px;
                color: #6c757d;
                font-style: italic;
            }
        }
    </style>
</head>
<body>
    <?php
    // Display success/error messages
    if (isset($_SESSION['message'])) {
        $messageClass = $_SESSION['success'] ? 'success-message' : 'error-message';
        echo '<div class="' . $messageClass . '">' . htmlspecialchars($_SESSION['message']) . '</div>';
        unset($_SESSION['message']);
        unset($_SESSION['success']);
    }
    ?>
    <div class="container">
        <div class="header">
            <h1>Montréal Volleyball Club - Management System</h1>
            <p>COMP 353 Project</p>
            <div class="header-buttons">
                <button class="btn btn-primary" onclick="showMainSystem()">Main System</button>
                <button class="btn btn-secondary" onclick="showReports()">Reports</button>
            </div>
        </div>

        <div class="nav-tabs" id="main-nav-tabs">
            <button class="nav-tab active" onclick="showSection('locations')">Locations</button>
            <button class="nav-tab" onclick="showSection('personnel')">Personnel</button>
            <button class="nav-tab" onclick="showSection('family')">Family</button>
            <button class="nav-tab" onclick="showSection('members')">Members</button>
            <button class="nav-tab" onclick="showSection('teams')">Teams</button>
            <button class="nav-tab" onclick="showSection('sessions')">Sessions</button>
            <button class="nav-tab" onclick="showSection('payments')">Payments</button>
        </div>

        <div class="content">
            <!-- Locations Section -->
            <div id="locations" class="section active">
                <div class="section-header">
                    <h2 class="section-title">Location Management</h2>
                    <button class="btn" onclick="openModal('locationModal')">Add Location</button>
                </div>
                

                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Address</th>
                            <th>Capacity</th>
                            <th>Web Address</th>
                            <th>General Manager</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $locations = getLocations($pdo);
                        foreach ($locations as $location) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($location['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($location['type']) . "</td>";
                            echo "<td>" . htmlspecialchars($location['address']) . "</td>";
                            echo "<td>" . htmlspecialchars($location['maxCapacity']) . "</td>";
                            echo "<td>" . htmlspecialchars($location['webAddress'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($location['managerID'] ?? 'N/A') . "</td>";
                            echo "<td class='action-buttons'>";
                            echo "<button class='edit-btn' onclick='editLocation(" . $location['locationID'] . ")'>Edit</button>";
                            echo "<button class='delete-btn' onclick='deleteLocation(" . $location['locationID'] . ")'>Delete</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Personnel Section -->
            <div id="personnel" class="section">
                <div class="section-header">
                    <h2 class="section-title">Personnel Management</h2>
                    <button class="btn" onclick="openModal('personnelModal')">Add Personnel</button>
                </div>
                

                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>SSN</th>
                            <th>Medicare</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Role</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $personnel = getPersonnel($pdo);
                        foreach ($personnel as $person) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($person['firstName'] . ' ' . $person['lastName']) . "</td>";
                            echo "<td>" . htmlspecialchars($person['ssn']) . "</td>";
                            echo "<td>" . htmlspecialchars($person['medicare']) . "</td>";
                            echo "<td>" . htmlspecialchars($person['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($person['phone']) . "</td>";
                            echo "<td>" . htmlspecialchars($person['address']) . "</td>";
                            echo "<td>" . htmlspecialchars($person['role']) . "</td>";
                            echo "<td>" . htmlspecialchars($person['location_name'] ?? 'N/A') . "</td>";
                            echo "<td class='action-buttons'>";
                            echo "<button class='edit-btn' onclick='editPersonnel(" . $person['employeeID'] . ")'>Edit</button>";
                            echo "<button class='delete-btn' onclick='deletePersonnel(" . $person['employeeID'] . ")'>Delete</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Family Section -->
            <div id="family" class="section">
                <div class="section-header">
                    <h2 class="section-title">Family Member Management</h2>
                    <button class="btn" onclick="openModal('familyModal')">Add Family Member</button>
                </div>
                

                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>SSN</th>
                            <th>Medicare</th>
                            <th>Type</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $familyMembers = getFamilyMembers($pdo);
                        foreach ($familyMembers as $family) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($family['firstName'] . ' ' . $family['lastName']) . "</td>";
                            echo "<td>" . htmlspecialchars($family['ssn']) . "</td>";
                            echo "<td>" . htmlspecialchars($family['medicare']) . "</td>";
                            echo "<td>" . htmlspecialchars($family['relationshipType']) . "</td>";
                            echo "<td>" . htmlspecialchars($family['phone']) . "</td>";
                            echo "<td>" . htmlspecialchars($family['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($family['address']) . "</td>";
                            echo "<td class='action-buttons'>";
                            echo "<button class='edit-btn' onclick='editFamily(" . $family['familyMemID'] . ")'>Edit</button>";
                            echo "<button class='delete-btn' onclick='deleteFamily(" . $family['familyMemID'] . ")'>Delete</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Members Section -->
            <div id="members" class="section">
                <div class="section-header">
                    <h2 class="section-title">Club Member Management</h2>
                    <button class="btn" onclick="openModal('memberModal')">Add Member</button>
                </div>
                
                <div class="filters">
                    <div class="filter-group">
                        <label>Status:</label>
                        <select>
                            <option value="">All</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Age Group:</label>
                        <select>
                            <option value="">All</option>
                            <option value="major">Major (18+)</option>
                            <option value="minor">Minor (<18)</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Location:</label>
                        <select>
                            <option value="">All Locations</option>
                            <?php
                            $locations = getLocations($pdo);
                            foreach ($locations as $location) {
                                echo "<option value='" . $location['locationID'] . "'>" . htmlspecialchars($location['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                

                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>SSN</th>
                            <th>Medicare</th>
                            <th>DOB</th>
                            <th>Age Group</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Height</th>
                            <th>Weight</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $members = getMembers($pdo);
                        foreach ($members as $member) {
                            $dob = new DateTime($member['dob']);
                            $today = new DateTime();
                            $age = $today->diff($dob)->y;
                            $ageGroup = $age < 18 ? 'Minor' : 'Major';
                            
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) . "</td>";
                            echo "<td>" . htmlspecialchars($member['ssn']) . "</td>";
                            echo "<td>" . htmlspecialchars($member['medicare']) . "</td>";
                            echo "<td>" . htmlspecialchars($member['dob']) . "</td>";
                            echo "<td>" . $ageGroup . "</td>";
                            echo "<td>" . htmlspecialchars($member['phone']) . "</td>";
                            echo "<td>" . htmlspecialchars($member['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($member['address']) . "</td>";
                            echo "<td>" . htmlspecialchars($member['height'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($member['weight'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($member['location_name'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($member['status'] ?? 'Active') . "</td>";
                            echo "<td class='action-buttons'>";
                            echo "<button class='edit-btn' onclick='editMember(" . $member['memberID'] . ")'>Edit</button>";
                            echo "<button class='delete-btn' onclick='deleteMember(" . $member['memberID'] . ")'>Delete</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Sessions Section -->
            <div id="sessions" class="section">
                <div class="section-header">
                    <h2 class="section-title">Game & Training Sessions</h2>
                    <button class="btn" onclick="openModal('sessionModal')">Schedule Session</button>
                </div>
                
                <div class="filters">
                    <div class="filter-group">
                        <label>Type:</label>
                        <select id="sessionTypeFilter">
                            <option value="">All Types</option>
                            <option value="game">Game</option>
                            <option value="training">Training</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Location:</label>
                        <select id="locationFilter">
                            <option value="">All Locations</option>
                            <?php
                            $locations = getLocations($pdo);
                            foreach ($locations as $location) {
                                echo "<option value='" . htmlspecialchars($location['name']) . "'>" . htmlspecialchars($location['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Date From:</label>
                        <input type="date" id="dateFrom">
                    </div>
                    <div class="filter-group">
                        <label>Date To:</label>
                        <input type="date" id="dateTo">
                    </div>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Date & Time</th>
                            <th>Location</th>
                            <th>Teams</th>
                            <th>Coach</th>
                            <th>Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sessions = getSessions($pdo);
                        if (empty($sessions)) {
                            echo "<tr><td colspan='7' style='text-align: center;'>No sessions found.</td></tr>";
                        } else {
                            foreach ($sessions as $session) {
                                $score = $session['score'] ?? 'N/A';
                                $teams = '';
                                if ($session['team1_name'] && $session['team2_name']) {
                                    $teams = $session['team1_name'] . ' vs ' . $session['team2_name'];
                                } elseif ($session['team1_name']) {
                                    $teams = $session['team1_name'] . ' vs TBD';
                                } elseif ($session['team2_name']) {
                                    $teams = 'TBD vs ' . $session['team2_name'];
                                } else {
                                    $teams = 'TBD vs TBD';
                                }
                                
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($session['sessionType']) . "</td>";
                                echo "<td>" . htmlspecialchars($session['sessionDate'] . ' ' . $session['startTime']) . "</td>";
                                echo "<td>" . htmlspecialchars($session['location_name'] ?? 'N/A') . "</td>";
                                echo "<td>" . htmlspecialchars($teams) . "</td>";
                                echo "<td>" . htmlspecialchars($session['coach_name'] ?? 'N/A') . "</td>";
                                echo "<td>" . htmlspecialchars($score) . "</td>";
                                echo "<td class='action-buttons'>";
                                echo "<button class='edit-btn' onclick='editSession(" . $session['sessionID'] . ")'>Edit</button>";
                                echo "<button class='delete-btn' onclick='deleteSession(" . $session['sessionID'] . ")'>Delete</button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Teams Section -->
            <div id="teams" class="section">
                <div class="section-header">
                    <h2 class="section-title">Team Management</h2>
                    <button class="btn" onclick="openModal('teamModal')">Create Team</button>
                </div>
                

                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Team Name</th>
                            <th>Gender</th>
                            <th>Head Coach</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $teams = getTeams($pdo);
                        if (empty($teams)) {
                            echo "<tr><td colspan='5' style='text-align: center;'>No teams found.</td></tr>";
                        } else {
                            foreach ($teams as $team) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($team['teamName']) . "</td>";
                                echo "<td>" . htmlspecialchars($team['gender']) . "</td>";
                                echo "<td>" . htmlspecialchars($team['coach_name'] ?? 'N/A') . "</td>";
                                echo "<td>" . htmlspecialchars($team['location_name'] ?? 'N/A') . "</td>";
                                echo "<td class='action-buttons'>";
                                echo "<button class='edit-btn' onclick='editTeam(" . $team['teamID'] . ")'>Edit</button>";
                                echo "<button class='delete-btn' onclick='deleteTeam(" . $team['teamID'] . ")'>Delete</button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Payments Section -->
            <div id="payments" class="section">
                <div class="section-header">
                    <h2 class="section-title">Payment Management</h2>
                    <button class="btn" onclick="openModal('paymentModal')">Record Payment</button>
                </div>
                

                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Payment Date</th>
                            <th>Year</th>
                            <th>Installment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $payments = getYearlyPayments($pdo);
                        if (empty($payments)) {
                            echo "<tr><td colspan='7' style='text-align: center;'>No payments found.</td></tr>";
                        } else {
                            foreach ($payments as $payment) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($payment['firstName'] . ' ' . $payment['lastName']) . "</td>";
                                echo "<td>$" . number_format($payment['amount'], 2) . "</td>";
                                echo "<td>" . htmlspecialchars($payment['method']) . "</td>";
                                echo "<td>" . htmlspecialchars($payment['paymentDate']) . "</td>";
                                echo "<td>" . htmlspecialchars($payment['membershipYear']) . "</td>";
                                echo "<td>" . htmlspecialchars($payment['installmentNo']) . "</td>";
                                echo "<td class='action-buttons'>";
                                echo "<button class='edit-btn' onclick='editPayment(" . $payment['paymentID'] . ")'>Edit</button>";
                                echo "<button class='delete-btn' onclick='deletePayment(" . $payment['paymentID'] . ")'>Delete</button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div id="reports-section" class="section" style="display: none;">
            <!-- Reports Navigation Tabs -->
            <div id="reports-nav-tabs" class="reports-nav-tabs">
                <button class="nav-tab active" onclick="showReportSection('make-payment')">7. Make a Payment</button>
                <button class="nav-tab" onclick="showReportSection('location-info')">8. Location Info</button>
                <button class="nav-tab" onclick="showReportSection('secondary-family-info')">9. Secondary Family Info</button>
                <button class="nav-tab" onclick="showReportSection('question-10')">10. Team Formations</button>
                <button class="nav-tab" onclick="showReportSection('question-11')">11. Inactive Members Report</button>
                <button class="nav-tab" onclick="showReportSection('question-12')">12. Session Member Counter</button>
                <button class="nav-tab" onclick="showReportSection('question-13')">13. Unassigned Active Members</button>
                <button class="nav-tab" onclick="showReportSection('minors-to-majors')">14. Minors to Majors</button>
                <button class="nav-tab" onclick="showReportSection('goalkeepers-only')">15. Goalkeepers Only</button>
                <button class="nav-tab" onclick="showReportSection('allrounder-players')">16. All-rounder Players</button>
                <button class="nav-tab" onclick="showReportSection('question-17')">17. Question</button>
                <button class="nav-tab" onclick="showReportSection('question-18')">18. Question</button>
                <button class="nav-tab" onclick="showReportSection('question-19')">19. Question</button>
            </div>

            <!-- Report Sections -->
            
            <!-- 7. Make a Payment -->
            <?php include 'payment_system.php'; ?>

            <!-- 8. Location Info -->
            <div id="location-info" class="report-section">
                <?php include 'location_info_system.php'; ?>
            </div>

            <!-- 9. Secondary Family Info -->
            <div id="secondary-family-info" class="report-section">
                <?php include 'secondary_family_system.php'; ?>
            </div>

            <!-- 14. Minors to Majors -->
            <div id="minors-to-majors" class="report-section">
                <?php include 'minors_to_majors_system.php'; ?>
            </div>

            <!-- 15. Goalkeepers Only -->
            <div id="goalkeepers-only" class="report-section">
                <?php include 'goalkeepers_only_system.php'; ?>
            </div>

            <!-- 16. All-rounder Players -->
            <div id="allrounder-players" class="report-section">
                <?php include 'all_rounder_players_system.php'; ?>
            </div>

           <!---- Yana code-------->

<div id="question-10" class="report-section">
    <div class="section-header">
        <h2 class="section-title">Question 10 - Team Formations Report</h2>
    </div>
    
    <div class="report-form">
        <h4>Get team formation details for a specific location and time period</h4>
        <p>This report shows head coach details, session information, team names, scores, and player roles for all sessions at a given location within a specified time period.</p>
        
        <form id="question10Form" onsubmit="executeQuestion10(event)">
            <div class="form-grid">
                <div class="form-group">
                    <label for="q10_location">Location:</label>
                    <select id="q10_location" name="location_id" required>
                        <option value="">Select Location</option>
                        <?php
                        $locations = getLocations($pdo);
                        foreach ($locations as $location) {
                            echo "<option value='" . $location['locationID'] . "'>" . 
                                htmlspecialchars($location['name']) . " - " . htmlspecialchars($location['address']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="q10_start_date">Start Date:</label>
                    <input type="date" id="q10_start_date" name="start_date" value="2025-01-01" required>
                </div>
                
                <div class="form-group">
                    <label for="q10_end_date">End Date:</label>
                    <input type="date" id="q10_end_date" name="end_date" value="2025-05-31" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Generate Report</button>
            <button type="button" class="btn btn-secondary" onclick="clearQuestion10Results()">Clear Results</button>
        </form>
    </div>
    
    <div id="question10Results" class="report-results" style="display: none;">
        <h4>Team Formation Details</h4>
        <div id="question10Summary" class="alert alert-info" style="margin-bottom: 15px;"></div>
        
        <table class="data-table" id="question10Table">
            <thead>
                <tr>
                    <th>Team Name</th>
                    <th>Coach Name</th>
                    <th>Session Date</th>
                    <th>Start Time</th>
                    <th>Address</th>
                    <th>Session Type</th>
                    <th>Score</th>
                    <th>Player Name</th>
                    <th>Player Role</th>
                </tr>
            </thead>
            <tbody id="question10TableBody">
                <!-- Results will be populated here -->
            </tbody>
        </table>
    </div>
    
    <div id="question10Error" class="alert alert-error" style="display: none;">
        <!-- Error messages will appear here -->
    </div>
</div>

<script>
function executeQuestion10(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const locationId = formData.get('location_id');
    const startDate = formData.get('start_date');
    const endDate = formData.get('end_date');
    
    // Validate dates
    if (new Date(startDate) > new Date(endDate)) {
        showQuestion10Error('Start date must be before or equal to end date.');
        return;
    }
    
    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Generating Report...';
    submitBtn.disabled = true;
    
    // Hide previous results and errors
    document.getElementById('question10Results').style.display = 'none';
    document.getElementById('question10Error').style.display = 'none';
    
    // Create form data for POST request
    const postData = new FormData();
    postData.append('action', 'execute_question_10');
    postData.append('location_id', locationId);
    postData.append('start_date', startDate);


    
<div id="question-10" class="report-section">
    <div class="section-header">
        <h2 class="section-title">Question 10 - Team Formations Report</h2>
    </div>
    
    <div class="report-form">
        <h4>Get team formation details for a specific location and time period</h4>
        <p>This report shows head coach details, session information, team names, scores, and player roles for all sessions at a given location within a specified time period.</p>
        
        <form id="question10Form" onsubmit="executeQuestion10(event)">
            <div class="form-grid">
                <div class="form-group">
                    <label for="q10_location">Location:</label>
                    <select id="q10_location" name="location_id" required>
                        <option value="">Select Location</option>
                        <?php
                        $locations = getLocations($pdo);
                        foreach ($locations as $location) {
                            echo "<option value='" . $location['locationID'] . "'>" . 
                                htmlspecialchars($location['name']) . " - " . htmlspecialchars($location['address']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="q10_start_date">Start Date:</label>
                    <input type="date" id="q10_start_date" name="start_date" value="2025-01-01" required>
                </div>
                
                <div class="form-group">
                    <label for="q10_end_date">End Date:</label>
                    <input type="date" id="q10_end_date" name="end_date" value="2025-05-31" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Generate Report</button>
            <button type="button" class="btn btn-secondary" onclick="clearQuestion10Results()">Clear Results</button>
        </form>
    </div>
    
    <div id="question10Results" class="report-results" style="display: none;">
        <h4>Team Formation Details</h4>
        <div id="question10Summary" class="alert alert-info" style="margin-bottom: 15px;"></div>
        
        <table class="data-table" id="question10Table">
            <thead>
                <tr>
                    <th>Team Name</th>
                    <th>Coach Name</th>
                    <th>Session Date</th>
                    <th>Start Time</th>
                    <th>Address</th>
                    <th>Session Type</th>
                    <th>Score</th>
                    <th>Player Name</th>
                    <th>Player Role</th>
                </tr>
            </thead>
            <tbody id="question10TableBody">
                <!-- Results will be populated here -->
            </tbody>
        </table>
    </div>
    
    <div id="question10Error" class="alert alert-error" style="display: none;">
        <!-- Error messages will appear here -->
    </div>
</div>

<script>
function executeQuestion10(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const locationId = formData.get('location_id');
    const startDate = formData.get('start_date');
    const endDate = formData.get('end_date');
    
    // Validate dates
    if (new Date(startDate) > new Date(endDate)) {
        showQuestion10Error('Start date must be before or equal to end date.');
        return;
    }
    
    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Generating Report...';
    submitBtn.disabled = true;
    
    // Hide previous results and errors
    document.getElementById('question10Results').style.display = 'none';
    document.getElementById('question10Error').style.display = 'none';
    
    // Create form data for POST request
    const postData = new FormData();
    postData.append('action', 'execute_question_10');
    postData.append('location_id', locationId);
    postData.append('start_date', startDate);
    postData.append('end_date', endDate);
    
    fetch('query_handler.php', {
        method: 'POST',
        body: postData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayQuestion10Results(data.results, data.summary);
        } else {
            showQuestion10Error(data.message || 'An error occurred while executing the query.');
        }
    })
    .catch(error => {
        showQuestion10Error('Network error: ' + error.message);
    })
    .finally(() => {
        // Restore button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

function displayQuestion10Results(results, summary) {
    const resultsDiv = document.getElementById('question10Results');
    const tableBody = document.getElementById('question10TableBody');
    const summaryDiv = document.getElementById('question10Summary');
    
    // Clear previous results
    tableBody.innerHTML = '';
    
    if (results.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="9" style="text-align: center; font-style: italic;">No team formations found for the specified location and time period.</td></tr>';
    } else {
        results.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${escapeHtml(row.teamName || 'N/A')}</td>
                <td>${escapeHtml((row.coachFirstName || '') + ' ' + (row.coachLastName || ''))}</td>
                <td>${escapeHtml(row.sessionDate || 'N/A')}</td>
                <td>${escapeHtml(row.startTime || 'N/A')}</td>
                <td>${escapeHtml(row.address || 'N/A')}</td>
                <td>${escapeHtml(row.sessionType || 'N/A')}</td>
                <td>${row.score !== null ? escapeHtml(row.score) : 'TBD'}</td>
                <td>${escapeHtml((row.playerFirstName || '') + ' ' + (row.playerLastName || ''))}</td>
                <td>${escapeHtml(row.roleInTeam || 'N/A')}</td>
            `;
            tableBody.appendChild(tr);
        });
    }
    
    // Update summary
    summaryDiv.textContent = summary || `Found ${results.length} team formation records.`;
    
    // Show results
    resultsDiv.style.display = 'block';
}

function showQuestion10Error(message) {
    const errorDiv = document.getElementById('question10Error');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
}

function clearQuestion10Results() {
    document.getElementById('question10Results').style.display = 'none';
    document.getElementById('question10Error').style.display = 'none';
    document.getElementById('question10Form').reset();
    
    // Reset form to default values
    document.getElementById('q10_start_date').value = '2025-01-01';
    document.getElementById('q10_end_date').value = '2025-05-31';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<!-- Q10 ENDS HERE -->

<!-- Q11 STARTS HERE -->

            <div id="question-11" class="report-section">
    <div class="section-header">
        <h2 class="section-title">Question 11 - Inactive Members with Multi-Location History</h2>
    </div>
    
    <div class="report-form">
        <h4>Get details of inactive club members with multi-location experience</h4>
        <p>This report shows club members who are currently inactive and have been associated with at least two different locations and are members for at least two years. Results include Club membership number, first name and last name, sorted by membership number.</p>
        
        <form id="question11Form" onsubmit="executeQuestion11(event)">
            <button type="submit" class="btn btn-primary">Generate Report</button>
            <button type="button" class="btn btn-secondary" onclick="clearQuestion11Results()">Clear Results</button>
        </form>
    </div>
    
    <div id="question11Results" class="report-results" style="display: none;">
        <h4>Inactive Members with Multi-Location History</h4>
        <div id="question11Summary" class="alert alert-info" style="margin-bottom: 15px;"></div>
        
        <table class="data-table" id="question11Table">
            <thead>
                <tr>
                    <th>Club Membership Number</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                </tr>
            </thead>
            <tbody id="question11TableBody">
                <!-- Results will be populated here -->
            </tbody>
        </table>
    </div>
    
    <div id="question11Error" class="alert alert-error" style="display: none;">
        <!-- Error messages will appear here -->
    </div>
</div>

<script>
function executeQuestion11(event) {
    event.preventDefault();
    
    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Generating Report...';
    submitBtn.disabled = true;
    
    // Hide previous results and errors
    document.getElementById('question11Results').style.display = 'none';
    document.getElementById('question11Error').style.display = 'none';
    
    // Create form data for POST request
    const postData = new FormData();
    postData.append('action', 'execute_question_11');
    
    fetch('query_handler.php', {
        method: 'POST',
        body: postData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayQuestion11Results(data.results, data.summary);
        } else {
            showQuestion11Error(data.message || 'An error occurred while executing the query.', data.suggestion);
        }
    })
    .catch(error => {
        showQuestion11Error('Network error: ' + error.message);
    })
    .finally(() => {
        // Restore button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

function displayQuestion11Results(results, summary) {
    const resultsDiv = document.getElementById('question11Results');
    const tableBody = document.getElementById('question11TableBody');
    const summaryDiv = document.getElementById('question11Summary');
    
    // Clear previous results
    tableBody.innerHTML = '';
    
    if (results.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="3" style="text-align: center; font-style: italic;">No inactive members found matching the criteria (at least 2 locations, member for at least 2 years).</td></tr>';
    } else {
        results.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${escapeHtml(row.memberNumber || 'N/A')}</td>
                <td>${escapeHtml(row.firstName || 'N/A')}</td>
                <td>${escapeHtml(row.lastName || 'N/A')}</td>
            `;
            tableBody.appendChild(tr);
        });
    }
    
    // Update summary
    summaryDiv.textContent = summary || `Found ${results.length} inactive members with multi-location history.`;
    
    // Show results
    resultsDiv.style.display = 'block';
}

function showQuestion11Error(message, suggestion) {
    const errorDiv = document.getElementById('question11Error');
    let errorContent = message;
    
    if (suggestion) {
        errorContent += '<br><br><strong>Suggestion:</strong> ' + suggestion;
    }
    
    errorDiv.innerHTML = errorContent;
    errorDiv.style.display = 'block';
}

function clearQuestion11Results() {
    document.getElementById('question11Results').style.display = 'none';
    document.getElementById('question11Error').style.display = 'none';
}

// Utility function to escape HTML (if not already defined)
if (typeof escapeHtml === 'undefined') {
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}
</script>

<!-- Q11 END HERE!!!! -->

<!-- Q12 STARTS HERE !!! -->

            <!-- Replace the question-12 section in your index.php with this code -->

<div id="question-12" class="report-section">
    <div class="section-header">
        <h2 class="section-title">Question 12 - Team Formations Report by Location</h2>
    </div>
    
    <div class="report-form">
        <h4>Get team formations report for all locations during a specific period</h4>
        <p>This report shows team formations for all locations within a given time period. For each location, it includes the location name, total training sessions, total training players, total game sessions, and total game players. Results only include locations with at least 4 game sessions, sorted by total game sessions (descending).</p>
        
        <form id="question12Form" onsubmit="executeQuestion12(event)">
            <div class="form-grid">
                <div class="form-group">
                    <label for="q12_start_date">Start Date:</label>
                    <input type="date" id="q12_start_date" name="start_date" value="2025-01-01" required>
                </div>
                
                <div class="form-group">
                    <label for="q12_end_date">End Date:</label>
                    <input type="date" id="q12_end_date" name="end_date" value="2025-05-31" required>
                </div>
            </div>
            
            <div style="margin-top: 15px;">
                <button type="submit" class="btn btn-primary">Generate Report</button>
                <button type="button" class="btn btn-secondary" onclick="clearQuestion12Results()">Clear Results</button>
            </div>
        </form>
    </div>
    
    <div id="question12Results" class="report-results" style="display: none;">
        <h4>Team Formations Report by Location</h4>
        <div id="question12Summary" class="alert alert-info" style="margin-bottom: 15px;"></div>
        
        <!-- Summary Statistics -->
        <div id="question12Stats" class="placeholder-content" style="margin-bottom: 20px;">
            <div class="placeholder-box">
                <h4 id="statsLocations">0</h4>
                <p>Qualifying Locations</p>
            </div>
            <div class="placeholder-box">
                <h4 id="statsGameSessions">0</h4>
                <p>Total Game Sessions</p>
            </div>
            <div class="placeholder-box">
                <h4 id="statsTrainingSessions">0</h4>
                <p>Total Training Sessions</p>
            </div>
            <div class="placeholder-box">
                <h4 id="statsGamePlayers">0</h4>
                <p>Total Game Players</p>
            </div>
            <div class="placeholder-box">
                <h4 id="statsTrainingPlayers">0</h4>
                <p>Total Training Players</p>
            </div>
        </div>
        
        <table class="data-table" id="question12Table">
            <thead>
                <tr>
                    <th>Location Name</th>
                    <th>Training Sessions</th>
                    <th>Training Players</th>
                    <th>Game Sessions</th>
                    <th>Game Players</th>
                    <th>Total Sessions</th>
                    <th>Total Players</th>
                </tr>
            </thead>
            <tbody id="question12TableBody">
                <!-- Results will be populated here -->
            </tbody>
        </table>
    </div>
    
    <div id="question12Error" class="alert alert-error" style="display: none;">
        <!-- Error messages will appear here -->
    </div>
</div>

<script>
function executeQuestion12(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const startDate = formData.get('start_date');
    const endDate = formData.get('end_date');
    
    // Validate dates
    if (new Date(startDate) > new Date(endDate)) {
        showQuestion12Error('Start date must be before or equal to end date.');
        return;
    }
    
    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Generating Report...';
    submitBtn.disabled = true;
    
    // Hide previous results and errors
    document.getElementById('question12Results').style.display = 'none';
    document.getElementById('question12Error').style.display = 'none';
    
    // Create form data for POST request
    const postData = new FormData();
    postData.append('action', 'execute_question_12');
    postData.append('start_date', startDate);
    postData.append('end_date', endDate);
    
    fetch('query_handler.php', {
        method: 'POST',
        body: postData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayQuestion12Results(data.results, data.summary, data.stats);
        } else {
            showQuestion12Error(data.message || 'An error occurred while executing the query.', data.suggestion);
        }
    })
    .catch(error => {
        showQuestion12Error('Network error: ' + error.message);
    })
    .finally(() => {
        // Restore button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

function displayQuestion12Results(results, summary, stats) {
    const resultsDiv = document.getElementById('question12Results');
    const tableBody = document.getElementById('question12TableBody');
    const summaryDiv = document.getElementById('question12Summary');
    
    // Clear previous results
    tableBody.innerHTML = '';
    
    // Update statistics boxes
    if (stats) {
        document.getElementById('statsLocations').textContent = stats.totalLocations || 0;
        document.getElementById('statsGameSessions').textContent = stats.totalGameSessions || 0;
        document.getElementById('statsTrainingSessions').textContent = stats.totalTrainingSessions || 0;
        document.getElementById('statsGamePlayers').textContent = stats.totalGamePlayers || 0;
        document.getElementById('statsTrainingPlayers').textContent = stats.totalTrainingPlayers || 0;
    }
    
    if (results.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="7" style="text-align: center; font-style: italic;">No locations found with at least 4 game sessions in the specified period.</td></tr>';
    } else {
        results.forEach(row => {
            const totalSessions = parseInt(row.totalTrainingSessions || 0) + parseInt(row.totalGameSessions || 0);
            const totalPlayers = parseInt(row.totalTrainingPlayers || 0) + parseInt(row.totalGamePlayers || 0);
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${escapeHtml(row.locationName || 'N/A')}</strong></td>
                <td>${row.totalTrainingSessions || 0}</td>
                <td>${row.totalTrainingPlayers || 0}</td>
                <td><strong>${row.totalGameSessions || 0}</strong></td>
                <td><strong>${row.totalGamePlayers || 0}</strong></td>
                <td>${totalSessions}</td>
                <td>${totalPlayers}</td>
            `;
            tableBody.appendChild(tr);
        });
    }
    
    // Update summary
    summaryDiv.textContent = summary || `Found ${results.length} locations with team formation data.`;
    
    // Show results
    resultsDiv.style.display = 'block';
}

function showQuestion12Error(message, suggestion) {
    const errorDiv = document.getElementById('question12Error');
    let errorContent = message;
    
    if (suggestion) {
        errorContent += '<br><br><strong>Suggestion:</strong> ' + suggestion;
    }
    
    errorDiv.innerHTML = errorContent;
    errorDiv.style.display = 'block';
}

function clearQuestion12Results() {
    document.getElementById('question12Results').style.display = 'none';
    document.getElementById('question12Error').style.display = 'none';
    document.getElementById('question12Form').reset();
    
    // Reset form to default values
    document.getElementById('q12_start_date').value = '2025-01-01';
    document.getElementById('q12_end_date').value = '2025-05-31';
    
    // Reset statistics boxes
    document.getElementById('statsLocations').textContent = '0';
    document.getElementById('statsGameSessions').textContent = '0';
    document.getElementById('statsTrainingSessions').textContent = '0';
    document.getElementById('statsGamePlayers').textContent = '0';
    document.getElementById('statsTrainingPlayers').textContent = '0';
}

// Utility function to escape HTML (if not already defined)
if (typeof escapeHtml === 'undefined') {
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}
</script>

    <!-- Q12 ENDS HERE !!! -->

    <!-- Q13 STARTS HERE !!! -->

            <!-- Replace the question-13 section in your index.php with this code -->

<div id="question-13" class="report-section">
    <div class="section-header">
        <h2 class="section-title">Question 13 - Unassigned Active Members</h2>
    </div>
    
    <div class="report-form">
        <h4>Get report on active club members never assigned to any team formation</h4>
        <p>This report shows all active club members who have never been assigned to any team session. The list includes membership number, personal details, and current location, sorted by location name then by age.</p>
        
        <form id="question13Form" onsubmit="executeQuestion13(event)">
            <button type="submit" class="btn btn-primary">Generate Report</button>
            <button type="button" class="btn btn-secondary" onclick="clearQuestion13Results()">Clear Results</button>
        </form>
    </div>
    
    <div id="question13Results" class="report-results" style="display: none;">
        <h4>Active Members Never Assigned to Teams</h4>
        <div id="question13Summary" class="alert alert-info" style="margin-bottom: 15px;"></div>
        
        <!-- Summary Statistics -->
        <div id="question13Stats" class="placeholder-content" style="margin-bottom: 20px;">
            <div class="placeholder-box">
                <h4 id="totalUnassignedMembers">0</h4>
                <p>Unassigned Members</p>
            </div>
            <div class="placeholder-box">
                <h4 id="locationsAffected">0</h4>
                <p>Locations Affected</p>
            </div>
            <div class="placeholder-box">
                <h4 id="avgAge">0</h4>
                <p>Average Age</p>
            </div>
            <div class="placeholder-box">
                <h4 id="youngestAge">0</h4>
                <p>Youngest Member</p>
            </div>
            <div class="placeholder-box">
                <h4 id="oldestAge">0</h4>
                <p>Oldest Member</p>
            </div>
        </div>
        
        <!-- Age Distribution -->
        <div id="ageDistribution" style="margin-bottom: 20px;">
            <h4>Age Distribution</h4>
            <div class="placeholder-content">
                <div class="placeholder-box">
                    <h4 id="ageUnder18">0</h4>
                    <p>Under 18</p>
                </div>
                <div class="placeholder-box">
                    <h4 id="age18to25">0</h4>
                    <p>18-25</p>
                </div>
                <div class="placeholder-box">
                    <h4 id="age26to35">0</h4>
                    <p>26-35</p>
                </div>
                <div class="placeholder-box">
                    <h4 id="age36to50">0</h4>
                    <p>36-50</p>
                </div>
                <div class="placeholder-box">
                    <h4 id="ageOver50">0</h4>
                    <p>Over 50</p>
                </div>
            </div>
        </div>
        
        <!-- Filter Options -->
        <div class="filters" style="margin-bottom: 15px;">
            <div class="filter-group">
                <label>Filter by Location:</label>
                <select id="locationFilter" onchange="filterQuestion13Results()">
                    <option value="">All Locations</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Filter by Age Group:</label>
                <select id="ageGroupFilter" onchange="filterQuestion13Results()">
                    <option value="">All Ages</option>
                    <option value="under18">Under 18</option>
                    <option value="18-25">18-25</option>
                    <option value="26-35">26-35</option>
                    <option value="36-50">36-50</option>
                    <option value="over50">Over 50</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Search by Name:</label>
                <input type="text" id="nameSearch" placeholder="First or last name..." onkeyup="filterQuestion13Results()">
            </div>
        </div>
        
        <table class="data-table" id="question13Table">
            <thead>
                <tr>
                    <th>Membership #</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Age</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody id="question13TableBody">
                <!-- Results will be populated here -->
            </tbody>
        </table>
        
        <div id="filteredCount" style="margin-top: 10px; font-style: italic; color: #666;">
            <!-- Filtered count will appear here -->
        </div>
    </div>
    
    <div id="question13Error" class="alert alert-error" style="display: none;">
        <!-- Error messages will appear here -->
    </div>
</div>

<script>
let question13Data = []; // Store the original data for filtering

function executeQuestion13(event) {
    event.preventDefault();
    
    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Generating Report...';
    submitBtn.disabled = true;
    
    // Hide previous results and errors
    document.getElementById('question13Results').style.display = 'none';
    document.getElementById('question13Error').style.display = 'none';
    
    // Create form data for POST request
    const postData = new FormData();
    postData.append('action', 'execute_question_13');
    
    fetch('query_handler.php', {
        method: 'POST',
        body: postData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            question13Data = data.results; // Store for filtering
            displayQuestion13Results(data.results, data.summary, data.stats);
        } else {
            showQuestion13Error(data.message || 'An error occurred while executing the query.', data.suggestion);
        }
    })
    .catch(error => {
        showQuestion13Error('Network error: ' + error.message);
    })
    .finally(() => {
        // Restore button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

function displayQuestion13Results(results, summary, stats) {
    const resultsDiv = document.getElementById('question13Results');
    const summaryDiv = document.getElementById('question13Summary');
    
    // Update summary
    summaryDiv.textContent = summary || `Found ${results.length} unassigned active members.`;
    
    // Update statistics
    if (stats) {
        document.getElementById('totalUnassignedMembers').textContent = stats.totalMembers || 0;
        document.getElementById('locationsAffected').textContent = stats.locationsAffected || 0;
        
        // Calculate age statistics
        const ages = results.map(r => parseInt(r.age));
        const avgAge = ages.length > 0 ? Math.round(ages.reduce((a, b) => a + b, 0) / ages.length) : 0;
        const youngestAge = ages.length > 0 ? Math.min(...ages) : 0;
        const oldestAge = ages.length > 0 ? Math.max(...ages) : 0;
        
        document.getElementById('avgAge').textContent = avgAge;
        document.getElementById('youngestAge').textContent = youngestAge;
        document.getElementById('oldestAge').textContent = oldestAge;
        
        // Update age distribution
        document.getElementById('ageUnder18').textContent = stats.ageGroups['Under 18'] || 0;
        document.getElementById('age18to25').textContent = stats.ageGroups['18-25'] || 0;
        document.getElementById('age26to35').textContent = stats.ageGroups['26-35'] || 0;
        document.getElementById('age36to50').textContent = stats.ageGroups['36-50'] || 0;
        document.getElementById('ageOver50').textContent = stats.ageGroups['Over 50'] || 0;
        
        // Populate location filter
        const locationFilter = document.getElementById('locationFilter');
        locationFilter.innerHTML = '<option value="">All Locations</option>';
        Object.keys(stats.locationCount || {}).sort().forEach(location => {
            locationFilter.innerHTML += `<option value="${escapeHtml(location)}">${escapeHtml(location)} (${stats.locationCount[location]})</option>`;
        });
    }
    
    // Display table data
    updateQuestion13Table(results);
    
    // Show results
    resultsDiv.style.display = 'block';
}

function updateQuestion13Table(results) {
    const tableBody = document.getElementById('question13TableBody');
    const filteredCount = document.getElementById('filteredCount');
    
    // Clear previous results
    tableBody.innerHTML = '';
    
    if (results.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="7" style="text-align: center; font-style: italic;">No unassigned active members found matching the criteria.</td></tr>';
        filteredCount.textContent = '';
    } else {
        results.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${escapeHtml(row.membershipNumber || 'N/A')}</strong></td>
                <td>${escapeHtml(row.firstName || 'N/A')}</td>
                <td>${escapeHtml(row.lastName || 'N/A')}</td>
                <td>${escapeHtml(row.age || 'N/A')}</td>
                <td>${escapeHtml(row.phone || 'N/A')}</td>
                <td>${escapeHtml(row.email || 'N/A')}</td>
                <td>${escapeHtml(row.locationName || 'N/A')}</td>
            `;
            tableBody.appendChild(tr);
        });
        
        // Update filtered count
        if (results.length < question13Data.length) {
            filteredCount.textContent = `Showing ${results.length} of ${question13Data.length} members`;
        } else {
            filteredCount.textContent = '';
        }
    }
}

function filterQuestion13Results() {
    const locationFilter = document.getElementById('locationFilter').value;
    const ageGroupFilter = document.getElementById('ageGroupFilter').value;
    const nameSearch = document.getElementById('nameSearch').value.toLowerCase();
    
    let filteredResults = question13Data.filter(row => {
        // Location filter
        if (locationFilter && row.locationName !== locationFilter) {
            return false;
        }
        
        // Age group filter
        if (ageGroupFilter) {
            const age = parseInt(row.age);
            switch (ageGroupFilter) {
                case 'under18':
                    if (age >= 18) return false;
                    break;
                case '18-25':
                    if (age < 18 || age > 25) return false;
                    break;
                case '26-35':
                    if (age < 26 || age > 35) return false;
                    break;
                case '36-50':
                    if (age < 36 || age > 50) return false;
                    break;
                case 'over50':
                    if (age <= 50) return false;
                    break;
            }
        }
        
        // Name search
        if (nameSearch) {
            const firstName = (row.firstName || '').toLowerCase();
            const lastName = (row.lastName || '').toLowerCase();
            const fullName = firstName + ' ' + lastName;
            if (!firstName.includes(nameSearch) && !lastName.includes(nameSearch) && !fullName.includes(nameSearch)) {
                return false;
            }
        }
        
        return true;
    });
    
    updateQuestion13Table(filteredResults);
}

function showQuestion13Error(message, suggestion) {
    const errorDiv = document.getElementById('question13Error');
    let errorContent = message;
    
    if (suggestion) {
        errorContent += '<br><br><strong>Suggestion:</strong> ' + suggestion;
    }
    
    errorDiv.innerHTML = errorContent;
    errorDiv.style.display = 'block';
}

function clearQuestion13Results() {
    document.getElementById('question13Results').style.display = 'none';
    document.getElementById('question13Error').style.display = 'none';
    question13Data = [];
    
    // Clear filters
    document.getElementById('locationFilter').value = '';
    document.getElementById('ageGroupFilter').value = '';
    document.getElementById('nameSearch').value = '';
    
    // Reset statistics
    document.getElementById('totalUnassignedMembers').textContent = '0';
    document.getElementById('locationsAffected').textContent = '0';
    document.getElementById('avgAge').textContent = '0';
    document.getElementById('youngestAge').textContent = '0';
    document.getElementById('oldestAge').textContent = '0';
    
    // Reset age distribution
    document.getElementById('ageUnder18').textContent = '0';
    document.getElementById('age18to25').textContent = '0';
    document.getElementById('age26to35').textContent = '0';
    document.getElementById('age36to50').textContent = '0';
    document.getElementById('ageOver50').textContent = '0';
}

// Utility function to escape HTML (if not already defined)
if (typeof escapeHtml === 'undefined') {
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}
</script>


           <div id="question-17" class="report-section">
                <div class="section-header">
                    <h2 class="section-title">Qualified Family Members (Q17)</h2>
                </div>

                <div class="report-form">
                    <label for="q17-location">Select Location:</label>
                    <select id="q17-location">
                        <option value="">-- Select Location --</option>
                        <?php
                        $locations = getLocations($pdo);
                        foreach ($locations as $loc) {
                            echo "<option value='{$loc['locationID']}'>" . htmlspecialchars($loc['name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div id="q17-table-container" class="report-results" style="display: none;"></div>
            </div>


            <div id="question-18" class="report-section">
                <div class="section-header">
                    <h2 class="section-title">Active Game Winners (Q18)</h2>
                    <button class="btn" onclick="loadQ18Results()">Load Results</button>
                </div>
                <div id="q18-table-container" class="report-results" style="display: none;"></div>
            </div>


            <div id="question-19" class="report-section">
                <div class="section-header">
                    <h2 class="section-title">Volunteer Family Supervisors (Q19)</h2>
                    <button class="btn" onclick="loadQ19Results()">Load Results</button>
                </div>
                <div id="q19-table-container" class="report-results" style="display: none;"></div>
            </div>
        </div>            
    </div>
    

    <!-- Include the modals and JavaScript -->
    <?php include 'modals.php'; ?>
    <?php include 'script.php'; ?>
</body>
</html> 