<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $event_name = $_POST['event_name'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $hour = $_POST['hour'] ?? '';
    $minute = $_POST['minute'] ?? '';
    $ampm = $_POST['ampm'] ?? '';
    $event_time = sprintf("%02d:%02d %s", $hour, $minute, $ampm);
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $approval_letter_path = $_POST['existing_letter'] ?? '';

    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (isset($_FILES['approval_letter']) && $_FILES['approval_letter']['error'] === UPLOAD_ERR_OK) {
        $filename = basename($_FILES["approval_letter"]["name"]);
        $approval_letter_path = $upload_dir . $filename;
        move_uploaded_file($_FILES["approval_letter"]["tmp_name"], $approval_letter_path);
    }

    $sql = "UPDATE events SET 
                event_name = '$event_name',
                event_date = '$event_date',
                event_time = '$event_time',
                location = '$location',
                description = '$description',
                approval_letter = '$approval_letter_path'
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("refresh:2;url=event_list.php");
        echo "<h3>✅ Event updated successfully! Redirecting to event list...</h3>";
    } else {
        header("refresh:3;url=event_list.php");
        echo "<h3>❌ Failed to update: " . $conn->error . "<br>Redirecting...</h3>";
    }

    $conn->close();
}
?>
