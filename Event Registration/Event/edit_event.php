<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

$id = $_GET['id'] ?? 0;
$id = intval($id);

$result = $conn->query("SELECT * FROM events WHERE id = $id");
$event = $result->fetch_assoc();
$conn->close();

// Extract time
$time_parts = explode(':', $event['event_time']);
$hour = $time_parts[0];
$minute_ampm = isset($time_parts[1]) ? explode(' ', $time_parts[1]) : ['00', 'AM'];
$minute = $minute_ampm[0];
$ampm = $minute_ampm[1] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Event</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="sidebar">
        <img src="../css/petakom_logo.png" alt="PETAKOM Logo">
        <h2>MyPetakom</h2>
        <ul>
            <li><a href="#">Dashboard Event Advisor</a></li>
            <li><a href="create_event.html">Create Event</a></li>
            <li><a href="event_list.php">Event List</a></li>
            <li><a href="#">Committee</a></li>
            <li><a href="#">Merit Application</a></li>
            <li><a href="#">QR Codes</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="header">
            <h1>Edit Event</h1>
            <div class="profile">
                <div class="profile-icon">👤</div>
                <span>User's Name</span>
                <button>Sign Out</button>
            </div>
        </div>

        <div class="container">
            <h2>Event Details</h2>
            <form action="update_event.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $event['id'] ?>">

                <label>Event Name</label>
                <input type="text" name="event_name" value="<?= $event['event_name'] ?>" required>

                <label>Date</label>
                <input type="date" name="event_date" value="<?= $event['event_date'] ?>" required>

                <label>Time</label>
                <div class="row">
                    <select name="hour" required>
                        <option value="">Hour</option>
                        <?php foreach (range(1, 12) as $h): $h = str_pad($h, 2, '0', STR_PAD_LEFT); ?>
                            <option value="<?= $h ?>" <?= $hour == $h ? 'selected' : '' ?>><?= $h ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="minute" required>
                        <option value="">Min</option>
                        <?php foreach (range(0, 55, 5) as $m): $m = str_pad($m, 2, '0', STR_PAD_LEFT); ?>
                            <option value="<?= $m ?>" <?= $minute == $m ? 'selected' : '' ?>><?= $m ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="ampm" required>
                        <option value="AM" <?= $ampm == 'AM' ? 'selected' : '' ?>>AM</option>
                        <option value="PM" <?= $ampm == 'PM' ? 'selected' : '' ?>>PM</option>
                    </select>
                </div>

                <label>Location</label>
                <select name="location" required>
                    <option value="Main Hall" <?= $event['location'] == 'Main Hall' ? 'selected' : '' ?>>Main Hall</option>
                    <option value="Lecture Hall A" <?= $event['location'] == 'Lecture Hall A' ? 'selected' : '' ?>>Lecture Hall A</option>
                    <option value="Online" <?= $event['location'] == 'Online' ? 'selected' : '' ?>>Online</option>
                </select>

                <label>Description</label>
                <textarea name="description" required><?= $event['description'] ?></textarea>

                <label>Current Approval Letter</label>
                <?php if (!empty($event['approval_letter'])): ?>
                    <p><a href="<?= $event['approval_letter'] ?>" target="_blank">View Current Letter</a></p>
                <?php else: ?>
                    <p><em>No file uploaded yet</em></p>
                <?php endif; ?>
                <input type="hidden" name="existing_letter" value="<?= $event['approval_letter'] ?>">
                <input type="file" name="approval_letter">

                <input type="hidden" name="status" value="<?= $event['status'] ?>">

                <div class="btn-group">
                    <button type="button" onclick="history.back()">Cancel</button>
                    <button class="btn-submit" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
