<?php
// Start session at the very beginning
session_start();

// Database configuration
$host = '127.0.0.1';
$dbname = 'mvc_db';
$username = 'mvc_user';
$password = 'mvc_pass';
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
<<<<<<< HEAD
        if (isset($data['familyID']) && !empty($data['familyID'])) {
            // UPDATE existing family member
            $stmt = $pdo->prepare("UPDATE FamilyMembers SET relationshipType = ?, firstName = ?, lastName = ?, dob = ?, ssn = ?, medicare = ?, phone = ?, address = ?, city = ?, province = ?, postalCode = ?, email = ?, locationID = ? WHERE familyID = ?");
=======
        if (isset($data['familyMemID']) && !empty($data['familyMemID'])) {
            // UPDATE existing person
            $stmt = $pdo->prepare("UPDATE Person SET firstName = ?, lastName = ?, dob = ?, ssn = ?, medicare = ?, address = ?, postalCode = ?, phone = ?, email = ? WHERE pID = ?");
>>>>>>> origin/main
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
<<<<<<< HEAD
                $data['location_id'],
                $data['familyID']
=======
                $data['familyMemID']
            ]);
            
            // UPDATE existing family member
            $stmt = $pdo->prepare("UPDATE FamilyMember SET primarySecondaryRelationship = ? WHERE familyMemID = ?");
            $stmt->execute([
                $data['relationshipType'],
                $data['familyMemID']
>>>>>>> origin/main
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
<<<<<<< HEAD
            // UPDATE existing member
            $stmt = $pdo->prepare("UPDATE ClubMembers SET firstName = ?, lastName = ?, dob = ?, age = ?, height = ?, weight = ?, ssn = ?, medicare = ?, phone = ?, address = ?, email = ?, city = ?, province = ?, postalCode = ?, locationID = ?, familyID = ?, status = ? WHERE memberID = ?");
=======
            // UPDATE existing person
            $stmt = $pdo->prepare("UPDATE Person SET firstName = ?, lastName = ?, dob = ?, ssn = ?, medicare = ?, address = ?, postalCode = ?, phone = ?, email = ? WHERE pID = ?");
>>>>>>> origin/main
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
<<<<<<< HEAD
            // INSERT new member
            $stmt = $pdo->prepare("INSERT INTO ClubMembers (firstName, lastName, dob, age, height, weight, ssn, medicare, phone, address, email, city, province, postalCode, locationID, familyID, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
=======
            // INSERT new person
            $stmt = $pdo->prepare("INSERT INTO Person (firstName, lastName, dob, ssn, medicare, address, postalCode, phone, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
>>>>>>> origin/main
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
<<<<<<< HEAD
    $stmt = $pdo->query("SELECT fm.*, p.firstName, p.lastName, p.phone, p.email, p.address 
                        FROM FamilyMember fm 
                        LEFT JOIN Person p ON fm.familyID = p.pID 
                        ORDER BY p.lastName");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
=======
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
>>>>>>> origin/main
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












// Q.17
// function getQualifiedFamilyMembers(PDO $pdo, int $locationID): array {
//     $stmt = $pdo->prepare("
//         SELECT DISTINCT p.firstName, p.lastName, p.phone
//         FROM Person p
//         JOIN FamilyMember fm ON p.pID = fm.familyID
//         JOIN FamilyMember fm ON p.pID = fm.familyID
//         JOIN FamilyHistory fh ON fh.familyID = fm.familyID
//         JOIN Team t ON t.headCoachID = fm.familyID
//         WHERE cm.status = 'Active'
//             AND cm.locationID = :locationID
//             AND t.locationID = :locationID
//     ");
//     $stmt->execute(['locationID' => $locationID]);
//     return $stmt->fetchAll(PDO::FETCH_ASSOC);
// }


// Q.18
function getQualifiedFamilyMembers(PDO $pdo): array {
    $stmt = $pdo->prepare("
        SELECT cm.memberID, p.firstName, p.lastName, TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS age, p.phone, p.email, l.name AS locationName
        FROM ClubMember cm
        JOIN Person p ON cm.memberID = p.pID
        JOIN Location l ON cm.locationID = l.locationID
        WHERE cm.status = 'Active'
        AND cm.memberID IN (
            -- Members who have participated in at least one Game session
            SELECT DISTINCT sp.participantID
            FROM SessionParticipation sp
            JOIN Session s ON sp.sessionID = s.sessionID
            WHERE s.sessionType = 'Game'
        )
        AND cm.memberID NOT IN (
            -- Members who have ever LOST a game
            SELECT sp.participantID
            FROM SessionParticipation sp
            JOIN Session s ON sp.sessionID = s.sessionID
            WHERE s.sessionType = 'Game'
                AND (
                    (sp.teamID = s.team1ID AND s.team1Score < s.team2Score) OR
                    (sp.teamID = s.team2ID AND s.team2Score < s.team1Score)
                )
        )
        ORDER BY l.name ASC, cm.memberID ASC;
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Q.19
// function getQualifiedFamilyMembers(PDO $pdo): array {
//     $stmt = $pdo->prepare("
//         SELECT p.firstName, p.lastName, COUNT(DISTINCT cm.memberID) AS minorCount, p.phone, p.email, l.name AS locationName, pl.role
//         FROM Person p
//         JOIN Personnel pl ON p.pID = pl.employeeID AND pl.mandate = 'Volunteer'
//         JOIN FamilyMember fm ON p.pID = fm.familyID
//         JOIN FamilyHistory fh ON fh.familyID = fm.familyID
//         JOIN ClubMember cm ON fh.memberID = cm.memberID AND cm.memberType = 'Minor'
//         JOIN WorkInfo wi ON pl.employeeID = wi.employeeID
//         JOIN Location l ON wi.locationID = l.locationID
//         WHERE wi.endDate IS NULL
//         GROUP BY p.pID, l.name, pl.role
//         ORDER BY l.name ASC, pl.role ASC, p.firstName ASC, p.lastName ASC
//     ");
//     $stmt->execute();
//     return $stmt->fetchAll(PDO::FETCH_ASSOC);
// }





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

<<<<<<< HEAD
function saveFamilyHistory($pdo, $data) {
    try {
        // Check if we're editing existing family history
        if (isset($data['oldStartDate']) && !empty($data['oldStartDate'])) {
            // UPDATE existing family history
            $stmt = $pdo->prepare("UPDATE FamilyHistory SET memberID = ?, type = ?, familyID = ?, startDate = ?, endDate = ? WHERE memberID = ? AND familyID = ? AND startDate = ?");
            $stmt->execute([
                $data['memberID'],
                $data['type'],
                $data['familyID'],
                $data['startDate'],
                $data['endDate'] ?: null,
                $data['oldMemberID'],
                $data['oldFamilyID'],
                $data['oldStartDate']
            ]);
        } else {
            // INSERT new family history
            $stmt = $pdo->prepare("INSERT INTO FamilyHistory (memberID, type, familyID, startDate, endDate) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['memberID'],
                $data['type'],
                $data['familyID'],
                $data['startDate'],
                $data['endDate'] ?: null
            ]);
        }
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function saveTeamMember($pdo, $data) {
    try {
        // Check if we're editing existing team member
        if (isset($data['oldMemberID']) && !empty($data['oldMemberID'])) {
            // UPDATE existing team member
            $stmt = $pdo->prepare("UPDATE TeamMember SET teamID = ?, memberID = ?, roleInTeam = ? WHERE teamID = ? AND memberID = ?");
            $stmt->execute([
                $data['teamID'],
                $data['memberID'],
                $data['roleInTeam'],
                $data['oldTeamID'],
                $data['oldMemberID']
            ]);
        } else {
            // INSERT new team member
            $stmt = $pdo->prepare("INSERT INTO TeamMember (teamID, memberID, roleInTeam) VALUES (?, ?, ?)");
            $stmt->execute([
                $data['teamID'],
                $data['memberID'],
                $data['roleInTeam']
            ]);
        }
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Data retrieval functions for new tables
function getPostalAreas($pdo) {
    $stmt = $pdo->query("SELECT * FROM PostalAreaInfo ORDER BY postalCode");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getLocationPhones($pdo) {
    $stmt = $pdo->query("SELECT lp.*, l.name as location_name 
                        FROM LocationPhone lp 
                        LEFT JOIN Location l ON lp.locationID = l.locationID 
                        ORDER BY l.name, lp.phone");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFamilyHistory($pdo) {
    $stmt = $pdo->query("SELECT fh.*, 
                             CONCAT(m.firstName, ' ', m.lastName) as member_name,
                             CONCAT(fm.firstName, ' ', fm.lastName) as family_member_name
                        FROM FamilyHistory fh 
                        LEFT JOIN ClubMember cm ON fh.memberID = cm.memberID 
                        LEFT JOIN Person m ON cm.memberID = m.pID 
                        LEFT JOIN FamilyMember fm_rel ON fh.familyID = fm_rel.familyID 
                        LEFT JOIN Person fm ON fm_rel.familyID = fm.pID 
                        ORDER BY fh.startDate DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
=======
>>>>>>> origin/main
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
                            echo "<td>" . htmlspecialchars($location['type'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($location['address'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($location['maxCapacity'] ?? '') . "</td>";
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
<<<<<<< HEAD
                            echo "<td>" . htmlspecialchars($person['medicare'] ?? '') . "</td>";
=======
                            echo "<td>" . htmlspecialchars($person['medicare']) . "</td>";
                            echo "<td>" . htmlspecialchars($person['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($person['phone']) . "</td>";
                            echo "<td>" . htmlspecialchars($person['address']) . "</td>";
>>>>>>> origin/main
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
<<<<<<< HEAD
                            echo "<td>" . htmlspecialchars($family['relationshipType'] ?? '') . "</td>";
=======
                            echo "<td>" . htmlspecialchars($family['ssn']) . "</td>";
                            echo "<td>" . htmlspecialchars($family['medicare']) . "</td>";
                            echo "<td>" . htmlspecialchars($family['relationshipType']) . "</td>";
>>>>>>> origin/main
                            echo "<td>" . htmlspecialchars($family['phone']) . "</td>";
                            echo "<td>" . htmlspecialchars($family['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($family['address']) . "</td>";
                            echo "<td class='action-buttons'>";
                            echo "<button class='edit-btn' onclick='editFamily(" . $family['familyID'] . ")'>Edit</button>";
                            echo "<button class='delete-btn' onclick='deleteFamily(" . $family['familyID'] . ")'>Delete</button>";
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

<<<<<<< HEAD
            <!-- Payments Section -->
            <div id="payments" class="section">
                <div class="section-header">
                    <h2 class="section-title">Payments & Donations</h2>
                    <button class="btn" onclick="openModal('paymentModal')">Record Payment</button>
                </div>
                
                <div class="filters">
                    <div class="filter-group">
                        <label>Year:</label>
                        <select>
                            <option value="">All Years</option>
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Payment Method:</label>
                        <select>
                            <option value="">All Methods</option>
                            <option value="cash">Cash</option>
                            <option value="check">Check</option>
                            <option value="credit">Credit Card</option>
                            <option value="debit">Debit Card</option>
                        </select>
                    </div>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Year</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $payments = getPayments($pdo);
                        foreach ($payments as $payment) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($payment['firstName'] . ' ' . $payment['lastName']) . "</td>";
                            echo "<td>$" . number_format($payment['amount'], 2) . "</td>";
                            echo "<td>" . htmlspecialchars($payment['method']) . "</td>";
                            echo "<td>" . htmlspecialchars($payment['paymentDate']) . "</td>";
                            echo "<td>" . htmlspecialchars($payment['membershipYear']) . "</td>";
                            echo "<td>Membership</td>";
                            echo "<td class='action-buttons'>";
                            echo "<button class='edit-btn' onclick='editPayment(" . $payment['memberID'] . ")'>Edit</button>";
                            echo "<button class='delete-btn' onclick='deletePayment(" . $payment['memberID'] . ")'>Delete</button>";
                            echo "</td>";
                            echo "</tr>";
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
                            <th>Players</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $teams = getTeams($pdo);
                        foreach ($teams as $team) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($team['teamName'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($team['teamType'] ?? '') . "</td>";
                            echo "<td>N/A</td>";
                            echo "<td>" . htmlspecialchars($team['location_name'] ?? 'N/A') . "</td>";
                            echo "<td>0</td>"; // TODO: Count team players
                            echo "<td class='action-buttons'>";
                            echo "<button class='edit-btn' onclick='editTeam(" . $team['teamID'] . ")'>Edit</button>";
                            echo "<button class='delete-btn' onclick='deleteTeam(" . $team['teamID'] . ")'>Delete</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Hobbies Section -->
            <div id="hobbies" class="section">
                <div class="section-header">
                    <h2 class="section-title">Hobbies Management</h2>
                    <button class="btn" onclick="openModal('hobbyModal')">Add Hobby</button>
                    <button class="btn btn-secondary" onclick="openModal('memberHobbyModal')">Assign Hobby to Member</button>
                </div>
                
                <div class="filters">
                    <div class="filter-group">
                        <label>Filter by Member:</label>
                        <select id="memberFilter" onchange="filterMemberHobbies()">
                            <option value="">All Members</option>
                            <?php
                            $members = getMembers($pdo);
                            foreach ($members as $member) {
                                echo "<option value='" . $member['memberID'] . "'>" . htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <h3>Available Hobbies</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Hobby Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $hobbies = getHobbies($pdo);
                        foreach ($hobbies as $hobby) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($hobby['hobbyName']) . "</td>";
                            echo "<td class='action-buttons'>";
                            echo "<button class='edit-btn' onclick='editHobby(\"" . htmlspecialchars($hobby['hobbyName']) . "\")'>Edit</button>";
                            echo "<button class='delete-btn' onclick='deleteHobby(\"" . htmlspecialchars($hobby['hobbyName']) . "\")'>Delete</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <h3>Member Hobbies</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Member Name</th>
                            <th>Hobby</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $memberHobbies = getMemberHobbies($pdo);
                        foreach ($memberHobbies as $memberHobby) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($memberHobby['firstName'] . ' ' . $memberHobby['lastName']) . "</td>";
                            echo "<td>" . htmlspecialchars($memberHobby['hobbyName']) . "</td>";
                            echo "<td class='action-buttons'>";
                            echo "<button class='delete-btn' onclick='removeMemberHobby(" . $memberHobby['memberID'] . ", \"" . htmlspecialchars($memberHobby['hobbyName']) . "\")'>Remove</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Work History Section -->
            <div id="workinfo" class="section">
                <div class="section-header">
                    <h2 class="section-title">Work History Management</h2>
                    <button class="btn" onclick="openModal('workInfoModal')">Add Work Assignment</button>
                </div>
                
                <div class="filters">
                    <div class="filter-group">
                        <label>Filter by Personnel:</label>
                        <select id="personnelFilter" onchange="filterWorkInfo()">
                            <option value="">All Personnel</option>
                            <?php
                            $personnel = getPersonnel($pdo);
                            foreach ($personnel as $person) {
                                echo "<option value='" . $person['pID'] . "'>" . htmlspecialchars($person['firstName'] . ' ' . $person['lastName']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Personnel Name</th>
                            <th>Location</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $workInfo = getWorkInfo($pdo);
                        foreach ($workInfo as $work) {
                            $status = $work['endDate'] ? 'Completed' : 'Active';
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($work['firstName'] . ' ' . $work['lastName']) . "</td>";
                            echo "<td>" . htmlspecialchars($work['location_name'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($work['startDate']) . "</td>";
                            echo "<td>" . htmlspecialchars($work['endDate'] ?? 'Ongoing') . "</td>";
                            echo "<td>" . $status . "</td>";
                            echo "<td class='action-buttons'>";
                            echo "<button class='edit-btn' onclick='editWorkInfo(" . $work['pID'] . ", " . $work['locationID'] . ", \"" . $work['startDate'] . "\")'>Edit</button>";
                            echo "<button class='delete-btn' onclick='deleteWorkInfo(" . $work['pID'] . ", " . $work['locationID'] . ", \"" . $work['startDate'] . "\")'>Delete</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

=======
>>>>>>> origin/main
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
                

<<<<<<< HEAD
            <!-- Postal Areas Section -->
            <div id="postalareas" class="section">
                <div class="section-header">
                    <h2 class="section-title">Postal Area Management</h2>
                    <button class="btn" onclick="openModal('postalAreaModal')">Add Postal Area</button>
                </div>
                
                <input type="text" class="search-bar" placeholder="Search postal areas...">
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Postal Code</th>
                            <th>City</th>
                            <th>Province</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $postalAreas = getPostalAreas($pdo);
                        foreach ($postalAreas as $area) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($area['postalCode']) . "</td>";
                            echo "<td>" . htmlspecialchars($area['city']) . "</td>";
                            echo "<td>" . htmlspecialchars($area['province']) . "</td>";
                            echo "<td class='action-buttons'>";
                            echo "<button class='edit-btn' onclick='editPostalArea(\"" . htmlspecialchars($area['postalCode']) . "\")'>Edit</button>";
                            echo "<button class='delete-btn' onclick='deletePostalArea(\"" . htmlspecialchars($area['postalCode']) . "\")'>Delete</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Location Phones Section -->
            <div id="locationphones" class="section">
                <div class="section-header">
                    <h2 class="section-title">Location Phone Management</h2>
                    <button class="btn" onclick="openModal('locationPhoneModal')">Add Location Phone</button>
                </div>
                
                <div class="filters">
                    <div class="filter-group">
                        <label>Location:</label>
                        <select id="locationPhoneFilter" onchange="filterLocationPhones()">
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
                
                <input type="text" class="search-bar" placeholder="Search location phones...">
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $locationPhones = getLocationPhones($pdo);
                        if (empty($locationPhones)) {
                            echo "<tr><td colspan='3' style='text-align: center;'>No location phones found.</td></tr>";
                        } else {
                            foreach ($locationPhones as $phone) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($phone['location_name'] ?? 'N/A') . "</td>";
                                echo "<td>" . htmlspecialchars($phone['phone']) . "</td>";
                                echo "<td class='action-buttons'>";
                                echo "<button class='edit-btn' onclick='editLocationPhone(" . $phone['locationID'] . ", \"" . htmlspecialchars($phone['phone']) . "\")'>Edit</button>";
                                echo "<button class='delete-btn' onclick='deleteLocationPhone(" . $phone['locationID'] . ", \"" . htmlspecialchars($phone['phone']) . "\")'>Delete</button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Family History Section -->
            <div id="familyhistory" class="section">
                <div class="section-header">
                    <h2 class="section-title">Family History Management</h2>
                    <button class="btn" onclick="openModal('familyHistoryModal')">Add Family History</button>
                </div>
                
                <div class="filters">
                    <div class="filter-group">
                        <label>Filter by Member:</label>
                        <select id="familyHistoryMemberFilter" onchange="filterFamilyHistory()">
                            <option value="">All Members</option>
                            <?php
                            $members = getMembers($pdo);
                            foreach ($members as $member) {
                                echo "<option value='" . $member['memberID'] . "'>" . htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Member Name</th>
                            <th>Family Member</th>
                            <th>Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $familyHistory = getFamilyHistory($pdo);
                        foreach ($familyHistory as $fh) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($fh['member_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($fh['family_member_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($fh['type'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($fh['startDate']) . "</td>";
                            echo "<td>" . htmlspecialchars($fh['endDate'] ?? 'Ongoing') . "</td>";
                            echo "<td class='action-buttons'>";
                            echo "<button class='edit-btn' onclick='editFamilyHistory(" . $fh['memberID'] . ", " . $fh['familyID'] . ", \"" . $fh['startDate'] . "\")'>Edit</button>";
                            echo "<button class='delete-btn' onclick='deleteFamilyHistory(" . $fh['memberID'] . ", " . $fh['familyID'] . ", \"" . $fh['startDate'] . "\")'>Delete</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Team Members Section -->
            <div id="teammembers" class="section">
                <div class="section-header">
                    <h2 class="section-title">Team Member Management</h2>
                    <button class="btn" onclick="openModal('teamMemberModal')">Add Team Member</button>
                </div>
                
                <div class="filters">
                    <div class="filter-group">
                        <label>Team:</label>
                        <select id="teamMemberFilter" onchange="filterTeamMembers()">
                            <option value="">All Teams</option>
                            <?php
                            $teams = getTeams($pdo);
                            foreach ($teams as $team) {
                                echo "<option value='" . $team['teamID'] . "'>" . htmlspecialchars($team['teamName']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <input type="text" class="search-bar" placeholder="Search team members...">
=======
>>>>>>> origin/main
                
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
<<<<<<< HEAD
                                echo "<td>" . htmlspecialchars($tm['teamName'] ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars($tm['member_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($tm['roleInTeam'] ?? '') . "</td>";
=======
                                echo "<td>" . htmlspecialchars($team['teamName']) . "</td>";
                                echo "<td>" . htmlspecialchars($team['gender']) . "</td>";
                                echo "<td>" . htmlspecialchars($team['coach_name'] ?? 'N/A') . "</td>";
                                echo "<td>" . htmlspecialchars($team['location_name'] ?? 'N/A') . "</td>";
>>>>>>> origin/main
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

<<<<<<< HEAD





            <div id="reports" class="section">
                <div class="section-header">
                    <h2 class="section-title">Reports: Qualified Family Members</h2>
                    
=======
            <!-- Payments Section -->
            <div id="payments" class="section">
                <div class="section-header">
                    <h2 class="section-title">Payment Management</h2>
                    <button class="btn" onclick="openModal('paymentModal')">Record Payment</button>
>>>>>>> origin/main
                </div>

                

                <form method="GET" style="margin-bottom: 20px;">
                    <label for="locationID"><strong>Select Location:</strong></label>
                    <select name="locationID" id="locationID" onchange="this.form.submit()">
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= $loc['locationID'] ?>" <?= $loc['locationID'] == $selectedLocationID ? 'selected' : '' ?>>
                                <?= htmlspecialchars($loc['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <noscript><button type="submit">Submit</button></noscript>
                </form>

                <?php
                if ($selectedLocationID) {
                    $results = getQualifiedFamilyMembers($pdo, $selectedLocationID);
                }
                ?>

                <h3>Qualified Family Members (Head Coaches + Active Members)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Location</th>
                            <th>Role</th>
                            <th># of Minor Members</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($results)): ?>
                            <tr><td colspan="7">No results found for this location.</td></tr>
                        <?php else: ?>
                            <?php foreach ($results as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['firstName']) ?></td>
                                    <td><?= htmlspecialchars($row['lastName']) ?></td>
                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['locationName']) ?></td>
                                    <td><?= htmlspecialchars($row['role']) ?></td>
                                    <td><?= htmlspecialchars($row['minorCount']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                
<<<<<<< HEAD
            </div>
            
        </div>
        
=======

                
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
>>>>>>> origin/main
    </div>
    

    <!-- Include the modals and JavaScript -->
    <?php include 'modals.php'; ?>
    <?php include 'script.php'; ?>
</body>
</html> 