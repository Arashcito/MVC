-- Montréal Volleyball Club Database Schema
-- This file contains all the necessary tables for the management system

-- Create Sessions table if it doesn't exist
CREATE TABLE IF NOT EXISTS Sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('game', 'training') NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    locationID INT,
    coachID INT,
    team1ID INT,
    team2ID INT,
    score VARCHAR(20),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (locationID) REFERENCES Location(locationID) ON DELETE SET NULL,
    FOREIGN KEY (coachID) REFERENCES Personnel(pID) ON DELETE SET NULL,
    FOREIGN KEY (team1ID) REFERENCES Teams(teamID) ON DELETE SET NULL,
    FOREIGN KEY (team2ID) REFERENCES Teams(teamID) ON DELETE SET NULL
);

-- Create Emails table if it doesn't exist
CREATE TABLE IF NOT EXISTS Emails (
    emailID INT AUTO_INCREMENT PRIMARY KEY,
    senderID INT,
    receiverID INT,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    sent_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('sent', 'delivered', 'read') DEFAULT 'sent',
    FOREIGN KEY (senderID) REFERENCES Personnel(pID) ON DELETE SET NULL,
    FOREIGN KEY (receiverID) REFERENCES ClubMembers(memberID) ON DELETE SET NULL
);

-- Create TeamMembers table for player assignments if it doesn't exist
CREATE TABLE IF NOT EXISTS TeamMembers (
    teamID INT,
    memberID INT,
    join_date DATE NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    PRIMARY KEY (teamID, memberID),
    FOREIGN KEY (teamID) REFERENCES Teams(teamID) ON DELETE CASCADE,
    FOREIGN KEY (memberID) REFERENCES ClubMembers(memberID) ON DELETE CASCADE
);

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_sessions_date ON Sessions(date);
CREATE INDEX IF NOT EXISTS idx_sessions_location ON Sessions(locationID);
CREATE INDEX IF NOT EXISTS idx_emails_sent_date ON Emails(sent_date);
CREATE INDEX IF NOT EXISTS idx_team_members_status ON TeamMembers(status);

-- Insert sample data for Sessions if table is empty
INSERT IGNORE INTO Sessions (type, date, time, locationID, coachID, score) VALUES
('training', '2024-01-15', '18:00:00', 1, 1, NULL),
('game', '2024-01-20', '19:00:00', 1, 1, '25-23, 25-21'),
('training', '2024-01-22', '17:30:00', 2, 2, NULL),
('game', '2024-01-25', '20:00:00', 2, 2, '23-25, 25-22, 15-13');

-- Insert sample data for Emails if table is empty
INSERT IGNORE INTO Emails (senderID, receiverID, subject, body, status) VALUES
(1, 1, 'Training Session Reminder', 'Hello! This is a reminder about tomorrow\'s training session at 6 PM.', 'sent'),
(1, 2, 'Game Schedule Update', 'The game scheduled for this weekend has been moved to next Saturday.', 'delivered');

-- Insert sample data for TeamMembers if table is empty
INSERT IGNORE INTO TeamMembers (teamID, memberID, join_date, status) VALUES
(1, 1, '2024-01-01', 'active'),
(1, 2, '2024-01-01', 'active'),
(2, 3, '2024-01-01', 'active'),
(2, 4, '2024-01-01', 'active'); 