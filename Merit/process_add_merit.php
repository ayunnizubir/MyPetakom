<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matric_id = $_POST['matric_id'];
    $event_name = $_POST['event_name'];
    $date = $_POST['date'];
    $organizer = $_POST['organizer'];
    $position = $_POST['position'];
    $level = $_POST['level'];
    $marks = $_POST['marks'];

    $stmt = $conn->prepare("INSERT INTO merit (matric_id, event_name, date, organizer, position, level, marks) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $matric_id, $event_name, $date, $organizer, $position, $level, $marks);

    if ($stmt->execute()) {
        header("Location: add-merit.php?success=1");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
