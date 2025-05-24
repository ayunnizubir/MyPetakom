<?php
$conn = new mysqli("localhost", "root", "", "db_registration");
$result = $conn->query("SELECT * FROM events WHERE status = 'Pending'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Events</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Coordinator Sidebar -->
    <div class="sidebar">
        <img src="../petakom_logo.png" alt="PETAKOM Logo">
        <h2>Coordinator Panel</h2>
        <ul>
            <li><a href="event_approval.php">Approve Events</a></li>
            <li><a href="#">Merit Records</a></li>
            <li><a href="#">Reports</a></li>
            <li><a href="#">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main">
        <div class="header">
            <h1>Approve Events</h1>
            <div class="profile">
                <div class="profile-icon">👤</div>
                <span>Coordinator</span>
                <button>Sign Out</button>
            </div>
        </div>

        <div class="container">
            <h2>Pending Approvals</h2>
            <table>
                <tr>
                    <th>No</th>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Approval Letter</th>
                    <th>Action</th>
                </tr>
                <?php
                $i = 1;
                while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= $row['event_name'] ?></td>
                    <td><?= $row['event_date'] ?></td>
                    <td><?= $row['event_time'] ?></td>
                    <td><?= $row['location'] ?></td>
                    <td><?= $row['description'] ?></td>
                    <td>
                        <?php if (!empty($row['approval_letter'])): ?>
                            <a href="../<?= $row['approval_letter'] ?>" target="_blank">View Letter</a>
                        <?php else: ?>
                            No Letter
                        <?php endif; ?>
                    </td>
                    <td>
                        <form action="update_status.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="status" value="Approved">
                            <button type="submit" class="qr">Approve</button>
                        </form>
                        <form action="update_status.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="status" value="Rejected">
                            <button type="submit" class="delete">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
