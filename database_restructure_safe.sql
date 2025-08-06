-- Safe Database Restructure Script
-- This script will restructure the database to only include the 6 required tables

-- Step 1: First, let's check what data we have
-- (This will be run manually to understand the current state)

-- Step 2: Create backup of current data
CREATE TABLE IF NOT EXISTS Personnel_backup AS SELECT * FROM Personnel;
CREATE TABLE IF NOT EXISTS FamilyMember_backup AS SELECT * FROM FamilyMember;
CREATE TABLE IF NOT EXISTS ClubMember_backup AS SELECT * FROM ClubMember;
CREATE TABLE IF NOT EXISTS Person_backup AS SELECT * FROM Person;

-- Step 3: Add missing attributes to existing tables (with default values)

-- Add personal information fields to Personnel table
ALTER TABLE Personnel 
ADD COLUMN firstName VARCHAR(50) DEFAULT '' AFTER employeeID,
ADD COLUMN lastName VARCHAR(50) DEFAULT '' AFTER firstName,
ADD COLUMN dob DATE DEFAULT '1900-01-01' AFTER lastName,
ADD COLUMN ssn VARCHAR(15) DEFAULT '' AFTER dob,
ADD COLUMN medicare VARCHAR(20) DEFAULT '' AFTER ssn,
ADD COLUMN address VARCHAR(225) DEFAULT '' AFTER medicare,
ADD COLUMN postalCode VARCHAR(10) DEFAULT '' AFTER address,
ADD COLUMN phone VARCHAR(20) DEFAULT '' AFTER postalCode,
ADD COLUMN email VARCHAR(100) DEFAULT '' AFTER phone;

-- Add personal information fields to FamilyMember table
ALTER TABLE FamilyMember 
ADD COLUMN firstName VARCHAR(50) DEFAULT '' AFTER familyMemID,
ADD COLUMN lastName VARCHAR(50) DEFAULT '' AFTER firstName,
ADD COLUMN dob DATE DEFAULT '1900-01-01' AFTER lastName,
ADD COLUMN ssn VARCHAR(15) DEFAULT '' AFTER dob,
ADD COLUMN medicare VARCHAR(20) DEFAULT '' AFTER ssn,
ADD COLUMN address VARCHAR(225) DEFAULT '' AFTER medicare,
ADD COLUMN postalCode VARCHAR(10) DEFAULT '' AFTER address,
ADD COLUMN phone VARCHAR(20) DEFAULT '' AFTER postalCode,
ADD COLUMN email VARCHAR(100) DEFAULT '' AFTER phone,
ADD COLUMN locationID INT DEFAULT 1 AFTER email;

-- Add personal information fields to ClubMember table
ALTER TABLE ClubMember 
ADD COLUMN firstName VARCHAR(50) DEFAULT '' AFTER memberID,
ADD COLUMN lastName VARCHAR(50) DEFAULT '' AFTER firstName,
ADD COLUMN dob DATE DEFAULT '1900-01-01' AFTER lastName,
ADD COLUMN ssn VARCHAR(15) DEFAULT '' AFTER dob,
ADD COLUMN medicare VARCHAR(20) DEFAULT '' AFTER ssn,
ADD COLUMN address VARCHAR(225) DEFAULT '' AFTER medicare,
ADD COLUMN postalCode VARCHAR(10) DEFAULT '' AFTER address,
ADD COLUMN phone VARCHAR(20) DEFAULT '' AFTER postalCode,
ADD COLUMN email VARCHAR(100) DEFAULT '' AFTER phone,
ADD COLUMN familyMemID INT DEFAULT NULL AFTER email;

-- Add missing fields to Location table
ALTER TABLE Location 
ADD COLUMN city VARCHAR(50) DEFAULT 'Montreal' AFTER address,
ADD COLUMN province VARCHAR(50) DEFAULT 'Quebec' AFTER city,
ADD COLUMN phone VARCHAR(20) DEFAULT '' AFTER province;

-- Add missing fields to Session table
ALTER TABLE Session 
ADD COLUMN locationID INT DEFAULT 1 AFTER address,
ADD COLUMN coachID INT DEFAULT 1 AFTER locationID;

-- Step 4: Migrate data from Person table to appropriate tables

-- Migrate Personnel data (only if SSN is not already set)
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
    p.email = per.email
WHERE p.ssn = '' OR p.ssn IS NULL;

-- Migrate FamilyMember data (only if SSN is not already set)
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
    fm.email = per.email
WHERE fm.ssn = '' OR fm.ssn IS NULL;

-- Migrate ClubMember data (only if SSN is not already set)
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
    cm.email = per.email
WHERE cm.ssn = '' OR cm.ssn IS NULL;

-- Step 5: Now make the fields NOT NULL after data migration

-- Update Personnel table
ALTER TABLE Personnel 
MODIFY COLUMN firstName VARCHAR(50) NOT NULL,
MODIFY COLUMN lastName VARCHAR(50) NOT NULL,
MODIFY COLUMN dob DATE NOT NULL,
MODIFY COLUMN ssn VARCHAR(15) NOT NULL,
MODIFY COLUMN address VARCHAR(225) NOT NULL,
MODIFY COLUMN postalCode VARCHAR(10) NOT NULL,
MODIFY COLUMN phone VARCHAR(20) NOT NULL;

-- Update FamilyMember table
ALTER TABLE FamilyMember 
MODIFY COLUMN firstName VARCHAR(50) NOT NULL,
MODIFY COLUMN lastName VARCHAR(50) NOT NULL,
MODIFY COLUMN dob DATE NOT NULL,
MODIFY COLUMN ssn VARCHAR(15) NOT NULL,
MODIFY COLUMN address VARCHAR(225) NOT NULL,
MODIFY COLUMN postalCode VARCHAR(10) NOT NULL,
MODIFY COLUMN phone VARCHAR(20) NOT NULL;

-- Update ClubMember table
ALTER TABLE ClubMember 
MODIFY COLUMN firstName VARCHAR(50) NOT NULL,
MODIFY COLUMN lastName VARCHAR(50) NOT NULL,
MODIFY COLUMN dob DATE NOT NULL,
MODIFY COLUMN ssn VARCHAR(15) NOT NULL,
MODIFY COLUMN address VARCHAR(225) NOT NULL,
MODIFY COLUMN postalCode VARCHAR(10) NOT NULL,
MODIFY COLUMN phone VARCHAR(20) NOT NULL;

-- Update Location table
ALTER TABLE Location 
MODIFY COLUMN city VARCHAR(50) NOT NULL,
MODIFY COLUMN province VARCHAR(50) NOT NULL;

-- Step 6: Add unique constraints after data is properly set

-- Add unique constraints for SSN and Medicare
ALTER TABLE Personnel ADD UNIQUE KEY uk_personnel_ssn (ssn);
ALTER TABLE Personnel ADD UNIQUE KEY uk_personnel_medicare (medicare);
ALTER TABLE FamilyMember ADD UNIQUE KEY uk_familymember_ssn (ssn);
ALTER TABLE FamilyMember ADD UNIQUE KEY uk_familymember_medicare (medicare);
ALTER TABLE ClubMember ADD UNIQUE KEY uk_clubmember_ssn (ssn);
ALTER TABLE ClubMember ADD UNIQUE KEY uk_clubmember_medicare (medicare);

-- Step 7: Add foreign key constraints

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

-- Step 8: Remove unnecessary tables

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

-- Step 9: Create indexes for better performance

CREATE INDEX idx_personnel_name ON Personnel(lastName, firstName);
CREATE INDEX idx_familymember_name ON FamilyMember(lastName, firstName);
CREATE INDEX idx_clubmember_name ON ClubMember(lastName, firstName);
CREATE INDEX idx_session_date ON Session(sessionDate);
CREATE INDEX idx_payment_date ON Payment(paymentDate);
CREATE INDEX idx_location_name ON Location(name);

-- Step 10: Update sample data for any empty records

-- Update Location data with proper city/province if missing
UPDATE Location SET city = 'Montreal', province = 'Quebec' WHERE city = 'Montreal' OR city = '';
UPDATE Location SET phone = '514-555-0000' WHERE phone = '';

-- Update Personnel data with sample data if missing
UPDATE Personnel SET 
    firstName = CONCAT('Coach', employeeID),
    lastName = CONCAT('Staff', employeeID),
    dob = '1980-01-01',
    ssn = CONCAT('123-45-', LPAD(employeeID, 4, '0')),
    medicare = CONCAT('MED', LPAD(employeeID, 9, '0')),
    address = CONCAT(employeeID, ' Main St'),
    postalCode = 'H1A 1A1',
    phone = '514-555-0000',
    email = CONCAT('coach', employeeID, '@mvc.ca')
WHERE firstName = '' OR firstName IS NULL;

-- Update FamilyMember data with sample data if missing
UPDATE FamilyMember SET 
    firstName = CONCAT('Family', familyMemID),
    lastName = CONCAT('Member', familyMemID),
    dob = '1970-01-01',
    ssn = CONCAT('111-11-', LPAD(familyMemID, 4, '0')),
    medicare = CONCAT('MED', LPAD(familyMemID, 9, '0')),
    address = CONCAT(familyMemID, ' Family St'),
    postalCode = 'H1A 1A1',
    phone = '514-555-0000',
    email = CONCAT('family', familyMemID, '@email.com'),
    locationID = 1
WHERE firstName = '' OR firstName IS NULL;

-- Update ClubMember data with sample data if missing
UPDATE ClubMember SET 
    firstName = CONCAT('Member', memberID),
    lastName = CONCAT('Person', memberID),
    dob = '1990-01-01',
    ssn = CONCAT('333-33-', LPAD(memberID, 4, '0')),
    medicare = CONCAT('MED', LPAD(memberID, 9, '0')),
    address = CONCAT(memberID, ' Member Ave'),
    postalCode = 'H1A 1A1',
    phone = '514-555-0000',
    email = CONCAT('member', memberID, '@email.com'),
    locationID = 1
WHERE firstName = '' OR firstName IS NULL;

-- Step 11: Update Location managerID to reference Personnel
UPDATE Location SET managerID = 1 WHERE locationID = 1 AND managerID IS NULL;
UPDATE Location SET managerID = 2 WHERE locationID = 2 AND managerID IS NULL;
UPDATE Location SET managerID = 3 WHERE locationID = 3 AND managerID IS NULL; 