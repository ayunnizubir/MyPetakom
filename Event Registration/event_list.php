<?php
$conn = new mysqli("localhost", "root", "", "db_registration");
$result = $conn->query("SELECT * FROM events");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event List</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Optional inline override styles */
        .action-buttons button {
            margin-right: 5px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <img src="petakom_logo.png" alt="PETAKOM Logo"><br><br>
    <h2>MyPetakom</h2>
    <ul>
        <li><strong>Event Advisor</strong></li>
        <li><a href="#">Dashboard Event Advisor</a></li>
        <li><a href="create_event.html">Create Event</a></li>
        <li><a href="event_list.php" class="active">Event List</a></li>
        <li><a href="#">Committee</a></li>
        <li><a href="merit_applications.php">Merit Application</a></li>
        <li><a href="#">QR Codes</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main">
    <div class="header">
        <h1>Event List</h1>
        <div class="profile">
            <div class="profile-icon">👤</div>
            <span>User's Name</span>
            <button onclick="alert('Signed out')">Sign Out</button>
        </div>
    </div>

    <div class="container">
        <h2>All Events</h2>
        <a href="create_event.html"><button>Create New Event</button></a>
        <table>
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                        <td><?= htmlspecialchars($row['event_date']) ?> at <?= htmlspecialchars($row['event_time']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td class="action-buttons">
                            <a href="edit_event.php?id=<?= $row['id'] ?>"><button class="edit">Edit</button></a>
                            <form action="delete_event.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" class="delete">Delete</button>
                            </form>
                            <button class="qr">View QR</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
