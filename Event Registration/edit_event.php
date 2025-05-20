<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

if (!isset($_GET['id'])) {
    die("Event ID not provided.");
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM events WHERE id = $id");
$event = $result->fetch_assoc();
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
    <img src="petakom_logo.png" alt="PETAKOM Logo"><br><br>
    <h2>MyPetakom</h2>
    <ul>
        <li><strong>Event Advisor</strong></li>
        <li><a href="#">Dashboard Event Advisor</a></li>
        <li><a href="create_event.html">Create Event</a></li>
        <li><a href="event_list.php" class="active">Event List</a></li>
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
            <button onclick="alert('Signed out')">Sign Out</button>
        </div>
    </div>

    <div class="container">
        <h2>Update Event Details</h2>
        <form action="update_event.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $event['id'] ?>">
            <input type="hidden" name="status" value="<?= htmlspecialchars($event['status']) ?>">

            <label>Event Name</label>
            <input type="text" name="event_name" value="<?= htmlspecialchars($event['event_name']) ?>" required>

            <label>Date</label>
            <input type="date" name="date" value="<?= $event['event_date'] ?>" required>

            <label>Time</label>
            <div class="row">
                <?php
                list($timePart, $ampm) = explode(' ', $event['event_time']);
                list($hour, $minute) = explode(':', $timePart);
                ?>
                <input type="number" name="hour" min="1" max="12" placeholder="Hour" value="<?= intval($hour) ?>" required>
                <input type="number" name="minute" min="0" max="59" placeholder="Minute" value="<?= intval($minute) ?>" required>
                <select name="ampm" required>
                    <option value="AM" <?= $ampm == "AM" ? "selected" : "" ?>>AM</option>
                    <option value="PM" <?= $ampm == "PM" ? "selected" : "" ?>>PM</option>
                </select>
            </div>
            <input type="hidden" name="formatted_time" id="formatted_time">
            <p id="time_preview" style="margin-top:10px; color: green;"></p>

            <label>Location</label>
            <select name="location" required>
                <option value="">Select Location</option>
                <?php
                $locations = ['Astaka', 'DK1', 'DK2', 'Online'];
                foreach ($locations as $loc) {
                    $selected = $event['location'] == $loc ? "selected" : "";
                    echo "<option value='$loc' $selected>$loc</option>";
                }
                ?>
            </select>

            <label>Description</label>
            <textarea name="description" rows="4"><?= htmlspecialchars($event['description']) ?></textarea>

            <label>Current Approval Letter:</label>
            <p><?= basename($event['approval_letter']) ?></p>

            <label>Upload New Approval Letter (optional)</label>
            <input type="file" name="approval_letter" accept=".pdf,.doc,.docx">

            <div class="btn-group">
                <a href="event_list.php"><button type="button">Cancel</button></a>
                <button type="submit">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    const hourInput = document.querySelector('input[name="hour"]');
    const minuteInput = document.querySelector('input[name="minute"]');
    const ampmSelect = document.querySelector('select[name="ampm"]');
    const formattedTimeInput = document.getElementById('formatted_time');
    const timePreview = document.getElementById('time_preview');
    const form = document.querySelector('form');

    function formatTime() {
        let hour = parseInt(hourInput.value, 10);
        let minute = parseInt(minuteInput.value, 10);
        let ampm = ampmSelect.value;

        if (isNaN(hour) || isNaN(minute)) {
            timePreview.textContent = '';
            return;
        }

        if (hour < 1 || hour > 12 || minute < 0 || minute > 59) {
            timePreview.textContent = 'Invalid time';
            return;
        }

        const formatted = `${hour}:${minute.toString().padStart(2, '0')} ${ampm}`;
        formattedTimeInput.value = formatted;
        timePreview.textContent = `Selected Time: ${formatted}`;
    }

    hourInput.addEventListener('input', formatTime);
    minuteInput.addEventListener('input', formatTime);
    ampmSelect.addEventListener('change', formatTime);
    form.addEventListener('submit', formatTime);
</script>
</body>
</html>
