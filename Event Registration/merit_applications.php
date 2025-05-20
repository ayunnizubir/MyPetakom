<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "db_registration"; // Updated to match your DB

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

    // Ensure applications table exists
    $sql = "INSERT INTO merit (event_name, type, status, applied_date)
            VALUES ('$event', '$type', '$status', '$date')";
    $conn->query($sql);
}

// Fetch only upcoming events
$eventList = $conn->query("SELECT event_name FROM events WHERE status = 'Upcoming' AND approved_status = 'Approved'");


// Fetch previous merit applications
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
        <h2>Merit Application</h2>
        <p>Apply for merit award for events</p>

        <form method="POST">
            <label for="event">Event Name</label>
            <select id="event" name="event" required>
                <option value="">Select Event</option>
                <?php while ($row = $eventList->fetch_assoc()): ?>
                    <option value="<?= $row['event_name'] ?>"><?= $row['event_name'] ?></option>
                <?php endwhile; ?>
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
                <?php while ($app = $history->fetch_assoc()): ?>
                    <tr>
                        <td><?= $app['event_name'] ?></td>
                        <td><?= $app['type'] ?></td>
                        <td><?= $app['status'] ?></td>
                        <td><?= $app['applied_date'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
