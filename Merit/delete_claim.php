<?php 
require_once 'db.php'; 

if (!isset($_GET['id'])) {
    die("Error: Claim ID not provided.");
}

$claim_id = $_GET['id'];
$sql_delete = "DELETE FROM merit_claim WHERE ClaimID='$claim_id'";
if ($conn->query($sql_delete)) {
    header("Location: manage_merit.php");
    exit;
} else {
    die("Error: " . $conn->error);
}
?>
