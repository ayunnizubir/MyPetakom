<?php
// edit_event.php — display the edit form only

// 1) connect & fetch the existing event
$conn = new mysqli("localhost", "root", "", "db_registration");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Invalid Event ID.");
}

$res = $conn->query("SELECT * FROM events WHERE id = $id");
if (!$res || $res->num_rows === 0) {
    die("Event not found.");
}
$event = $res->fetch_assoc();
$conn->close();

// 2) parse existing time into hour/minute/AMPM
$hour   = '01';
$minute = '00';
$ampm   = 'AM';
if (!empty($event['event_time'])) {
    list($hm, $a) = explode(' ', $event['event_time']);
    list($h, $m)  = explode(':', $hm);
    $hour   = str_pad($h, 2, '0', STR_PAD_LEFT);
    $minute = str_pad($m, 2, '0', STR_PAD_LEFT);
    $ampm   = $a;
}

// 3) the only source of truth for locations
$locations = ['Astaka', 'DK1', 'DK2', 'Online'];
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
        <li><a href="dashboard_advisor.php">Dashboard Event Advisor</a></li>
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
            <input type="hidden" name="id" value="<?= htmlspecialchars($event['id']) ?>">

        <!-- Event Name -->
        <label for="event_name">Event Name</label>
        <input
            type="text"
            id="event_name"
            name="event_name"
            value="<?= htmlspecialchars($event['event_name']) ?>"
            required
            >

        <!-- Date -->
        <label for="event_date">Date</label>
        <input
            type="date"
            id="event_date"
            name="event_date"
            value="<?= htmlspecialchars($event['event_date']) ?>"
            required
            >

        <!-- Time -->
        <label>Time</label>
        <div class="row">
            <select name="hour" required>
                <option value="">Hour</option>
                <?php for ($h = 1; $h <= 12; $h++):
                $h2 = str_pad($h, 2, '0', STR_PAD_LEFT);
                ?>
                <option value="<?= $h2 ?>" <?= $hour === $h2 ? 'selected' : '' ?>>
                <?= $h2 ?>
                </option>
                <?php endfor; ?>
            </select>

            <select name="minute" required>
                <option value="">Min</option>
                <?php for ($m = 0; $m < 60; $m += 5):
                $m2 = str_pad($m, 2, '0', STR_PAD_LEFT);
                ?>
                <option value="<?= $m2 ?>" <?= $minute === $m2 ? 'selected' : '' ?>>
                <?= $m2 ?>
                </option>
                <?php endfor; ?>
            </select>

            <select name="ampm" required>
                <option value="AM" <?= $ampm === 'AM' ? 'selected' : '' ?>>AM</option>
                <option value="PM" <?= $ampm === 'PM' ? 'selected' : '' ?>>PM</option>
            </select>
            </div>

        <!-- Location -->
        <label for="location">Location</label>
        <select id="location" name="location" required>
            <option value="">— Select Location —</option>
            <?php foreach ($locations as $loc): ?>
                <option
                value="<?= htmlspecialchars($loc) ?>"
                <?= $event['location'] === $loc ? 'selected' : '' ?>
                >
                <?= htmlspecialchars($loc) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Description -->
        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            required
        ><?= htmlspecialchars($event['description']) ?></textarea>

        <input type="hidden" name="status" value="<?= htmlspecialchars($event['status']) ?>">

        <label>Current Approval Letter</label>
        <?php if (!empty($event['approval_letter'])): ?>
            <p>
                <a href="<?= htmlspecialchars($event['approval_letter']) ?>" target="_blank">
                View Current Letter
                </a>
            </p>
        <?php else: ?>
            <p><em>No file uploaded yet</em></p>
        <?php endif; ?>
        <input
            type="hidden"
            name="existing_letter"
            value="<?= htmlspecialchars($event['approval_letter']) ?>"
        >

        <label for="approval_letter">Upload New Approval Letter</label>
        <input type="file" id="approval_letter" name="approval_letter">

        <div class="btn-group">
            <button type="button" onclick="history.back()">Cancel</button>
            <button class="btn-submit" type="submit">Update</button>
        </div>
        </form>
    </div>
    </div>
</body>
</html>
