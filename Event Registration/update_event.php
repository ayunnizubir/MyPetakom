<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $status = $_POST['status']; // Approved or Rejected

    $sql = "UPDATE events SET status = '$status' WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo "Event status updated successfully";
    } else {
        echo "Error updating status: " . $conn->error;
    }
    $conn->close();
}
?>