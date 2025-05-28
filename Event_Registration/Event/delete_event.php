<?php
<<<<<<< HEAD
// delete_event.php — safely delete an event then redirect

// 1) Only accept an “id” parameter
=======

>>>>>>> 6c3d878ae734b94f067428de2fbff590fde75955
if (!isset($_GET['id'])) {
    header('Location: event_list.php');
    exit;
}
$id = intval($_GET['id']);
if ($id <= 0) {
    header('Location: event_list.php');
    exit;
}

<<<<<<< HEAD
// 2) Connect
=======

>>>>>>> 6c3d878ae734b94f067428de2fbff590fde75955
$conn = new mysqli("localhost","root","","db_registration");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

<<<<<<< HEAD
// 3) Optionally remove the approval_letter file
=======

>>>>>>> 6c3d878ae734b94f067428de2fbff590fde75955
$stmt = $conn->prepare("SELECT approval_letter FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($filePath);
if ($stmt->fetch() && $filePath && file_exists(__DIR__ . '/' . $filePath)) {
    @unlink(__DIR__ . '/' . $filePath);
}
$stmt->close();

<<<<<<< HEAD
// 4) Delete the record
=======

>>>>>>> 6c3d878ae734b94f067428de2fbff590fde75955
$stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();

<<<<<<< HEAD
// 5) Redirect back to the list
=======

>>>>>>> 6c3d878ae734b94f067428de2fbff590fde75955
header('Location: event_list.php');
exit;
?>
