<?php
require_once 'db.php';

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    die("Error: Missing ID or status.");
}

$id = $_GET['id'];
$status = $_GET['status'];

$sql = "UPDATE merit_claim SET Claim_Status='$status' WHERE ClaimID=$id";
if ($conn->query($sql)) {
    header("Location: manage_merit.php");
    exit;
} else {
    die("SQL Error: " . $conn->error);
}
?>