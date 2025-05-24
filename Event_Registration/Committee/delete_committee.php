<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM committee WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: committee.php");
        exit();
    } else {
        echo "Error deleting committee member: " . $conn->error;
    }
}
$conn->close();
?>
