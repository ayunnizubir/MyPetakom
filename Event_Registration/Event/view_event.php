<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

if (isset($_GET['event_id'])) {
    $event_id = intval($_GET['event_id']);
    $result = $conn->query("SELECT * FROM events WHERE id = $event_id");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Event</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Optional if you want consistent design -->
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h1 {
            margin-bottom: 20px;
        }
        p {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($result) && $result && $row = $result->fetch_assoc()): ?>
            <h1>Event Details</h1>
            <p><strong>Name:</strong> <?= $row['event_name'] ?></p>
            <p><strong>Date:</strong> <?= $row['event_date'] ?></p>
            <p><strong>Time:</strong> <?= $row['event_time'] ?></p>
            <p><strong>Location:</strong> <?= $row['location'] ?></p>
            <p><strong>Description:</strong><br><?= nl2br($row['description']) ?></p>
        <?php else: ?>
            <p style="color: red;"><strong>❌ Event not found or no event ID provided.</strong></p>
        <?php endif; ?>
    </div>
</body>
</html>
