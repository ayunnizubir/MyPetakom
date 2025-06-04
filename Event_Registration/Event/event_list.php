<?php
// Event_Registration/Event/event_list.php

// 1) Fetch all events
$conn = new mysqli("localhost","root","","db_registration");
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
            <button>Sign Out</button>
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
                    <button class="edit" disabled>Edit</button>
                    <?php else: ?>
                    <a href="edit_event.php?id=<?= $ev['id'] ?>" class="edit">Edit</a>
                    <?php endif; ?>

                    <a href="delete_event.php?id=<?= $ev['id'] ?>"
                    class="delete"
                    onclick="return confirm('Delete this event?');">
                    Delete
                    </a>


                <button class="qr" onclick="toggleQR(<?= $ev['id'] ?>)">
                    QR
                </button>
                <div id="qrcode-<?= $ev['id'] ?>" class="qrcode-container"></div>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
    <script>

    const FALLBACK_HOST = '10.62.87.12';

    function toggleQR(eventId) {
        const holder = document.getElementById('qrcode-' + eventId);

        if (holder.innerHTML.trim() !== '') {
            holder.innerHTML = '';
            return;
        }

        let host = window.location.hostname;
        if (host === 'localhost' || host === '127.0.0.1') {
            host = FALLBACK_HOST;
        }

        const url = `${window.location.protocol}//${host}/mypetakom/Event_Registration/Event/view_event.php?event_id=${eventId}`;

        QRCode.toCanvas(url, { width: 128 }, (err, canvas) => {
        if (err) return console.error(err);
        holder.innerHTML = '';
        holder.appendChild(canvas);

        const a = document.createElement('a');
        a.href = canvas.toDataURL();
        a.download = `event_${eventId}_qr.png`;
        a.textContent = 'Download QR';
        a.style.display = 'block';
        a.style.marginTop = '8px';
        holder.appendChild(a);
        });
    }
    </script>
</body>
</html>
