<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Optional: Delete approval letter file
    $fileQuery = $conn->query("SELECT approval_letter FROM events WHERE id = $id");
    if ($fileRow = $fileQuery->fetch_assoc()) {
        $filePath = $fileRow['approval_letter'];
        if (!empty($filePath) && file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $sql = "DELETE FROM events WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: event_list.php");
    } else {
        echo "Error deleting event: " . $conn->error;
    }
}
$conn->close();
?>
