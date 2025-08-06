<?php
// bootstrap.php

session_start();

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

// Function used for Q17
function getQualifiedFamilyMembersQ17(PDO $pdo, int $locationID): array {
    $stmt = $pdo->prepare("
        SELECT DISTINCT p.firstName, p.lastName, p.phone
        FROM Person p
        JOIN FamilyMember fm ON p.pID = fm.familyMemID
        JOIN FamilyHistory fh ON fh.familyID = fm.familyMemID
        JOIN ClubMember cm ON fh.memberID = cm.memberID
        JOIN SessionParticipation sp ON sp.participantID = p.pID
        JOIN Session s ON sp.sessionID = s.sessionID
        WHERE cm.status = 'Active'
        AND cm.locationID = :locationID
        AND s.locationID = :locationID
        AND sp.roleInSession = 'HeadCoach'
    ");
    $stmt->execute(['locationID' => $locationID]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Q18 function
function getQualifiedFamilyMembersQ18(PDO $pdo): array {
    $stmt = $pdo->prepare("
        SELECT cm.memberID, p.firstName, p.lastName, TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS age,
               p.phone, p.email, l.name AS locationName
        FROM ClubMember cm
        JOIN Person p ON cm.memberID = p.pID
        JOIN Location l ON cm.locationID = l.locationID
        WHERE cm.status = 'Active'
          AND cm.memberID IN (
              SELECT DISTINCT sp.participantID
              FROM SessionParticipation sp
              JOIN Session s ON sp.sessionID = s.sessionID
              WHERE s.sessionType = 'Game'
          )
          AND cm.memberID NOT IN (
              SELECT sp.participantID
              FROM SessionParticipation sp
              JOIN Session s ON sp.sessionID = s.sessionID
              WHERE s.sessionType = 'Game'
                AND (
                    (sp.teamID = s.team1ID AND s.team1Score < s.team2Score) OR
                    (sp.teamID = s.team2ID AND s.team2Score < s.team1Score)
                )
          )
        ORDER BY l.name ASC, cm.memberID ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Q19 function
function getQualifiedFamilyMembersQ19(PDO $pdo): array {
    $stmt = $pdo->prepare("
        SELECT p.firstName, p.lastName, COUNT(DISTINCT cm.memberID) AS minorCount,
               p.phone, p.email, l.name AS locationName, pl.role
        FROM Person p
        JOIN Personnel pl ON p.pID = pl.employeeID AND pl.mandate = 'Volunteer'
        JOIN FamilyMember fm ON p.pID = fm.familyMemID
        JOIN FamilyHistory fh ON fh.familyID = fm.familyMemID
        JOIN ClubMember cm ON fh.memberID = cm.memberID AND cm.memberType = 'Minor'
        JOIN WorkInfo wi ON pl.employeeID = wi.employeeID
        JOIN Location l ON wi.locationID = l.locationID
        WHERE wi.endDate IS NULL
        GROUP BY p.pID, l.name, pl.role
        ORDER BY l.name ASC, pl.role ASC, p.firstName ASC, p.lastName ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
