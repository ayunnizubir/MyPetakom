<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $sql = "UPDATE events SET status = '$status' WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("refresh:2;url=event_approval.php");
        echo "<h3>✅ Event status updated to '$status'! Redirecting...</h3>";
    } else {
        header("refresh:3;url=event_approval.php");
        echo "<h3>❌ Error updating status: " . $conn->error . "<br>Redirecting...</h3>";
    }

    $conn->close();
}
?>
