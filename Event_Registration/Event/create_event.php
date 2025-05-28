<?php
<<<<<<< HEAD
// create_event.php — handle the Create Event form submission

// 1) connect
=======

>>>>>>> 6c3d878ae734b94f067428de2fbff590fde75955
$conn = new mysqli("localhost","root","","db_registration");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

<<<<<<< HEAD
// 2) only accept POST
=======

>>>>>>> 6c3d878ae734b94f067428de2fbff590fde75955
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: create_event.html');
    exit;
}

<<<<<<< HEAD
// 3) grab & sanitize inputs
=======

>>>>>>> 6c3d878ae734b94f067428de2fbff590fde75955
$event_name = $conn->real_escape_string($_POST['event_name']);
$event_date = $_POST['event_date'];
$hour       = $_POST['hour'];
$minute     = $_POST['minute'];
$ampm       = $_POST['ampm'];
$event_time = sprintf("%02d:%02d %s", $hour, $minute, $ampm);
$location   = $conn->real_escape_string($_POST['location']);
$description= $conn->real_escape_string($_POST['description']);
$status     = $conn->real_escape_string($_POST['status']);

// 4) handle file upload if present
$approval_letter_path = '';
if (isset($_FILES['approval_letter']) && $_FILES['approval_letter']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    $filename = basename($_FILES['approval_letter']['name']);
    $target   = $upload_dir . $filename;
    if (move_uploaded_file($_FILES['approval_letter']['tmp_name'], $target)) {
        // store web-relative path
        $approval_letter_path = 'uploads/' . $filename;
    }
}

// 5) insert into database
$stmt = $conn->prepare("
    INSERT INTO events
        (event_name, event_date, event_time, location, description, status, approval_letter)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "sssssss",
    $event_name,
    $event_date,
    $event_time,
    $location,
    $description,
    $status,
    $approval_letter_path
);

if ($stmt->execute()) {
    // success → back to list
    header('Location: event_list.php');
    exit;
} else {
    echo "Error creating event: " . htmlspecialchars($stmt->error);
}
?>
