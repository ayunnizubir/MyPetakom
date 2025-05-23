<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate each field
    $event_name = $_POST['event_name'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $hour = $_POST['hour'] ?? '';
    $minute = $_POST['minute'] ?? '';
    $ampm = $_POST['ampm'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';

    // Combine time safely
    if ($hour !== '' && $minute !== '' && $ampm !== '') {
        $event_time = sprintf("%02d:%02d %s", $hour, $minute, $ampm);
    } else {
        $event_time = '';
    }

    // File upload
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

    // Insert if all values are okay
    if ($event_name && $event_date && $event_time && $location && $description && $approval_letter_path) {
        $sql = "INSERT INTO events (event_name, event_date, event_time, location, description, approval_letter, status)
                VALUES ('$event_name', '$event_date', '$event_time', '$location', '$description', '$approval_letter_path', 'Upcoming')";

        if ($conn->query($sql) === TRUE) {
            header("refresh:2;url=create_event.html");
            echo "<h3>✅ Event successfully created! Redirecting...</h3>";
        } else {
            header("refresh:3;url=create_event.html");
            echo "<h3>❌ Database error: " . $conn->error . "<br>Redirecting...</h3>";
        }
    } else {
        header("refresh:3;url=create_event.html");
        echo "<h3>⚠️ Please complete all required fields.<br>Redirecting...</h3>";
    }

    $conn->close();
}
?>
