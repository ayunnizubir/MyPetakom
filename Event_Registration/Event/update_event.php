<?php
// Advisor's Update Event page — handle POST then display form
$conn = new mysqli("localhost", "root", "", "db_registration");

// Determine ID (from POST on submission or GET otherwise)
$id = isset($_POST['id']) ? intval($_POST['id']) : intval($_GET['id'] ?? 0);

// POST: update record
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = $conn->real_escape_string($_POST['event_name']);
    $event_date = $_POST['event_date'];
    $hour = $_POST['hour'];
    $minute = $_POST['minute'];
    $ampm = $_POST['ampm'];
    $event_time = sprintf("%02d:%02d %s", $hour, $minute, $ampm);
    $location = $conn->real_escape_string($_POST['location']);
    $description = $conn->real_escape_string($_POST['description']);
    $status = $_POST['status'];

    // Preserve or update approval letter
    $approval_letter_path = $_POST['existing_letter'] ?? '';
    if (isset($_FILES['approval_letter']) && $_FILES['approval_letter']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . "/uploads/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = basename($_FILES['approval_letter']['name']);
        $approval_letter_path = "uploads/" . $filename;
        move_uploaded_file($_FILES['approval_letter']['tmp_name'], $approval_letter_path);
    }

    $sql = "UPDATE events SET
                event_name='$event_name',
                event_date='$event_date',
                event_time='$event_time',
                location='$location',
                description='$description',
                status='$status',
                approval_letter='$approval_letter_path'
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header('Location: event_list.php');
        exit;
    } else {
        echo "Error updating event: " . $conn->error;
    }
}

// GET or failed POST: fetch event details
$result = $conn->query("SELECT * FROM events WHERE id = $id");
$event = $result->fetch_assoc();

// Parse existing time into parts
$hour = '00';
$minute = '00';
$ampm = 'AM';
if (!empty($event['event_time'])) {
    $parts = explode(' ', $event['event_time']);
    if (count($parts) === 2) {
        list($time_part, $ampm) = $parts;
        $time_sub = explode(':', $time_part);
        if (count($time_sub) === 2) {
            list($hour, $minute) = $time_sub;
        }
    }
}
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
            <li><a href="../Committee/committee.php">Committee</a></li>
            <li><a href="../Merit/merit_applications.php">Merit Application</a></li>
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
                <input type="text" name="event_name" value="<?= htmlspecialchars($event['event_name']) ?>" required>

                <label>Date</label>
                <input type="date" name="event_date" value="<?= $event['event_date'] ?>" required>

                <label>Time</label>
                <div class="row">
                    <select name="hour" required>
                        <option value="">Hour</option>
                        <?php foreach (range(1, 12) as $hVal): ?>
                            <?php $hOpt = sprintf('%02d', $hVal); ?>
                            <option value="<?= $hOpt ?>" <?= ($hour === $hOpt) ? 'selected' : '' ?>><?= $hOpt ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="minute" required>
                        <option value="">Min</option>
                        <?php foreach (range(0, 55, 5) as $mVal): ?>
                            <?php $mOpt = sprintf('%02d', $mVal); ?>
                            <option value="<?= $mOpt ?>" <?= ($minute === $mOpt) ? 'selected' : '' ?>><?= $mOpt ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="ampm" required>
                        <option value="AM" <?= ($ampm === 'AM') ? 'selected' : '' ?>>AM</option>
                        <option value="PM" <?= ($ampm === 'PM') ? 'selected' : '' ?>>PM</option>
                    </select>
                </div>

                <label>Location</label>
                <select name="location" required>
                    <?php foreach (['Main Hall', 'Lecture Hall A', 'Online'] as $loc): ?>
                        <option value="<?= $loc ?>" <?= ($event['location'] === $loc) ? 'selected' : '' ?>><?= $loc ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Description</label>
                <textarea name="description" required><?= htmlspecialchars($event['description']) ?></textarea>

                <label>Status</label>
                <select name="status" required>
                    <?php foreach (['Pending', 'Postponed', 'Cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($event['status'] === $s) ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Current Approval Letter</label>
                <?php if (!empty($event['approval_letter'])): ?>
                    <p><a href="<?= $event['approval_letter'] ?>" target="_blank">View Current Letter</a></p>
                <?php else: ?>
                    <p><em>No file uploaded yet</em></p>
                <?php endif; ?>
                <input type="hidden" name="existing_letter" value="<?= $event['approval_letter'] ?>">

                <label>Upload New Approval Letter</label>
                <input type="file" name="approval_letter">

                <div class="btn-group">
                    <button type="button" onclick="history.back()">Cancel</button>
                    <button class="btn-submit" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
