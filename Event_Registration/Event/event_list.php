<?php
// event_list.php — show all events, disable Edit for “Cancelled” and style buttons

$conn   = new mysqli("localhost","root","","db_registration");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$result = $conn->query("SELECT * FROM events ORDER BY event_date, event_time");
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event List</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* BUTTON STYLES */
        .btn-create,
        .btn-edit,
        .btn-delete {
        display: inline-block;
        padding: 0.5rem 1rem;
        margin: 0.25rem;
        font-size: 0.9rem;
        border: none;
        border-radius: 4px;
        color: #fff;
        text-decoration: none;
        cursor: pointer;
        }
        .btn-create { background-color: #4CAF50; }
        .btn-create:hover { background-color: #45A049; }
        .btn-edit   { background-color: #2196F3; }
        .btn-edit:hover   { background-color: #0B7FDA; }
        .btn-delete { background-color: #F44336; }
        .btn-delete:hover { background-color: #D32F2F; }
        .btn-edit[disabled] {
        background-color: #aaa;
        cursor: not-allowed;
        }

        /* Make sure header & container from your CSS still apply */
        .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <img src="../css/petakom_logo.png" alt="PETAKOM Logo">
        <h2>MyPetakom</h2>
        <ul>
        <li><a href="dashboard_advisor.php">Dashboard Event Advisor</a></li>
        <li><a href="create_event.html">Create Event</a></li>
        <li><a href="event_list.php" class="active">Event List</a></li>
        <li><a href="../Committee/committee.php">Committee</a></li>
        <li><a href="../Merit/merit_applications.php">Merit Application</a></li>
        </ul>
    </div>

    <!-- Main content -->
    <div class="main">
        <div class="header">
        <h1>Event List</h1>
        <div class="profile">
            <div class="profile-icon">👤</div>
            <span>User's Name</span>
            <button>Sign Oubutton>
        </div>
        </div>

        <div class="container">
        <a href="create_event.html" class="btn-create">+ Create New Event</a>

        <?php if ($result->num_rows === 0): ?>
            <p><em>No events found.</em></p>
        <?php else: ?>
            <table>
            <thead>
                <tr>
                <th>Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Location</th>
                <th>Status</th>
                <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($ev = $result->fetch_assoc()): ?>
                <tr>
                <td><?= htmlspecialchars($ev['event_name']) ?></td>
                <td><?= htmlspecialchars($ev['event_date']) ?></td>
                <td><?= htmlspecialchars($ev['event_time']) ?></td>
                <td><?= htmlspecialchars($ev['location']) ?></td>
                <td><?= htmlspecialchars($ev['status']) ?></td>
                <td>
                    <?php if ($ev['status'] === 'Cancelled'): ?>
                        <button class="btn-edit" disabled>Edit</button>
                    <?php else: ?>
                        <a href="edit_event.php?id=<?= $ev['id'] ?>" class="btn-edit">Edit</a>
                    <?php endif; ?>
                    <a href="delete_event.php?id=<?= $ev['id'] ?>"
                        class="btn-delete"
                        onclick="return confirm('Delete this event?');">
                        Delete
                    </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            </table>
        <?php endif; ?>
    </div>
    </div>
</body>
</html>
