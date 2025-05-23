<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['approved_status'])) {
    $id = intval($_POST['id']);
    $approved_status = $_POST['approved_status']; // This comes from your dropdown

    // Use the approved_status value to update the 'status' field only
    if ($approved_status === 'Approved' || $approved_status === 'Rejected') {
        $stmt = $conn->prepare("UPDATE events SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $approved_status, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: event_approval.php");
        exit();
    } else {
        echo "Invalid approval status.";
    }
} else {
    echo "Invalid request.";
}
?>
