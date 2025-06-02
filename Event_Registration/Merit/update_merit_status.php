<?php
// Event_Registration/Merit/update_merit_status.php

// Only accept POST submissions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: merit_approval.php');
    exit;
}

$id     = isset($_POST['id'])     ? intval($_POST['id'])         : 0;
$status = isset($_POST['status']) ? $_POST['status']             : '';

if ($id <= 0 || !in_array($status, ['Approved','Rejected'])) {
    header('Location: merit_approval.php');
    exit;
}

// 1) Connect
$conn = new mysqli("localhost","root","","db_registration");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2) Update the correct table (`merit`)
$stmt = $conn->prepare("
    UPDATE merit
    SET status = ?
    WHERE id = ?
");
$stmt->bind_param("si", $status, $id);
$stmt->execute();

// 3) Feedback & redirect
if ($stmt->affected_rows >= 0) {
    header("refresh:2;url=merit_approval.php");
    echo "<h3>✅ Application marked “{$status}”. Redirecting back…</h3>";
} else {
    header("refresh:3;url=merit_approval.php");
    echo "<h3>❌ Failed to update: " . htmlspecialchars($stmt->error) . "<br>Redirecting…</h3>";
}

$stmt->close();
$conn->close();
exit;
?>
