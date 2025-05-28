<?php
<<<<<<< HEAD
$conn = new mysqli("localhost", "root", "", "db_registration");

$result = $conn->query("
    SELECT merit.*, events.event_name
    FROM merit
    JOIN events ON merit.event_id = events.id
    WHERE merit.status = 'Pending'
") or die("SQL Error: " . $conn->error);
?>

=======

$conn = new mysqli("localhost", "root", "", "db_registration");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
    SELECT
        m.id,
        e.event_name,
        m.type,
        m.applied_date
    FROM merit AS m
    JOIN events AS e ON m.event_id = e.id
    WHERE m.status = 'Pending'
    ORDER BY e.event_date, e.event_time
";
$result = $conn->query($sql) or die("SQL Error: " . $conn->error);
$conn->close();
?>
>>>>>>> 6c3d878ae734b94f067428de2fbff590fde75955
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
<<<<<<< HEAD
    <title>Approve Merit Applications</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
=======
    <title>Merit Approval</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Coordinator Sidebar -->
>>>>>>> 6c3d878ae734b94f067428de2fbff590fde75955
    <div class="sidebar">
        <img src="../css/petakom_logo.png" alt="PETAKOM Logo">
        <h2>Coordinator Panel</h2>
        <ul>
<<<<<<< HEAD
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
=======
        <li><a href="../Coordinator/event_approval.php">Approve Events</a></li>
        <li><a href="merit_approval.php" class="active">Merit Approval</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main">
        <div class="header">
        <h1>Merit Approval</h1>
        <div class="profile">
            <div class="profile-icon">👤</div>
            <span>User's Name</span>
            <button>Sign Out</button>
        </div>
        </div>

        <div class="container">
        <h2>Pending Merit Applications</h2>

        <?php if (!$result || $result->num_rows === 0): ?>
            <p><em>No pending applications.</em></p>
        <?php else: ?>
            <table>
            <thead>
                <tr>
                <th>Event</th>
                <th>Type</th>
                <th>Applied Date</th>
                <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                <tr>
                <td><?= htmlspecialchars($row['event_name']) ?></td>
                <td><?= htmlspecialchars($row['type']) ?></td>
                <td><?= htmlspecialchars($row['applied_date']) ?></td>
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
            </tbody>
            </table>
        <?php endif; ?>
>>>>>>> 6c3d878ae734b94f067428de2fbff590fde75955
        </div>
    </div>
</body>
</html>
