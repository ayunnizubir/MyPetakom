<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

$result = $conn->query("
    SELECT merit.*, events.event_name
    FROM merit
    JOIN events ON merit.event_id = events.id
    WHERE merit.status = 'Pending'
") or die("SQL Error: " . $conn->error);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Merit Applications</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="sidebar">
        <img src="../css/petakom_logo.png" alt="PETAKOM Logo">
        <h2>Coordinator Panel</h2>
        <ul>
            <li><a href="#">Approve Events</a></li>
            <li><a href="merit_approval.php">Merit Approval</a></li>
            <li><a href="#">Reports</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="header">
            <h1>Approve Merit Applications</h1>
        </div>

        <div class="container">
            <h2>Pending Applications</h2>
            <table>
                <tr>
                    <th>No</th>
                    <th>Event</th>
                    <th>Type</th>
                    <th>Applied Date</th>
                    <th>Action</th>
                </tr>
                <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= $row['event_name'] ?></td>
                    <td><?= $row['type'] ?></td>
                    <td><?= $row['applied_date'] ?></td>
                    <td>
                        <form method="POST" action="update_merit_status.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="status" value="Approved">
                            <button class="qr" type="submit">Approve</button>
                        </form>
                        <form method="POST" action="update_merit_status.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="status" value="Rejected">
                            <button class="delete" type="submit">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
