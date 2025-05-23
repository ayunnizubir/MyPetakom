<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_id = $_POST['event_id'] ?? '';
    $type = $_POST['type'] ?? '';
    $status = "Pending";
    $applied_date = date("Y-m-d");

    if ($event_id && $type) {
        $sql = "INSERT INTO merit (event_id, type, status, applied_date)
                VALUES ('$event_id', '$type', '$status', '$applied_date')";

        if ($conn->query($sql) === TRUE) {
            header("refresh:2;url=merit_applications.php");
            echo "<h3>✅ Merit application submitted! Redirecting...</h3>";
        } else {
            header("refresh:3;url=merit_applications.php");
            echo "<h3>❌ Error: " . $conn->error . "<br>Redirecting...</h3>";
        }
    } else {
        header("refresh:3;url=merit_applications.php");
        echo "<h3>⚠️ Please complete the form. Redirecting...</h3>";
    }

    $conn->close();
}
?>
