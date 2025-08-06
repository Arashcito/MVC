<?php
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
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'save_location':
                saveLocation($pdo, $_POST);
                break;
            case 'save_personnel':
                savePersonnel($pdo, $_POST);
                break;
            case 'save_family':
                saveFamily($pdo, $_POST);
                break;
            case 'save_member':
                saveMember($pdo, $_POST);
                break;
            case 'save_payment':
                savePayment($pdo, $_POST);
                break;
            case 'save_team':
                saveTeam($pdo, $_POST);
                break;
            case 'save_session':
                saveSession($pdo, $_POST);
                break;
            case 'save_hobby':
                saveHobby($pdo, $_POST);
                break;
            case 'save_member_hobby':
                saveMemberHobby($pdo, $_POST);
                break;
            case 'save_workinfo':
                saveWorkInfo($pdo, $_POST);
                break;
            case 'save_postalarea':
                savePostalArea($pdo, $_POST);
                break;
            case 'save_locationphone':
                saveLocationPhone($pdo, $_POST);
                break;
            case 'save_familyhistory':
                saveFamilyHistory($pdo, $_POST);
                break;
            case 'save_teammember':
                saveTeamMember($pdo, $_POST);
                break;
        }
    }
}

// Database functions
function saveLocation($pdo, $data) {
    try {
        // Check if we're editing an existing location
        if (isset($data['locationID']) && !empty($data['locationID'])) {
            // UPDATE existing location
            $stmt = $pdo->prepare("UPDATE Location SET name = ?, type = ?, address = ?, city = ?, province = ?, postalCode = ?, phone = ?, webAddress = ?, maxCapacity = ? WHERE locationID = ?");
            $stmt->execute([
                $data['name'],
                $data['type'],
                $data['address'],
                $data['city'],
                $data['province'],
                $data['postal_code'],
                $data['phone'],
                $data['web_address'],
                $data['max_capacity'],
                $data['locationID']
            ]);
        } else {
            // INSERT new location
            $stmt = $pdo->prepare("INSERT INTO Location (name, type, address, city, province, postalCode, phone, webAddress, maxCapacity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['name'],
                $data['type'],
                $data['address'],
                $data['city'],
                $data['province'],
                $data['postal_code'],
                $data['phone'],
                $data['web_address'],
                $data['max_capacity']
            ]);
        }
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function savePersonnel($pdo, $data) {
    try {
        // Check if we're editing existing personnel
        if (isset($data['pID']) && !empty($data['pID'])) {
            // UPDATE existing personnel
            $stmt = $pdo->prepare("UPDATE Personnel SET firstName = ?, lastName = ?, dob = ?, ssn = ?, medicare = ?, phone = ?, address = ?, city = ?, province = ?, postalCode = ?, email = ?, role = ?, mandate = ? WHERE employeeID = ?");
            $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['dob'],
                $data['ssn'],
                $data['medicare'],
                $data['phone'],
                $data['address'],
                $data['city'],
                $data['province'],
                $data['postal_code'],
                $data['email'],
                $data['role'],
                $data['mandate'],
                $data['pID']
            ]);
        } else {
            // INSERT new personnel
            $stmt = $pdo->prepare("INSERT INTO Personnel (firstName, lastName, dob, ssn, medicare, phone, address, city, province, postalCode, email, role, mandate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['dob'],
                $data['ssn'],
                $data['medicare'],
                $data['phone'],
                $data['address'],
                $data['city'],
                $data['province'],
                $data['postal_code'],
                $data['email'],
                $data['role'],
                $data['mandate']
            ]);
        }
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function saveFamily($pdo, $data) {
    try {
        // Check if we're editing existing family member
        if (isset($data['familyID']) && !empty($data['familyID'])) {
            // UPDATE existing family member
            $stmt = $pdo->prepare("UPDATE FamilyMembers SET relationshipType = ?, firstName = ?, lastName = ?, dob = ?, ssn = ?, medicare = ?, phone = ?, address = ?, city = ?, province = ?, postalCode = ?, email = ?, locationID = ? WHERE familyID = ?");
            $stmt->execute([
                $data['relationshipType'],
                $data['first_name'],
                $data['last_name'],
                $data['dob'],
                $data['ssn'],
                $data['medicare'],
                $data['phone'],
                $data['address'],
                $data['city'],
                $data['province'],
                $data['postal_code'],
                $data['email'],
                $data['location_id'],
                $data['familyID']
            ]);
        } else {
            // INSERT new family member
            $stmt = $pdo->prepare("INSERT INTO FamilyMembers (relationshipType, firstName, lastName, dob, ssn, medicare, phone, address, city, province, postalCode, email, locationID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['relationshipType'],
                $data['first_name'],
                $data['last_name'],
                $data['dob'],
                $data['ssn'],
                $data['medicare'],
                $data['phone'],
                $data['address'],
                $data['city'],
                $data['province'],
                $data['postal_code'],
                $data['email'],
                $data['location_id']
            ]);
        }
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function saveMember($pdo, $data) {
    try {
        // Check if we're editing existing member
        if (isset($data['memberID']) && !empty($data['memberID'])) {
            // UPDATE existing member
            $stmt = $pdo->prepare("UPDATE ClubMembers SET firstName = ?, lastName = ?, dob = ?, age = ?, height = ?, weight = ?, ssn = ?, medicare = ?, phone = ?, address = ?, email = ?, city = ?, province = ?, postalCode = ?, locationID = ?, familyID = ?, status = ? WHERE memberID = ?");
            $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['dob'],
                $data['age'],
                $data['height'],
                $data['weight'],
                $data['ssn'],
                $data['medicare'],
                $data['phone'],
                $data['address'],
                $data['email'],
                $data['city'],
                $data['province'],
                $data['postal_code'],
                $data['location_id'],
                $data['family_member_id'] ?: null,
                $data['status'] ?? 'Active',
                $data['memberID']
            ]);
        } else {
            // INSERT new member
            $stmt = $pdo->prepare("INSERT INTO ClubMembers (firstName, lastName, dob, age, height, weight, ssn, medicare, phone, address, email, city, province, postalCode, locationID, familyID, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['dob'],
                $data['age'],
                $data['height'],
                $data['weight'],
                $data['ssn'],
                $data['medicare'],
                $data['phone'],
                $data['address'],
                $data['email'],
                $data['city'],
                $data['province'],
                $data['postal_code'],
                $data['location_id'],
                $data['family_member_id'] ?: null,
                $data['status'] ?? 'Active'
            ]);
        }
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function savePayment($pdo, $data) {
    try {
        // Check if we're editing existing payment
        if (isset($data['paymentID']) && !empty($data['paymentID'])) {
            // UPDATE existing payment
            $stmt = $pdo->prepare("UPDATE Payments SET memberID = ?, amount = ?, method = ?, paymentDate = ?, membershipYear = ?, installmentNo = ? WHERE paymentID = ?");
            $stmt->execute([
                $data['member_id'],
                $data['amount'],
                $data['payment_method'],
                $data['payment_date'],
                $data['year'],
                $data['installment_no'] ?? 1,
                $data['paymentID']
            ]);
        } else {
            // INSERT new payment
            $stmt = $pdo->prepare("INSERT INTO Payments (memberID, amount, method, paymentDate, membershipYear, installmentNo) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['member_id'],
                $data['amount'],
                $data['payment_method'],
                $data['payment_date'],
                $data['year'],
                $data['installment_no'] ?? 1
            ]);
        }
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function saveTeam($pdo, $data) {
    try {
        // Check if we're editing existing team
        if (isset($data['teamID']) && !empty($data['teamID'])) {
            // UPDATE existing team
            $stmt = $pdo->prepare("UPDATE Teams SET teamName = ?, teamType = ?, locationID = ? WHERE teamID = ?");
            $stmt->execute([
                $data['name'],
                $data['team_type'],
                $data['location_id'],
                $data['teamID']
            ]);
        } else {
            // INSERT new team
            $stmt = $pdo->prepare("INSERT INTO Teams (teamName, teamType, locationID) VALUES (?, ?, ?)");
            $stmt->execute([
                $data['name'],
                $data['team_type'],
                $data['location_id']
            ]);
        }
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function saveSession($pdo, $data) {
    try {
        // Check if we're editing existing session
        if (isset($data['sessionID']) && !empty($data['sessionID'])) {
            // UPDATE existing session
            $stmt = $pdo->prepare("UPDATE sessions SET type = ?, date = ?, time = ?, location_id = ?, team1_id = ?, team2_id = ?, coach_id = ?, score = ? WHERE sessionID = ?");
            $stmt->execute([
                $data['type'],
                $data['date'],
                $data['time'],
                $data['location_id'],
                $data['team1_id'],
                $data['team2_id'] ?: null,
                $data['coach_id'],
                $data['score'] ?: null,
                $data['sessionID']
            ]);
        } else {
            // INSERT new session
            $stmt = $pdo->prepare("INSERT INTO sessions (type, date, time, location_id, team1_id, team2_id, coach_id, score) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['type'],
                $data['date'],
                $data['time'],
                $data['location_id'],
                $data['team1_id'],
                $data['team2_id'] ?: null,
                $data['coach_id'],
                $data['score'] ?: null
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
    $stmt = $pdo->query("SELECT p.*, per.firstName, per.lastName, per.email, per.phone, per.address, per.dob, per.ssn, per.medicare, l.name as location_name 
                        FROM Personnel p 
                        LEFT JOIN Person per ON p.employeeID = per.pID 
                        LEFT JOIN Location l ON p.employeeID = l.managerID 
                        ORDER BY per.lastName");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFamilyMembers($pdo) {
    $stmt = $pdo->query("SELECT fm.*, p.firstName, p.lastName, p.phone, p.email, p.address 
                        FROM FamilyMember fm 
                        LEFT JOIN Person p ON fm.familyID = p.pID 
                        ORDER BY p.lastName");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMembers($pdo) {
    $stmt = $pdo->query("SELECT cm.*, p.firstName, p.lastName, p.dob, p.address, p.phone, p.email, l.name as location_name 
                        FROM ClubMember cm 
                        LEFT JOIN Person p ON cm.memberID = p.pID 
                        LEFT JOIN Location l ON cm.locationID = l.locationID 
                        ORDER BY p.lastName");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPayments($pdo) {
    $stmt = $pdo->query("SELECT p.*, per.firstName, per.lastName 
                        FROM Payment p 
                        LEFT JOIN ClubMember cm ON p.memberID = cm.memberID 
                        LEFT JOIN Person per ON cm.memberID = per.pID 
                        ORDER BY p.paymentDate DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTeams($pdo) {
    $stmt = $pdo->query("SELECT t.*, l.name as location_name 
                        FROM Team t 
                        LEFT JOIN Location l ON t.locationID = l.locationID 
                        ORDER BY t.teamName");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSessions($pdo) {
    try {
        $stmt = $pdo->query("SELECT s.*, t1.teamName as team1_name, t2.teamName as team2_name 
                             FROM Session s 
                             LEFT JOIN Team t1 ON s.team1ID = t1.teamID 
                             LEFT JOIN Team t2 ON s.team2ID = t2.teamID 
                             ORDER BY s.sessionDate DESC, s.startTime DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getHobbies($pdo) {
    $stmt = $pdo->query("SELECT * FROM Hobby ORDER BY hobbyName");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMemberHobbies($pdo) {
    $stmt = $pdo->query("SELECT mh.*, per.firstName, per.lastName, h.hobbyName 
                        FROM MemberHobby mh 
                        LEFT JOIN ClubMember cm ON mh.memberID = cm.memberID 
                        LEFT JOIN Person per ON cm.memberID = per.pID 
                        LEFT JOIN Hobby h ON mh.hobbyName = h.hobbyName 
                        ORDER BY per.lastName, h.hobbyName");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getWorkInfo($pdo) {
    $stmt = $pdo->query("SELECT wi.*, per.firstName, per.lastName, l.name as location_name 
                        FROM WorkInfo wi 
                        LEFT JOIN Personnel p ON wi.employeeID = p.employeeID 
                        LEFT JOIN Person per ON p.employeeID = per.pID 
                        LEFT JOIN Location l ON wi.locationID = l.locationID 
                        ORDER BY per.lastName, wi.startDate DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getYearlyPayments($pdo) {
    try {
        // Create a direct query instead of using the broken view
        $stmt = $pdo->query("SELECT p.memberID, p.membershipYear, 
                                   SUM(p.amount) as totalYearlyPayment,
                                   per.firstName, per.lastName 
                            FROM Payment p 
                            LEFT JOIN ClubMember cm ON p.memberID = cm.memberID 
                            LEFT JOIN Person per ON cm.memberID = per.pID 
                            GROUP BY p.memberID, p.membershipYear 
                            ORDER BY p.membershipYear DESC, per.lastName");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getEmails($pdo) {
    try {
        $stmt = $pdo->query("SELECT e.*, 
                             CONCAT(p.firstName, ' ', p.lastName) as sender_name,
                             CONCAT(m.firstName, ' ', m.lastName) as receiver_name
                             FROM Emails e 
                             LEFT JOIN Personnel p ON e.senderID = p.pID 
                             LEFT JOIN ClubMembers m ON e.receiverID = m.memberID 
                             ORDER BY e.sent_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getTeamMembers($pdo) {
    try {
        $stmt = $pdo->query("SELECT tm.*, t.teamName, 
                             CONCAT(m.firstName, ' ', m.lastName) as member_name
                             FROM TeamMember tm 
                             LEFT JOIN Team t ON tm.teamID = t.teamID 
                             LEFT JOIN ClubMember cm ON tm.memberID = cm.memberID 
                             LEFT JOIN Person m ON cm.memberID = m.pID 
                             ORDER BY t.teamName, m.lastName");
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
        // Check if we're editing existing hobby
        if (isset($data['oldHobbyName']) && !empty($data['oldHobbyName'])) {
            // UPDATE existing hobby
            $stmt = $pdo->prepare("UPDATE Hobbies SET hobbyName = ? WHERE hobbyName = ?");
            $stmt->execute([$data['hobbyName'], $data['oldHobbyName']]);
        } else {
            // INSERT new hobby
            $stmt = $pdo->prepare("INSERT INTO Hobbies (hobbyName) VALUES (?)");
            $stmt->execute([$data['hobbyName']]);
        }
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
        // Check if we're editing existing work info
        if (isset($data['oldPID']) && isset($data['oldLocationID']) && isset($data['oldStartDate'])) {
            // UPDATE existing work info
            $stmt = $pdo->prepare("UPDATE WorkInfo SET pID = ?, locationID = ?, startDate = ?, endDate = ? WHERE pID = ? AND locationID = ? AND startDate = ?");
            $stmt->execute([
                $data['pID'],
                $data['locationID'],
                $data['startDate'],
                $data['endDate'] ?: null,
                $data['oldPID'],
                $data['oldLocationID'],
                $data['oldStartDate']
            ]);
        } else {
            // INSERT new work info
            $stmt = $pdo->prepare("INSERT INTO WorkInfo (pID, locationID, startDate, endDate) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['pID'],
                $data['locationID'],
                $data['startDate'],
                $data['endDate'] ?: null
            ]);
        }
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// New functions for additional tables
function savePostalArea($pdo, $data) {
    try {
        // Check if we're editing existing postal area
        if (isset($data['oldPostalCode']) && !empty($data['oldPostalCode'])) {
            // UPDATE existing postal area
            $stmt = $pdo->prepare("UPDATE PostalAreaInfo SET postalCode = ?, city = ?, province = ? WHERE postalCode = ?");
            $stmt->execute([
                $data['postalCode'],
                $data['city'],
                $data['province'],
                $data['oldPostalCode']
            ]);
        } else {
            // INSERT new postal area
            $stmt = $pdo->prepare("INSERT INTO PostalAreaInfo (postalCode, city, province) VALUES (?, ?, ?)");
            $stmt->execute([
                $data['postalCode'],
                $data['city'],
                $data['province']
            ]);
        }
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function saveLocationPhone($pdo, $data) {
    try {
        // Check if we're editing existing location phone
        if (isset($data['oldPhone']) && !empty($data['oldPhone'])) {
            // UPDATE existing location phone
            $stmt = $pdo->prepare("UPDATE LocationPhone SET phone = ? WHERE locationID = ? AND phone = ?");
            $stmt->execute([
                $data['phone'],
                $data['locationID'],
                $data['oldPhone']
            ]);
        } else {
            // INSERT new location phone
            $stmt = $pdo->prepare("INSERT INTO LocationPhone (locationID, phone) VALUES (?, ?)");
            $stmt->execute([
                $data['locationID'],
                $data['phone']
            ]);
        }
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

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

        .search-bar {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            font-size: 14px;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Montréal Volleyball Club - Management System</h1>
            <p>COMP 353 Project</p>
            <div class="header-buttons">
                <button class="btn btn-primary" onclick="showMainSystem()">Main System</button>
                <button class="btn btn-secondary" onclick="showEmptyTemplate()">Reports</button>
            </div>
        </div>

        <div class="nav-tabs">
            <button class="nav-tab active" onclick="showSection('locations')">Locations</button>
            <button class="nav-tab" onclick="showSection('personnel')">Personnel</button>
            <button class="nav-tab" onclick="showSection('family')">Family</button>
            <button class="nav-tab" onclick="showSection('members')">Members</button>
            <button class="nav-tab" onclick="showSection('hobbies')">Hobbies</button>
            <button class="nav-tab" onclick="showSection('payments')">Payments</button>
            <button class="nav-tab" onclick="showSection('teams')">Teams</button>
            <button class="nav-tab" onclick="showSection('workinfo')">Work History</button>
            <button class="nav-tab" onclick="showSection('sessions')">Sessions</button>
            <button class="nav-tab" onclick="showSection('postalareas')">Postal Areas</button>
            <button class="nav-tab" onclick="showSection('locationphones')">Location Phones</button>
            <button class="nav-tab" onclick="showSection('familyhistory')">Family History</button>
            <button class="nav-tab" onclick="showSection('teammembers')">Team Members</button>
            <button class="nav-tab" onclick="showSection('emails')">Emails</button>
        </div>

        <div class="content">
            <!-- Locations Section -->
            <div id="locations" class="section active">
                <div class="section-header">
                    <h2 class="section-title">Location Management</h2>
                    <button class="btn" onclick="openModal('locationModal')">Add Location</button>
                </div>
                
                <input type="text" class="search-bar" placeholder="Search locations...">
                
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
                
                <input type="text" class="search-bar" placeholder="Search personnel...">
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>SSN</th>
                            <th>Medicare</th>
                            <th>Role</th>
                            <th>Location</th>
                            <th>Start Date</th>
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
                            echo "<td>" . htmlspecialchars($person['medicare'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($person['role']) . "</td>";
                            echo "<td>" . htmlspecialchars($person['location_name'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($person['startDate'] ?? 'N/A') . "</td>";
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
                
                <input type="text" class="search-bar" placeholder="Search family members...">
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
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
                            echo "<td>" . htmlspecialchars($family['relationshipType'] ?? '') . "</td>";
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
                
                <input type="text" class="search-bar" placeholder="Search members...">
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>DOB</th>
                            <th>Age Group</th>
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
                            echo "<td>" . htmlspecialchars($member['dob']) . "</td>";
                            echo "<td>" . $ageGroup . "</td>";
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

            <!-- Sessions Section -->
            <div id="sessions" class="section">
                <div class="section-header">
                    <h2 class="section-title">Game & Training Sessions</h2>
                    <button class="btn" onclick="openModal('sessionModal')">Schedule Session</button>
                </div>
                
                <div class="filters">
                    <div class="filter-group">
                        <label>Type:</label>
                        <select>
                            <option value="">All Types</option>
                            <option value="game">Game</option>
                            <option value="training">Training</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Date:</label>
                        <input type="date">
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
                                $score = $session['team1Score'] && $session['team2Score'] ? 
                                        $session['team1Score'] . '-' . $session['team2Score'] : 'N/A';
                                $teams = $session['team1_name'] . ' vs ' . ($session['team2_name'] ?? 'TBD');
                                
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($session['sessionType']) . "</td>";
                                echo "<td>" . htmlspecialchars($session['sessionDate'] . ' ' . $session['startTime']) . "</td>";
                                echo "<td>" . htmlspecialchars($session['address']) . "</td>";
                                echo "<td>" . htmlspecialchars($teams) . "</td>";
                                echo "<td>N/A</td>"; // Coach info not available in current structure
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

            <!-- Emails Section -->
            <div id="emails" class="section">
                <div class="section-header">
                    <h2 class="section-title">Email Management</h2>
                    <button class="btn" onclick="generateEmails()">Generate Session Emails</button>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Sender</th>
                            <th>Receiver</th>
                            <th>Subject</th>
                            <th>Preview</th>
                            <th>Sent Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $emails = getEmails($pdo);
                        if (empty($emails)) {
                            echo "<tr><td colspan='7' style='text-align: center;'>No emails found. Use 'Generate Session Emails' to create sample emails.</td></tr>";
                        } else {
                            foreach ($emails as $email) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($email['sender_name'] ?? 'System') . "</td>";
                                echo "<td>" . htmlspecialchars($email['receiver_name'] ?? 'All Members') . "</td>";
                                echo "<td>" . htmlspecialchars($email['subject']) . "</td>";
                                echo "<td>" . htmlspecialchars(substr($email['body'], 0, 50)) . "...</td>";
                                echo "<td>" . htmlspecialchars($email['sent_date']) . "</td>";
                                echo "<td>" . htmlspecialchars(ucfirst($email['status'])) . "</td>";
                                echo "<td class='action-buttons'>";
                                echo "<button class='view-btn' onclick='viewEmail(" . $email['emailID'] . ")'>View</button>";
                                echo "<button class='delete-btn' onclick='deleteEmail(" . $email['emailID'] . ")'>Delete</button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

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
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Team Name</th>
                            <th>Member Name</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $teamMembers = getTeamMembers($pdo);
                        if (empty($teamMembers)) {
                            echo "<tr><td colspan='4' style='text-align: center;'>No team members found.</td></tr>";
                        } else {
                            foreach ($teamMembers as $tm) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($tm['teamName'] ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars($tm['member_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($tm['roleInTeam'] ?? '') . "</td>";
                                echo "<td class='action-buttons'>";
                                echo "<button class='edit-btn' onclick='editTeamMember(" . $tm['teamID'] . ", " . $tm['memberID'] . ", \"" . htmlspecialchars($tm['roleInTeam']) . "\")'>Edit</button>";
                                echo "<button class='delete-btn' onclick='deleteTeamMember(" . $tm['teamID'] . ", " . $tm['memberID'] . ")'>Delete</button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>



            <!-- Question Section -->
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            <button onclick="handleQuestion(7)">Question 7</button>
            <button onclick="handleQuestion(8)">Question 8</button>
            <button onclick="handleQuestion(9)">Question 9</button>
            <button onclick="handleQuestion(10)">Question 10</button>
            <button onclick="handleQuestion(11)">Question 11</button>
            <button onclick="handleQuestion(12)">Question 12</button>
            <button onclick="handleQuestion(13)">Question 13</button>
            <button onclick="handleQuestion(14)">Question 14</button>
            <button onclick="handleQuestion(15)">Question 15</button>
            <button onclick="handleQuestion(16)">Question 16</button>
            <button onclick="handleQuestion(17)">Question 17</button>
            <button onclick="handleQuestion(18)">Question 18</button>
            <button onclick="handleQuestion(19)">Question 19</button>
            </div>

            <script>
            function handleQuestion(qNum) {
                alert("You clicked Question " + qNum);
                // Replace this with your real logic (e.g. AJAX or navigation)
            }
            </script>


            <div class="content">
                <?php
                // Get location list for dropdown
                $locationQuery = $pdo->query("SELECT locationID, name FROM Location");
                $locations = $locationQuery->fetchAll(PDO::FETCH_ASSOC);

                // Get selected locationID from GET, default to first in list
                $selectedLocationID = $_GET['locationID'] ?? ($locations[0]['locationID'] ?? null);
                ?>

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
                // Query based on selected location
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
                            <tr><td colspan="3">No results found for this location.</td></tr>
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
                    <style>
                        table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        th, td {
                            border: 1px solid #ddd;
                            padding: 8px;
                        }
                        th {
                            background-color: #f2f2f2;
                            text-align: left;
                        }
                    </style>
                </table>
            </div>
        </div>
    </div>

    <!-- Include the modals and JavaScript -->
    <?php include 'modals.php'; ?>
    <?php include 'script.php'; ?>
</body>
</html> 