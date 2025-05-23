<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

$selected_event_id = $_GET['event_id'] ?? '';
$default_type = $_GET['type'] ?? '';

$event_result = $conn->query("SELECT id, event_name FROM events WHERE status = 'Approved'");
$history_result = $conn->query("SELECT merit.*, events.event_name FROM merit JOIN events ON merit.event_id = events.id ORDER BY applied_date DESC");
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
        <img src="../css/petakom_logo.png" alt="PETAKOM Logo">
        <h2>MyPetakom</h2>
        <ul>
            <li><a href="#">Dashboard Event Advisor</a></li>
            <li><a href="create_event.html">Create Event</a></li>
            <li><a href="event_list.php">Event List</a></li>
            <li><a href="committee.php">Committee</a></li>
            <li><a href="merit_applications.php">Merit Application</a></li>
            <li><a href="#">QR Codes</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="header">
            <h1>Merit Application</h1>
            <div class="profile">
                <div class="profile-icon">👤</div>
                <span>User's Name</span>
                <button>Sign Out</button>
            </div>
        </div>

        <div class="container">
            <h2>Apply for Merit</h2>

            <form method="POST" action="submit_merit.php">
                <label for="event_id">Select Approved Event:</label>
                <select name="event_id" required>
                    <?php
                    if ($event_result && $event_result->num_rows > 0) {
                        while ($row = $event_result->fetch_assoc()) {
                            $selected = ($row['id'] == $selected_event_id) ? 'selected' : '';
                            echo "<option value='{$row['id']}' $selected>{$row['event_name']}</option>";
                        }
                    } else {
                        echo '<option disabled>No approved events available</option>';
                    }
                    ?>
                </select>

                <label>Application Type</label>
                <table class="radio-table-layout">
                    <tr>
                        <td>Committee</td>
                        <td><input type="radio" name="type" value="Committee" <?= $default_type === 'Committee' ? 'checked' : '' ?> required></td>
                    </tr>
                    <tr>
                        <td>Participant</td>
                        <td><input type="radio" name="type" value="Participant" <?= $default_type === 'Participant' ? 'checked' : '' ?> required></td>
                    </tr>
                </table>

                <div class="btn-group">
                    <button type="reset">Cancel</button>
                    <button class="btn-submit" type="submit">Submit</button>
                </div>
            </form>

            <h2 style="margin-top: 40px;">Application History</h2>
            <table>
                <tr>
                    <th>No</th>
                    <th>Event</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Applied Date</th>
                </tr>
                <?php
                $i = 1;
                while ($row = $history_result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= $row['event_name'] ?></td>
                    <td><?= $row['type'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><?= $row['applied_date'] ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
