<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $status = $_POST['status'];

    $sql = "UPDATE merit SET status = '$status' WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: merit_approval.php");
        exit();
    } else {
        echo "Error updating merit status: " . $conn->error;
    }
}
$conn->close();
?>
