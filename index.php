<?php
// Start session at the very beginning
session_start();

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
    die("Connection failed: " . $e->getMessage());
}

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
                                   per.address, per.postalCode, per.phone, per.email, l.name as location_name 
                            FROM FamilyMember fm 
                            LEFT JOIN Person per ON fm.familyMemID = per.pID 
                            LEFT JOIN Location l ON fm.familyMemID = l.locationID 
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

            .payment-form-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
                margin-bottom: 20px;
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
                <button class="nav-tab" onclick="showReportSection('question-10')">10. Question</button>
                <button class="nav-tab" onclick="showReportSection('question-11')">11. Question</button>
                <button class="nav-tab" onclick="showReportSection('question-12')">12. Question</button>
                <button class="nav-tab" onclick="showReportSection('question-13')">13. Question</button>
                <button class="nav-tab" onclick="showReportSection('minors-to-majors')">14. Minors to Majors</button>
                <button class="nav-tab" onclick="showReportSection('goalkeepers-only')">15. Goalkeepers Only</button>
                <button class="nav-tab" onclick="showReportSection('allrounder-players')">16. All-rounder Players</button>
                <button class="nav-tab" onclick="showReportSection('question-17')">17. Question</button>
                <button class="nav-tab" onclick="showReportSection('question-18')">18. Question</button>
                <button class="nav-tab" onclick="showReportSection('question-19')">19. Question</button>
            </div>

            <!-- Report Sections -->
            
            <!-- 7. Make a Payment -->
            <div id="make-payment" class="report-section" style="display: block;">
                <div class="section-header">
                    <h2 class="section-title">Make a Payment</h2>
                </div>
                <div class="report-form">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="save_payment">
                        <div class="payment-form-grid">
                            <div class="form-group">
                                <label>Member:</label>
                                <select name="member_id" required>
                                    <option value="">Select Member</option>
                                    <?php
                                    $members = getMembers($pdo);
                                    foreach ($members as $member) {
                                        echo "<option value='" . $member['memberID'] . "'>" . 
                                            htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Amount:</label>
                                <input type="number" name="amount" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label>Payment Method:</label>
                                <select name="payment_method" required>
                                    <option value="">Select Method</option>
                                    <option value="cash">Cash</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="debit_card">Debit Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Payment Date:</label>
                                <input type="date" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Membership Year:</label>
                                <input type="number" name="year" value="<?php echo date('Y'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Installment No:</label>
                                <input type="number" name="installment_no" value="1" min="1" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Process Payment</button>
                    </form>
                </div>
            </div>

            <!-- 8. Location Info -->
            <div id="location-info" class="report-section">
                <div class="section-header">
                    <h2 class="section-title">Location Information Report</h2>
                </div>
                <div class="report-form">
                    <div class="form-group">
                        <label>Filter by Location Type:</label>
                        <select id="locationTypeFilter" onchange="filterLocationInfo()">
                            <option value="">All Types</option>
                            <option value="main">Main</option>
                            <option value="secondary">Secondary</option>
                        </select>
                    </div>
                </div>
                <div class="report-results">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Location Name</th>
                                <th>Type</th>
                                <th>Address</th>
                                <th>Postal Code</th>
                                <th>Max Capacity</th>
                                <th>Web Address</th>
                                <th>General Manager</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $locations = getLocations($pdo);
                            foreach ($locations as $location) {
                                echo "<tr class='location-row' data-type='" . strtolower($location['type']) . "'>";
                                echo "<td>" . htmlspecialchars($location['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($location['type']) . "</td>";
                                echo "<td>" . htmlspecialchars($location['address']) . "</td>";
                                echo "<td>" . htmlspecialchars($location['postalCode']) . "</td>";
                                echo "<td>" . htmlspecialchars($location['maxCapacity']) . "</td>";
                                echo "<td>" . htmlspecialchars($location['webAddress'] ?: 'N/A') . "</td>";
                                echo "<td>" . htmlspecialchars($location['managerID'] ?: 'N/A') . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 9. Secondary Family Info -->
            <div id="secondary-family-info" class="report-section">
                <div class="section-header">
                    <h2 class="section-title">Secondary Family Information</h2>
                </div>
                <div class="report-results">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Family Member Name</th>
                                <th>Relationship Type</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT fm.*, per.firstName, per.lastName, per.phone, per.email, per.address 
                                                FROM FamilyMember fm 
                                                LEFT JOIN Person per ON fm.familyMemID = per.pID 
                                                WHERE fm.primarySecondaryRelationship = 'secondary'
                                                ORDER BY per.lastName");
                                $secondaryFamily = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (empty($secondaryFamily)) {
                                    echo "<tr><td colspan='5' style='text-align: center;'>No secondary family members found.</td></tr>";
                                } else {
                                    foreach ($secondaryFamily as $family) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($family['firstName'] . ' ' . $family['lastName']) . "</td>";
                                        echo "<td>" . htmlspecialchars($family['primarySecondaryRelationship']) . "</td>";
                                        echo "<td>" . htmlspecialchars($family['phone']) . "</td>";
                                        echo "<td>" . htmlspecialchars($family['email']) . "</td>";
                                        echo "<td>" . htmlspecialchars($family['address']) . "</td>";
                                        echo "</tr>";
                                    }
                                }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='5' style='text-align: center; color: red;'>Error loading data.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 14. Minors to Majors -->
            <div id="minors-to-majors" class="report-section">
                <div class="section-header">
                    <h2 class="section-title">Members Transitioning from Minor to Major</h2>
                </div>
                <div class="report-results">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Member Name</th>
                                <th>Date of Birth</th>
                                <th>Current Age</th>
                                <th>Transition Date (18th Birthday)</th>
                                <th>Current Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT cm.*, per.firstName, per.lastName, per.dob 
                                                FROM ClubMember cm 
                                                LEFT JOIN Person per ON cm.memberID = per.pID 
                                                WHERE DATEDIFF(CURDATE(), per.dob) / 365.25 BETWEEN 17 AND 18
                                                ORDER BY per.dob DESC");
                                $transitionMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (empty($transitionMembers)) {
                                    echo "<tr><td colspan='5' style='text-align: center;'>No members currently transitioning from minor to major.</td></tr>";
                                } else {
                                    foreach ($transitionMembers as $member) {
                                        $dob = new DateTime($member['dob']);
                                        $today = new DateTime();
                                        $age = $today->diff($dob)->y;
                                        $eighteenthBirthday = clone $dob;
                                        $eighteenthBirthday->add(new DateInterval('P18Y'));
                                        
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) . "</td>";
                                        echo "<td>" . htmlspecialchars($member['dob']) . "</td>";
                                        echo "<td>" . $age . "</td>";
                                        echo "<td>" . $eighteenthBirthday->format('Y-m-d') . "</td>";
                                        echo "<td>" . htmlspecialchars($member['memberType']) . "</td>";
                                        echo "</tr>";
                                    }
                                }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='5' style='text-align: center; color: red;'>Error loading data.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 15. Goalkeepers Only -->
            <div id="goalkeepers-only" class="report-section">
                <div class="section-header">
                    <h2 class="section-title">Goalkeepers Report</h2>
                </div>
                <div class="coming-soon">
                    <h4>Goalkeepers Report</h4>
                    <p>This report will show all members who play as goalkeepers. Position tracking feature coming soon.</p>
                </div>
            </div>

            <!-- 16. All-rounder Players -->
            <div id="allrounder-players" class="report-section">
                <div class="section-header">
                    <h2 class="section-title">All-rounder Players</h2>
                </div>
                <div class="coming-soon">
                    <h4>All-rounder Players Report</h4>
                    <p>This report will show players who can play multiple positions. Position tracking feature coming soon.</p>
                </div>
            </div>

            <!-- Placeholder sections for other questions -->
            <div id="question-10" class="report-section">
                <div class="section-header">
                    <h2 class="section-title">Question 10</h2>
                </div>
                <div class="coming-soon">
                    <h4>Coming Soon</h4>
                    <p>This report section will be implemented based on specific requirements.</p>
                </div>
            </div>

            <div id="question-11" class="report-section">
                <div class="section-header">
                    <h2 class="section-title">Question 11</h2>
                </div>
                <div class="coming-soon">
                    <h4>Coming Soon</h4>
                    <p>This report section will be implemented based on specific requirements.</p>
                </div>
            </div>

            <div id="question-12" class="report-section">
                <div class="section-header">
                    <h2 class="section-title">Question 12</h2>
                </div>
                <div class="coming-soon">
                    <h4>Coming Soon</h4>
                    <p>This report section will be implemented based on specific requirements.</p>
                </div>
            </div>

            <div id="question-13" class="report-section">
                <div class="section-header">
                    <h2 class="section-title">Question 13</h2>
                </div>
                <div class="coming-soon">
                    <h4>Coming Soon</h4>
                    <p>This report section will be implemented based on specific requirements.</p>
                </div>
            </div>

            <div id="question-17" class="report-section">
                <div class="section-header">
                    <h2 class="section-title">Question 17</h2>
                </div>
                <div class="coming-soon">
                    <h4>Coming Soon</h4>
                    <p>This report section will be implemented based on specific requirements.</p>
                </div>
            </div>

            <div id="question-18" class="report-section">
                <div class="section-header">
                    <h2 class="section-title">Question 18</h2>
                </div>
                <div class="coming-soon">
                    <h4>Coming Soon</h4>
                    <p>This report section will be implemented based on specific requirements.</p>
                </div>
            </div>

            <div id="question-19" class="report-section">
                <div class="section-header">
                    <h2 class="section-title">Question 19</h2>
                </div>
                <div class="coming-soon">
                    <h4>Coming Soon</h4>
                    <p>This report section will be implemented based on specific requirements.</p>
                </div>
            </div>
        </div>            
    </div>
    

    <!-- Include the modals and JavaScript -->
    <?php include 'modals.php'; ?>
    <?php include 'script.php'; ?>
</body>
</html> 