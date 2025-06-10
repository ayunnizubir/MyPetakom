<?php
// db.php

$host = 'localhost';
$db   = 'petakom_merit';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    exit('Database connection failed: ' . $e->getMessage());
}

// Create tables if not exist

$pdo->exec("
CREATE TABLE IF NOT EXISTS user (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    MatricID VARCHAR(20) NOT NULL UNIQUE,
    Name VARCHAR(100) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Role ENUM('student', 'admin') NOT NULL DEFAULT 'student',
    Created_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS merit (
    MeritID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    EventName VARCHAR(255) NOT NULL,
    Date DATE NOT NULL,
    Organizer VARCHAR(255) NOT NULL,
    Position ENUM('Main Committee', 'Committee', 'Participant') NOT NULL,
    Level ENUM('International', 'National', 'State', 'District', 'UMPSA') NOT NULL,
    Marks INT NOT NULL,
    Created_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES user(UserID) ON DELETE CASCADE
) ENGINE=InnoDB;
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS merit_claim (
    ClaimID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    EventName VARCHAR(255) NOT NULL,
    EventDate DATE NOT NULL,
    Organizer VARCHAR(255) NOT NULL,
    Position ENUM('Main Committee', 'Committee', 'Participant') NOT NULL,
    Level ENUM('International', 'National', 'State', 'District', 'UMPSA') NOT NULL,
    Claim_Status ENUM('Pending', 'Submitted', 'Approved', 'Rejected') DEFAULT 'Pending',
    Submitted_Date DATETIME DEFAULT CURRENT_TIMESTAMP,
    Supporting_Doc VARCHAR(255) NOT NULL,
    FOREIGN KEY (UserID) REFERENCES user(UserID) ON DELETE CASCADE
) ENGINE=InnoDB;
");
?>
