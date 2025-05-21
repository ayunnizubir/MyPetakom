<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM events");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Approval - PETAKOM Coordinator</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>

<div class="sidebar">
    <img src="../../login/petakom_logo.png" alt="PETAKOM Logo">
    <h2>MyPetakom</h2>
    <ul>
        <li><strong>PETAKOM Coordinator</strong></li>
        <li><a href="event_approval.php" class="active">Approve Events</a></li>
    </ul>
</div>

<div class="main">
    <div class="header">
        <h1>Event Approval</h1>
        <div class="profile">
            <div class="profile-icon">👤</div>
            <span>Coordinator</span>
            <button onclick="alert('Signed out')">Sign Out</button>
        </div>
    </div>

    <div class="container">
        <h2>Pending Event Submissions</h2>
        <table>
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Approval Letter</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                        <td><?= htmlspecialchars($row['event_date']) ?></td>
                        <td><?= htmlspecialchars($row['event_time']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <?php if (!empty($row['approval_letter'])): ?>
                                <a href="<?= htmlspecialchars($row['approval_letter']) ?>" target="_blank">View</a>
                            <?php else: ?>
                                No File
                            <?php endif; ?>
                        </td>
                        <td>
                            --- event_approval.php
                        <form action="update_status.php" method="POST">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
-                           <select name="approved_status" required>
                                <option value="">Select</option>
                                <option value="Approved">Approve</option>
                                <option value="Rejected">Reject</option>
                            </select>

                            <button type="submit">Update</button>
                        </form>

                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
