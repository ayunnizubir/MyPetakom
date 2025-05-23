<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = $_POST['location'];
    $description = $_POST['description'];

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

    $sql = "INSERT INTO events (event_name, event_date, event_time, location, description, approval_letter, status)
            VALUES ('$event_name', '$event_date', '$event_time', '$location', '$description', '$approval_letter_path', 'Upcoming')";

    if ($conn->query($sql) === TRUE) {
        header("refresh:2;url=create_event.html");
        echo "<h3>✅ New event created successfully! Redirecting...</h3>";
    } else {
        header("refresh:3;url=create_event.html");
        echo "<h3>❌ Error: " . $conn->error . "<br>Redirecting...</h3>";
    }

    $conn->close();
}
?>
