<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "db_registration";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$event_name = $_POST['event_name'];
$date = $_POST['date'];
$hour = $_POST['hour'];
$minute = $_POST['minute'];
$ampm = $_POST['ampm'];
$event_time = sprintf("%02d:%02d %s", $hour, $minute, $ampm);
$location = $_POST['location'];
$description = $_POST['description'];

// Handle file upload
$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
$file_path = $upload_dir . basename($_FILES["approval_letter"]["name"]);
move_uploaded_file($_FILES["approval_letter"]["tmp_name"], $file_path);

// Insert event
$status = 'Pending'; // ✅ force status to Pending

$sql = "INSERT INTO events (event_name, date, time, location, description, approval_letter, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

// then bind and execute with $status included
$stmt->bind_param("sssssss", $event_name, $date, $time, $location, $description, $file_path, $status);


if ($conn->query($sql) === TRUE) {
    header("Location: event_list.php");
    exit();
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
