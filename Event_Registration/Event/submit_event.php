<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

$event_name = $_POST['event_name'];
$event_date = $_POST['event_date'];
$hour = $_POST['hour'];
$minute = $_POST['minute'];
$ampm = $_POST['ampm'];
$event_time = sprintf("%02d:%02d %s", $hour, $minute, $ampm);
$location = $_POST['location'];
$description = $_POST['description'];
$status = $_POST['status']; // This will be "Pending"

// Handle approval letter upload
$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$approval_letter_path = "";
if (isset($_FILES['approval_letter']) && $_FILES['approval_letter']['error'] === UPLOAD_ERR_OK) {
    $filename = basename($_FILES["approval_letter"]["name"]);
    $approval_letter_path = $upload_dir . $filename;
    move_uploaded_file($_FILES["approval_letter"]["tmp_name"], $approval_letter_path);
}

// Insert event
$sql = "INSERT INTO events (event_name, event_date, event_time, location, description, approval_letter, status)
        VALUES ('$event_name', '$event_date', '$event_time', '$location', '$description', '$approval_letter_path', '$status')";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Event created successfully!'); window.location.href='event_list.php';</script>";
} else {
    echo "Error: " . $conn->error;
}
?>
