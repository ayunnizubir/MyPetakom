<?php
// delete.php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    header("Location: profile_management.php");
    exit();
}

// Database configuration
$host = 'localhost';
$dbname = 'mypetakom';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Delete user from database
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    // Destroy session and redirect to login
    session_destroy();
    header("Location: login.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['error'] = "Failed to delete profile: " . $e->getMessage();
    header("Location: profile_management.php");
    exit();
}
?>