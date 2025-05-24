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
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
    <style>
        td {
            vertical-align: middle;
            text-align: center;
        }
        .qr-canvas {
            margin: 0 auto;
            display: block;
            width: 80px;
            height: 80px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <img src="../css/petakom_logo.png" alt="PETAKOM Logo">
        <h2>MyPetakom</h2>
        <ul>
            <li><a href="../Advisor/dashboard_advisor.php">Dashboard Event Advisor</a></li>
            <li><a href="create_event.html">Create Event</a></li>
            <li><a href="event_list.php">Event List</a></li>
            <li><a href="../Committee/committee.php">Committee</a></li>
            <li><a href="../Merit/merit_applications.php">Merit Application</a></li>
        </ul>
    </div>

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
                    <th>QR Code</th>
                    <th>View</th>
                    <th>Edit</th>
                </tr>
                <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                    <?php if ($row['status'] != 'Cancelled'): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                        <td><?= htmlspecialchars($row['event_date']) ?></td>
                        <td><?= htmlspecialchars($row['event_time']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <?php if ($row['status'] == 'Approved'): ?>
                                <canvas id="qr-<?= $row['id'] ?>" class="qr-canvas"></canvas>
                            <?php else: ?>
                                <em>Pending</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="view_event.php?event_id=<?= $row['id'] ?>">
                                <button class="qr">View</button>
                            </a>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'Approved' || $row['status'] == 'Rejected'): ?>
                                <em>Locked</em>
                            <?php else: ?>
                                <a href="update_event.php?id=<?= $row['id'] ?>">
                                    <button class="edit">Edit</button>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        <?php $js = '';?>
        <?php // Generate JS to render each canvas ?>
        <?php $result->data_seek(0); $i=1; while ($row = $result->fetch_assoc()): ?>
            <?php if ($row['status'] == 'Approved'): ?>
                QRCode.toCanvas(
                    document.getElementById('qr-<?= $row['id'] ?>'),
                    'http://10.65.90.56/mypetakom/Event_Registration/Event/view_event.php?event_id=<?= $row['id'] ?>',
                    { width: 80 },
                    function(error) { if(error) console.error(error); }
                );
            <?php endif; ?>
        <?php endwhile; ?>
    });
    </script>
</body>
</html>
