<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = intval($_POST['id']);
$event_name = $_POST['event_name'];
$date = $_POST['date'];
$hour = intval($_POST['hour']);
$minute = intval($_POST['minute']);
$ampm = $_POST['ampm'];

// Convert to 24-hour format for better DB consistency (optional but recommended)
if ($ampm === 'PM' && $hour != 12) {
    $hour += 12;
} elseif ($ampm === 'AM' && $hour == 12) {
    $hour = 0;
}
$event_time = sprintf("%02d:%02d", $hour, $minute);

$location = $_POST['location'];
$description = $_POST['description'];

// ✅ Preserve original status from database to prevent tampering
$result = $conn->query("SELECT status, approval_letter FROM events WHERE id = $id");
$row = $result->fetch_assoc();
$status = $row['status'];

// File upload logic
$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$approval_letter_path = $row['approval_letter']; // default to old path
if (isset($_FILES['approval_letter']) && $_FILES['approval_letter']['error'] === UPLOAD_ERR_OK) {
    $filename = basename($_FILES["approval_letter"]["name"]);
    $target_file = $upload_dir . $filename;

    // Optional: Validate file type
    $allowed_types = ['pdf', 'docx', 'doc'];
    $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (in_array($file_ext, $allowed_types)) {
        move_uploaded_file($_FILES["approval_letter"]["tmp_name"], $target_file);
        $approval_letter_path = $target_file;
    }
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
