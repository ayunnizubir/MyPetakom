<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['approved_status'])) {
    $id = intval($_POST['id']);
    $approved_status = $_POST['approved_status'];

    // If approved, mark event as 'Upcoming', else leave as-is
    $status = ($approved_status === 'Approved') ? 'Upcoming' : 'Rejected';

    // Update the correct columns
    $stmt = $conn->prepare("UPDATE events SET approved_status = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssi", $approved_status, $status, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: event_approval.php");
    exit();
} else {
    echo "Invalid request.";
}
?>
