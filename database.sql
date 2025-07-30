-- Montréal Volleyball Club Database Schema
-- COMP 353 Project

-- Create database
CREATE DATABASE IF NOT EXISTS volleyball_club;
USE volleyball_club;

-- Locations table
CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type ENUM('Head', 'Branch') NOT NULL,
    address VARCHAR(500) NOT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(50) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    web_address VARCHAR(255),
    max_capacity INT NOT NULL,
    general_manager_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Personnel table
CREATE TABLE personnel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    ssn VARCHAR(11) NOT NULL UNIQUE,
    medicare VARCHAR(20) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    address VARCHAR(500) NOT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(50) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    role ENUM('General Manager', 'Deputy Manager', 'Treasurer', 'Secretary', 'Administrator', 'Captain', 'Coach', 'Assistant Coach', 'Other') NOT NULL,
    mandate ENUM('Volunteer', 'Salaried') NOT NULL,
    location_id INT,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL
);

-- Family members table
CREATE TABLE family_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('Primary', 'Secondary') NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    ssn VARCHAR(11) NOT NULL UNIQUE,
    medicare VARCHAR(20) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    address VARCHAR(500) NOT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(50) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    location_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL
);

-- Members table
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    height VARCHAR(10),
    weight DECIMAL(5,2),
    location_id INT NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(255),
    address VARCHAR(500),
    hobbies TEXT,
    family_id INT,
    relationship ENUM('parent', 'guardian', 'sibling'),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE RESTRICT,
    FOREIGN KEY (family_id) REFERENCES family_members(id) ON DELETE SET NULL
);

-- Payments table
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'check', 'credit', 'debit') NOT NULL,
    payment_date DATE NOT NULL,
    year YEAR NOT NULL,
    type ENUM('membership', 'donation') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- Teams table
CREATE TABLE teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    head_coach_id INT,
    location_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (head_coach_id) REFERENCES personnel(id) ON DELETE SET NULL,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE RESTRICT
);

-- Team players junction table
CREATE TABLE team_players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    member_id INT NOT NULL,
    joined_date DATE NOT NULL,
    left_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    UNIQUE KEY unique_team_player (team_id, member_id)
);

-- Sessions table
CREATE TABLE sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('game', 'training') NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    location_id INT NOT NULL,
    team1_id INT,
    team2_id INT,
    coach_id INT,
    score VARCHAR(20),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE RESTRICT,
    FOREIGN KEY (team1_id) REFERENCES teams(id) ON DELETE SET NULL,
    FOREIGN KEY (team2_id) REFERENCES teams(id) ON DELETE SET NULL,
    FOREIGN KEY (coach_id) REFERENCES personnel(id) ON DELETE SET NULL
);

-- Emails table
CREATE TABLE emails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT,
    receiver_id INT,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    sent_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
    FOREIGN KEY (sender_id) REFERENCES personnel(id) ON DELETE SET NULL,
    FOREIGN KEY (receiver_id) REFERENCES members(id) ON DELETE SET NULL
);

-- Add foreign key for general manager in locations table
ALTER TABLE locations ADD FOREIGN KEY (general_manager_id) REFERENCES personnel(id) ON DELETE SET NULL;

-- Insert sample data
INSERT INTO locations (name, type, address, city, province, postal_code, phone, web_address, max_capacity) VALUES
('Downtown Sports Center', 'Head', '123 Main Street', 'Montreal', 'QC', 'H1A 1A1', '514-555-0100', 'www.downtownsports.com', 200),
('West End Gym', 'Branch', '456 West Avenue', 'Montreal', 'QC', 'H2B 2B2', '514-555-0200', 'www.westendgym.com', 150),
('East Side Arena', 'Branch', '789 East Boulevard', 'Montreal', 'QC', 'H3C 3C3', '514-555-0300', 'www.eastsidearena.com', 180);

INSERT INTO personnel (first_name, last_name, dob, ssn, medicare, phone, address, city, province, postal_code, email, role, mandate, location_id, start_date) VALUES
('John', 'Smith', '1980-05-15', '123-45-6789', 'MED123456789', '514-555-0101', '100 Manager St', 'Montreal', 'QC', 'H1A 1A2', 'john.smith@volleyball.ca', 'General Manager', 'Salaried', 1, '2020-01-15'),
('Sarah', 'Johnson', '1985-08-22', '234-56-7890', 'MED234567890', '514-555-0102', '200 Coach Ave', 'Montreal', 'QC', 'H1A 1A3', 'sarah.johnson@volleyball.ca', 'Coach', 'Salaried', 1, '2020-02-01'),
('Mike', 'Davis', '1990-03-10', '345-67-8901', 'MED345678901', '514-555-0103', '300 Assistant Blvd', 'Montreal', 'QC', 'H1A 1A4', 'mike.davis@volleyball.ca', 'Assistant Coach', 'Volunteer', 2, '2020-03-01');

INSERT INTO family_members (type, first_name, last_name, dob, ssn, medicare, phone, address, city, province, postal_code, email, location_id) VALUES
('Primary', 'Robert', 'Wilson', '1975-12-05', '456-78-9012', 'MED456789012', '514-555-0201', '400 Family St', 'Montreal', 'QC', 'H2B 2B3', 'robert.wilson@email.com', 1),
('Primary', 'Lisa', 'Brown', '1980-07-18', '567-89-0123', 'MED567890123', '514-555-0202', '500 Parent Ave', 'Montreal', 'QC', 'H2B 2B4', 'lisa.brown@email.com', 2);

INSERT INTO members (first_name, last_name, dob, height, weight, location_id, phone, email, address, hobbies, family_id, relationship) VALUES
('Alex', 'Wilson', '2005-04-12', '5\'8"', 140.5, 1, '514-555-0301', 'alex.wilson@email.com', '400 Family St', 'Volleyball, Reading', 1, 'parent'),
('Emma', 'Brown', '2007-09-25', '5\'6"', 125.0, 2, '514-555-0302', 'emma.brown@email.com', '500 Parent Ave', 'Volleyball, Swimming', 2, 'parent'),
('David', 'Miller', '2000-11-08', '6\'0"', 180.0, 1, '514-555-0303', 'david.miller@email.com', '600 Adult St', 'Volleyball, Basketball', NULL, NULL);

INSERT INTO payments (member_id, amount, payment_method, payment_date, year, type) VALUES
(1, 150.00, 'credit', '2025-01-15', 2025, 'membership'),
(2, 150.00, 'debit', '2025-01-20', 2025, 'membership'),
(3, 200.00, 'cash', '2025-01-25', 2025, 'membership'),
(1, 50.00, 'check', '2025-02-01', 2025, 'donation');

INSERT INTO teams (name, gender, head_coach_id, location_id) VALUES
('Eagles', 'male', 2, 1),
('Falcons', 'female', 3, 2),
('Hawks', 'male', 2, 1);

INSERT INTO team_players (team_id, member_id, joined_date) VALUES
(1, 1, '2025-01-15'),
(1, 3, '2025-01-15'),
(2, 2, '2025-01-20');

INSERT INTO sessions (type, date, time, location_id, team1_id, team2_id, coach_id, score) VALUES
('game', '2025-02-15', '14:00:00', 1, 1, 3, 2, '3-1'),
('training', '2025-02-16', '16:00:00', 1, 1, NULL, 2, NULL),
('game', '2025-02-17', '15:00:00', 2, 2, NULL, 3, '2-1');

-- Update locations with general managers
UPDATE locations SET general_manager_id = 1 WHERE id = 1;
UPDATE locations SET general_manager_id = 2 WHERE id = 2;
UPDATE locations SET general_manager_id = 3 WHERE id = 3; 