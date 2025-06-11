<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit;
}

$userID = $_SESSION['UserID'];

// Check if 'id' is provided via GET
if (!isset($_GET['id'])) {
    header('Location: my_claims.php'); // Redirect if no id
    exit;
}

$claimID = intval($_GET['id']); // Always sanitize

// First, verify that this claim belongs to this user
$stmt = $pdo->prepare("SELECT * FROM merit_claim WHERE ClaimID = ? AND UserID = ?");
$stmt->execute([$claimID, $userID]);
$claim = $stmt->fetch();

if (!$claim) {
    // Either claim not found or doesn't belong to user
    header('Location: my_claims.php?error=notfound');
    exit;
}

// OPTIONAL: Delete supporting document file if exists
if (!empty($claim['Supporting_Doc']) && file_exists($claim['Supporting_Doc'])) {
    unlink($claim['Supporting_Doc']);
}

// Now delete the claim from database
$stmt = $pdo->prepare("DELETE FROM merit_claim WHERE ClaimID = ?");
$stmt->execute([$claimID]);

header('Location: view_claimed.php?success=deleted');
exit;
?>
