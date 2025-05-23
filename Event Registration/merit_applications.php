<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "db_registration";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle merit application submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event = $_POST['event'];
    $type = $_POST['type'];
    $status = "Pending";
    $date = date("Y-m-d");

    $stmt = $conn->prepare("INSERT INTO merit (event_name, type, status, applied_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $event, $type, $status, $date);
    $stmt->execute();
    $stmt->close();
}

// Fetch approved events
$eventList = $conn->query("SELECT event_name FROM events WHERE status = 'Approved'");
if (!$eventList) {
    die("Event query failed: " . $conn->error);
} elseif ($eventList->num_rows == 0) {
    die("No events found with status = 'Approved'");
}
$history = $conn->query("SELECT * FROM merit");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Merit Application</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="sidebar">
    <img src="../login/petakom_logo.png" alt="PETAKOM Logo">
    <h2>MyPetakom</h2>
    <ul>
        <li><a href="#">Dashboard Event Advisor</a></li>
        <li><a href="create_event.html">Create Event</a></li>
        <li><a href="event_list.php">Event List</a></li>
        <li><a href="#">Committee</a></li>
        <li><a href="#" style="font-weight: bold;">Merit Application</a></li>
        <li><a href="#">QR Codes</a></li>
    </ul>
</div>

<div class="main">
    <div class="header">
        <h1>Merit Application</h1>
        <div class="profile">
            <span>User's Name</span>
            <div class="profile-icon">👤</div>
            <button>Sign Out</button>
        </div>
    </div>

    <div class="container">
        <h2>Apply for Merit</h2>

        <form method="POST">
            <label for="event">Event Name</label>
            <select id="event" name="event" required>
                <option value="">-- Select Event --</option>
                <?php
                if ($eventList && $eventList->num_rows > 0) {
                    while ($row = $eventList->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['event_name']) . "'>" . htmlspecialchars($row['event_name']) . "</option>";
                    }
                } else {
                    echo "<option disabled>No approved events found</option>";
                }
                ?>
            </select>

            <label>Application Type</label><br>
            <input type="radio" id="committee" name="type" value="Committee" checked>
            <label for="committee">Committee</label>
            <input type="radio" id="participant" name="type" value="Participant">
            <label for="participant">Participant</label>

            <div class="btn-group">
                <button type="reset">Cancel</button>
                <button type="submit" style="background-color: #007bff; color: white;">Submit</button>
            </div>
        </form>

        <h3 style="margin-top: 30px;">Application History</h3>
        <table>
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Applied Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($history && $history->num_rows > 0): ?>
                    <?php while ($app = $history->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($app['event_name']) ?></td>
                            <td><?= htmlspecialchars($app['type']) ?></td>
                            <td><?= htmlspecialchars($app['status']) ?></td>
                            <td><?= htmlspecialchars($app['applied_date']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4">No merit applications found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
