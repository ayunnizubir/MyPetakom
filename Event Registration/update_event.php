<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

$id = intval($_POST['id']);
$event_name = $_POST['event_name'];
$date = $_POST['date'];
$hour = $_POST['hour'];
$minute = $_POST['minute'];
$ampm = $_POST['ampm'];
$event_time = sprintf("%02d:%02d %s", $hour, $minute, $ampm);
$location = $_POST['location'];
$description = $_POST['description'];
$status = $_POST['status']; // Hidden input to preserve existing status

// File upload logic
$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$approval_letter_path = "";
if (isset($_FILES['approval_letter']) && $_FILES['approval_letter']['error'] === UPLOAD_ERR_OK) {
    $filename = basename($_FILES["approval_letter"]["name"]);
    $approval_letter_path = $upload_dir . $filename;
    move_uploaded_file($_FILES["approval_letter"]["tmp_name"], $approval_letter_path);
} else {
    // Keep old file if no new file uploaded
    $result = $conn->query("SELECT approval_letter FROM events WHERE id = $id");
    $row = $result->fetch_assoc();
    $approval_letter_path = $row['approval_letter'];
}

// Update query
$stmt = $conn->prepare("UPDATE events SET event_name=?, event_date=?, event_time=?, location=?, description=?, approval_letter=?, status=? WHERE id=?");
$stmt->bind_param("sssssssi", $event_name, $date, $event_time, $location, $description, $approval_letter_path, $status, $id);
$stmt->execute();
$stmt->close();

$conn->close();

header("Location: event_list.php");
exit();
?>
