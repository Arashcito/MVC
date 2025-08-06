-- Database Restructure Script
-- This script will restructure the database to only include:
-- 1. Location
-- 2. Personnel  
-- 3. FamilyMember
-- 4. ClubMember
-- 5. Session
-- 6. Payment

-- Step 1: Add missing attributes to existing tables

-- Add personal information fields to Personnel table
ALTER TABLE Personnel 
ADD COLUMN firstName VARCHAR(50) NOT NULL AFTER employeeID,
ADD COLUMN lastName VARCHAR(50) NOT NULL AFTER firstName,
ADD COLUMN dob DATE NOT NULL AFTER lastName,
ADD COLUMN ssn VARCHAR(15) UNIQUE NOT NULL AFTER dob,
ADD COLUMN medicare VARCHAR(20) UNIQUE AFTER ssn,
ADD COLUMN address VARCHAR(225) NOT NULL AFTER medicare,
ADD COLUMN postalCode VARCHAR(10) NOT NULL AFTER address,
ADD COLUMN phone VARCHAR(20) NOT NULL AFTER postalCode,
ADD COLUMN email VARCHAR(100) AFTER phone;

-- Add personal information fields to FamilyMember table
ALTER TABLE FamilyMember 
ADD COLUMN firstName VARCHAR(50) NOT NULL AFTER familyMemID,
ADD COLUMN lastName VARCHAR(50) NOT NULL AFTER firstName,
ADD COLUMN dob DATE NOT NULL AFTER lastName,
ADD COLUMN ssn VARCHAR(15) UNIQUE NOT NULL AFTER dob,
ADD COLUMN medicare VARCHAR(20) UNIQUE AFTER ssn,
ADD COLUMN address VARCHAR(225) NOT NULL AFTER medicare,
ADD COLUMN postalCode VARCHAR(10) NOT NULL AFTER address,
ADD COLUMN phone VARCHAR(20) NOT NULL AFTER postalCode,
ADD COLUMN email VARCHAR(100) AFTER phone,
ADD COLUMN locationID INT AFTER email;

-- Add personal information fields to ClubMember table
ALTER TABLE ClubMember 
ADD COLUMN firstName VARCHAR(50) NOT NULL AFTER memberID,
ADD COLUMN lastName VARCHAR(50) NOT NULL AFTER firstName,
ADD COLUMN dob DATE NOT NULL AFTER lastName,
ADD COLUMN ssn VARCHAR(15) UNIQUE NOT NULL AFTER dob,
ADD COLUMN medicare VARCHAR(20) UNIQUE AFTER ssn,
ADD COLUMN address VARCHAR(225) NOT NULL AFTER medicare,
ADD COLUMN postalCode VARCHAR(10) NOT NULL AFTER address,
ADD COLUMN phone VARCHAR(20) NOT NULL AFTER postalCode,
ADD COLUMN email VARCHAR(100) AFTER phone,
ADD COLUMN familyMemID INT AFTER email;

-- Add missing fields to Location table
ALTER TABLE Location 
ADD COLUMN city VARCHAR(50) NOT NULL AFTER address,
ADD COLUMN province VARCHAR(50) NOT NULL AFTER city,
ADD COLUMN phone VARCHAR(20) AFTER province;

-- Add missing fields to Session table
ALTER TABLE Session 
ADD COLUMN locationID INT AFTER address,
ADD COLUMN coachID INT AFTER locationID;

-- Step 2: Migrate data from Person table to appropriate tables

-- Migrate Personnel data
UPDATE Personnel p 
JOIN Person per ON p.employeeID = per.pID 
SET p.firstName = per.firstName,
    p.lastName = per.lastName,
    p.dob = per.dob,
    p.ssn = per.ssn,
    p.medicare = per.medicare,
    p.address = per.address,
    p.postalCode = per.postalCode,
    p.phone = per.phone,
    p.email = per.email;

-- Migrate FamilyMember data (assuming FamilyMember has a relationship with Person)
-- Note: This might need adjustment based on actual data relationships
UPDATE FamilyMember fm 
JOIN Person per ON fm.familyMemID = per.pID 
SET fm.firstName = per.firstName,
    fm.lastName = per.lastName,
    fm.dob = per.dob,
    fm.ssn = per.ssn,
    fm.medicare = per.medicare,
    fm.address = per.address,
    fm.postalCode = per.postalCode,
    fm.phone = per.phone,
    fm.email = per.email;

-- Migrate ClubMember data (assuming ClubMember has a relationship with Person)
-- Note: This might need adjustment based on actual data relationships
UPDATE ClubMember cm 
JOIN Person per ON cm.memberID = per.pID 
SET cm.firstName = per.firstName,
    cm.lastName = per.lastName,
    cm.dob = per.dob,
    cm.ssn = per.ssn,
    cm.medicare = per.medicare,
    cm.address = per.address,
    cm.postalCode = per.postalCode,
    cm.phone = per.phone,
    cm.email = per.email;

-- Step 3: Add foreign key constraints

-- Add foreign key for Personnel to Location (managerID)
ALTER TABLE Personnel 
ADD CONSTRAINT fk_personnel_location 
FOREIGN KEY (managerID) REFERENCES Location(locationID) ON DELETE SET NULL;

-- Add foreign key for FamilyMember to Location
ALTER TABLE FamilyMember 
ADD CONSTRAINT fk_familymember_location 
FOREIGN KEY (locationID) REFERENCES Location(locationID) ON DELETE SET NULL;

-- Add foreign key for ClubMember to Location
ALTER TABLE ClubMember 
ADD CONSTRAINT fk_clubmember_location 
FOREIGN KEY (locationID) REFERENCES Location(locationID) ON DELETE CASCADE;

-- Add foreign key for ClubMember to FamilyMember
ALTER TABLE ClubMember 
ADD CONSTRAINT fk_clubmember_familymember 
FOREIGN KEY (familyMemID) REFERENCES FamilyMember(familyMemID) ON DELETE SET NULL;

-- Add foreign key for Session to Location
ALTER TABLE Session 
ADD CONSTRAINT fk_session_location 
FOREIGN KEY (locationID) REFERENCES Location(locationID) ON DELETE SET NULL;

-- Add foreign key for Session to Personnel (coach)
ALTER TABLE Session 
ADD CONSTRAINT fk_session_coach 
FOREIGN KEY (coachID) REFERENCES Personnel(employeeID) ON DELETE SET NULL;

-- Add foreign key for Payment to ClubMember
ALTER TABLE Payment 
ADD CONSTRAINT fk_payment_clubmember 
FOREIGN KEY (memberID) REFERENCES ClubMember(memberID) ON DELETE CASCADE;

-- Step 4: Remove unnecessary tables

-- Drop tables that are no longer needed
DROP TABLE IF EXISTS Emails;
DROP TABLE IF EXISTS FamilyHistory;
DROP TABLE IF EXISTS Hobby;
DROP TABLE IF EXISTS LocationPhone;
DROP TABLE IF EXISTS MemberHobby;
DROP TABLE IF EXISTS PostalAreaInfo;
DROP TABLE IF EXISTS Team;
DROP TABLE IF EXISTS TeamMember;
DROP TABLE IF EXISTS WorkInfo;
DROP TABLE IF EXISTS YearlyPayments;
DROP TABLE IF EXISTS Person;

-- Step 5: Create indexes for better performance

CREATE INDEX idx_personnel_name ON Personnel(lastName, firstName);
CREATE INDEX idx_familymember_name ON FamilyMember(lastName, firstName);
CREATE INDEX idx_clubmember_name ON ClubMember(lastName, firstName);
CREATE INDEX idx_session_date ON Session(sessionDate);
CREATE INDEX idx_payment_date ON Payment(paymentDate);
CREATE INDEX idx_location_name ON Location(name);

-- Step 6: Insert sample data for testing (if tables are empty)

-- Insert sample Location data if empty
INSERT IGNORE INTO Location (name, type, address, city, province, postalCode, phone, webAddress, maxCapacity) VALUES
('Montreal Main Branch', 'HEAD', '100 Volleyball Ave', 'Montreal', 'Quebec', 'H1A 1A1', '514-555-0100', 'www.mvc-main.ca', 200),
('Montreal East Branch', 'BRANCH', '200 Sports Blvd', 'Montreal', 'Quebec', 'H2B 2B2', '514-555-0200', 'www.mvc-east.ca', 150),
('Laval Branch', 'BRANCH', '300 Athletic St', 'Laval', 'Quebec', 'H7P 3C3', '450-555-0300', 'www.mvc-laval.ca', 120);

-- Insert sample Personnel data if empty
INSERT IGNORE INTO Personnel (employeeID, firstName, lastName, dob, ssn, medicare, address, postalCode, phone, email, role, mandate) VALUES
(1, 'Marie', 'Dubois', '1980-05-15', '123-45-6789', 'MED123456789', '100 Main St', 'H1A 1A1', '514-555-0101', 'marie.dubois@mvc.ca', 'Coach', 'Salaried'),
(2, 'Robert', 'Johnson', '1975-08-22', '234-56-7890', 'MED234567890', '200 Oak Ave', 'H2B 2B2', '514-555-0202', 'robert.johnson@mvc.ca', 'Coach', 'Salaried'),
(3, 'David', 'Wilson', '1982-03-10', '345-67-8901', 'MED345678901', '300 Pine Rd', 'H7P 3C3', '450-555-0303', 'david.wilson@mvc.ca', 'Coach', 'Salaried');

-- Insert sample FamilyMember data if empty
INSERT IGNORE INTO FamilyMember (familyMemID, firstName, lastName, dob, ssn, medicare, address, postalCode, phone, email, relationshipType, locationID) VALUES
(1, 'John', 'Smith', '1970-01-01', '111-11-1111', 'MED111111111', '100 Family St', 'H1A 1A1', '514-555-1111', 'john.smith@email.com', 'Father', 1),
(2, 'Jane', 'Smith', '1972-02-02', '222-22-2222', 'MED222222222', '100 Family St', 'H1A 1A1', '514-555-2222', 'jane.smith@email.com', 'Mother', 1);

-- Insert sample ClubMember data if empty
INSERT IGNORE INTO ClubMember (memberID, firstName, lastName, dob, ssn, medicare, address, postalCode, phone, email, locationID, memberType, status, height, weight, familyMemID) VALUES
(1, 'Alice', 'Smith', '2005-06-15', '333-33-3333', 'MED333333333', '100 Family St', 'H1A 1A1', '514-555-3333', 'alice.smith@email.com', 1, 'Minor', 'Active', 165.5, 55.0, 1),
(2, 'Bob', 'Johnson', '1990-12-20', '444-44-4444', 'MED444444444', '200 Member Ave', 'H2B 2B2', '514-555-4444', 'bob.johnson@email.com', 2, 'Major', 'Active', 180.0, 75.0, NULL);

-- Insert sample Session data if empty
INSERT IGNORE INTO Session (sessionID, sessionType, sessionDate, startTime, address, locationID, coachID, team1ID, team2ID, team1Score, team2Score) VALUES
(1, 'Game', '2024-10-15', '18:00:00', '100 Volleyball Ave, Montreal', 1, 1, 1, 2, 3, 1),
(2, 'Training', '2024-10-22', '19:00:00', '200 Sports Blvd, Montreal', 2, 2, 1, NULL, NULL, NULL);

-- Insert sample Payment data if empty
INSERT IGNORE INTO Payment (paymentID, memberID, paymentDate, amount, method, membershipYear, installmentNo) VALUES
(1, 1, '2024-01-15', 25.00, 'Cash', 2024, 1),
(2, 2, '2024-01-20', 50.00, 'Debit', 2024, 1);

-- Step 7: Update Location managerID to reference Personnel
UPDATE Location SET managerID = 1 WHERE locationID = 1;
UPDATE Location SET managerID = 2 WHERE locationID = 2;
UPDATE Location SET managerID = 3 WHERE locationID = 3; 