<?php
// delete_event.php — safely delete an event then redirect

// 1) Only accept an “id” parameter
if (!isset($_GET['id'])) {
    header('Location: event_list.php');
    exit;
}
$id = intval($_GET['id']);
if ($id <= 0) {
    header('Location: event_list.php');
    exit;
}

// 2) Connect
$conn = new mysqli("localhost","root","","db_registration");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 3) Optionally remove the approval_letter file
$stmt = $conn->prepare("SELECT approval_letter FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($filePath);
if ($stmt->fetch() && $filePath && file_exists(__DIR__ . '/' . $filePath)) {
    @unlink(__DIR__ . '/' . $filePath);
}
$stmt->close();

// 4) Delete the record
$stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();

// 5) Redirect back to the list
header('Location: event_list.php');
exit;
?>
