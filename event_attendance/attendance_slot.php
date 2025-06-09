<?php
include('db.php');
include('phpqrcode/qrlib.php');

// --- Handle Delete Event QR ---
if (isset($_POST['delete_event']) && isset($_POST['delete_event_id'])) {
    $event_id = intval($_POST['delete_event_id']);
    $qr_file  = "qrcodes/{$event_id}.png";
    if (file_exists($qr_file)) {
        unlink($qr_file);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// --- Handle Event Search by Name ---
$events   = [];
$qr_files = [];
if (isset($_GET['event_name'])) {
    $search = mysqli_real_escape_string($conn, $_GET['event_name']);
    $sql    = "
      SELECT id, event_name, event_date, event_time, location, status
        FROM events
       WHERE event_name LIKE '%{$search}%'
       ORDER BY event_date DESC, event_time DESC
    ";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
        // generate QR if not exists
        $qr_data = "http://localhost/mypetakom/event_attendance/attendance_register.php?id=" . $row['id'];
        $qr_file = "qrcodes/{$row['id']}.png";
        if (!file_exists($qr_file)) {
            QRcode::png($qr_data, $qr_file, QR_ECLEVEL_H, 5);
        }
        $qr_files[$row['id']] = $qr_file;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Attendance Slot</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            background: #f5f5f5;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: 230px;
            background: #e0e0e0;
            padding-top: 18px;
            z-index: 100;
        }
        .sidebar .logo {
            display: block;
            margin: 0 auto 25px auto;
            width: 120px;
        }
        .sidebar h3 {
            margin-left: 24px;
            font-size: 20px;
            color: #333;
            margin-bottom: 18px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0 0 0 10px;
        }
        .sidebar ul li {
            margin-bottom: 8px;
            font-size: 15px;
        }
        .sidebar ul li a {
            color: #222;
            text-decoration: none;
            padding: 7px 16px;
            border-radius: 6px;
            display: block;
            transition: background 0.2s;
        }
        .sidebar ul li a.active,
        .sidebar ul li a:hover {
            background: #b2dfdb;
        }
        .header {
            margin-left: 230px;
            height: 70px;
            background: #a7ffeb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px 0 24px;
            box-sizing: border-box;
        }
        .header-title {
            font-size: 24px;
            font-weight: bold;
            color: #2d3a4b;
        }
        .header-user {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .header-user .user-circle {
            background: #e0f2f1;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #333;
        }
        .header-user span {
            font-size: 16px;
            color: #222;
        }
        .header-user .signout-btn {
            background: #333;
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 8px 20px;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .header-user .signout-btn:hover {
            background: #222;
        }
        .main-content {
            margin-left: 230px;
            padding: 40px 0;
            min-height: 100vh;
            background: #f5f5f5;
        }
        .content-card {
            background: #fff;
            max-width: 700px;
            margin: 0 auto;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            padding: 40px 36px 36px 36px;
        }
        .content-card h2 {
            font-size: 22px;
            margin-bottom: 18px;
            color: #222;
        }
        .search-row {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
        }
        .search-row input[type="text"] {
            flex: 1;
            padding: 10px;
            font-size: 16px;
            border-radius: 6px;
            border: 1px solid #bdbdbd;
        }
        .search-row button {
            background: #757575;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px 24px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .search-row button:hover {
            background: #424242;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 10px 8px;
            border: 1px solid #bdbdbd;
            text-align: center;
        }
        th {
            background: #f0f4f8;
            color: #2d3a4b;
        }
        .qr-section {
            text-align: center;
            margin-top: 30px;
        }
        .delete-btn {
            background: #d32f2f;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .delete-btn:hover {
            background: #b71c1c;
        }
        .download-btn {
            background: linear-gradient(90deg, #2979ff, #00b0ff);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 14px 38px;
            font-size: 18px;
            font-weight: 500;
            margin-top: 15px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .download-btn:hover {
            background: linear-gradient(90deg, #1565c0, #00838f);
        }
        @media (max-width: 900px) {
            .sidebar, .header { width: 100%; margin-left: 0; }
            .main-content { margin-left: 0; }
            .content-card { max-width: 98vw; padding: 18px; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <img src="petakom.png" alt="Petakom Logo" class="logo">
        <h3>MyPetakom</h3>
        <ul>
            <li><b>Petakom Coordinator</b>
                <ul>
                    <li><a href="#">Petakom Coordinator Dashboard</a></li>
                </ul>
            </li>
            <li><b>Event Advisor</b>
                <ul>
                    <li><a href="#" class="active">Create Attendance Slot</a></li>
                </ul>
            </li>
            <li><b>Student</b>
                <ul>
                    <li><a href="#">Attendance Registration</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="header">
        <span class="header-title">Create Attendance Slot</span>
        <div class="header-user">
            <span>User's Name</span>
            <div class="user-circle">
                <span>&#128100;</span>
            </div>
            <button class="signout-btn">Sign Out</button>
        </div>
    </div>
    <div class="main-content">
        <div class="content-card">
            <h2>Event Attendance Slot</h2>

            <form method="GET" class="search-row">
                <input type="text" name="event_name" placeholder="Enter event name" required>
                <button type="submit">Search</button>
            </form>

            <?php if (!empty($events)): ?>
                <table>
                    <tr>
                        <th>Event ID</th>
                        <th>Event Name</th>
                        <th>Date &amp; Time</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>QR Code</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($events as $ev): ?>
                    <tr>
                        <td><?= htmlspecialchars($ev['id']) ?></td>
                        <td><?= htmlspecialchars($ev['event_name']) ?></td>
                        <td>
                          <?= htmlspecialchars($ev['event_date']) ?>
                          <br>at <?= htmlspecialchars($ev['event_time']) ?>
                        </td>
                        <td><?= htmlspecialchars($ev['location']) ?></td>
                        <td><?= htmlspecialchars($ev['status']) ?></td>
                        <td>
                            <img src="<?= htmlspecialchars($qr_files[$ev['id']]) ?>" width="80" alt="QR">
                            <br>
                            <a href="<?= htmlspecialchars($qr_files[$ev['id']]) ?>" download>
                                <button type="button" class="download-btn">Download QR</button>
                            </a>
                        </td>
                        <td>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="delete_event_id" value="<?= $ev['id'] ?>">
                                <button type="submit" name="delete_event" class="delete-btn"
                                  onclick="return confirm('Delete QR for event <?= $ev['id'] ?>?')">
                                  Delete QR
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php elseif (isset($_GET['event_name'])): ?>
                <p style="color: #c00; text-align: center;">
                  No events found for “<?= htmlspecialchars($_GET['event_name']) ?>”.
                </p>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>
