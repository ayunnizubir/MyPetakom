<?php

if (!isset($_GET['id'])) {
    header('Location: event_list.php');
    exit;
}
$id = intval($_GET['id']);
if ($id <= 0) {
    header('Location: event_list.php');
    exit;
}


$conn = new mysqli("localhost","root","","db_registration");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$stmt = $conn->prepare("SELECT approval_letter FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($filePath);
if ($stmt->fetch() && $filePath && file_exists(__DIR__ . '/' . $filePath)) {
    @unlink(__DIR__ . '/' . $filePath);
}
$stmt->close();


$stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();


header('Location: event_list.php');
exit;
?>
