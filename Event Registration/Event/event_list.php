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
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <img src="../css/petakom_logo.png" alt="PETAKOM Logo">
        <h2>MyPetakom</h2>
        <ul>
            <li><a href="#">Dashboard Event Advisor</a></li>
            <li><a href="create_event.html">Create Event</a></li>
            <li><a href="#">Event List</a></li>
            <li><a href="#">Committee</a></li>
            <li><a href="#">Merit Application</a></li>
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
                <button>Sign Out</button>
            </div>
        </div>

        <div class="container">
            <h2>All Events</h2>
            <table>
                <tr>
                    <th>No</th>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Status</th>
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
                    <td><?= $row['status'] ?></td>
                    <td>
                        <?php if ($row['status'] !== 'Approved'): ?>
                            <a href="edit_event.php?id=<?= $row['id'] ?>"><button class="edit">Edit</button></a>
                        <?php else: ?>
                            <button class="edit" disabled>Approved</button>
                        <?php endif; ?>
                        <a href="delete_event.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure to delete this event?');">
                            <button class="delete">Delete</button>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
