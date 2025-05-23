<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

$student_id = $_GET['student_id'] ?? '';

$result = $conn->query("
    SELECT c.*, e.event_name, e.event_date 
    FROM committee c
    JOIN events e ON c.event_id = e.id
    WHERE c.student_id = '$student_id'
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Committee Roles</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="main">
        <div class="header">
            <h1>My Event Roles</h1>
        </div>

        <div class="container">
            <?php if ($student_id): ?>
                <h2>Roles for Student ID: <?= htmlspecialchars($student_id) ?></h2>
                <table>
                    <tr>
                        <th>Event</th>
                        <th>Date</th>
                        <th>Role</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['event_name'] ?></td>
                        <td><?= $row['event_date'] ?></td>
                        <td><?= $row['role'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>Please provide a student ID in the URL. Example: <code>?student_id=CB23012</code></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
