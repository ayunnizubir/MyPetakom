<?php
include('db.php');
include('phpqrcode/qrlib.php');

// --- Handle Delete Event ---
if (isset($_POST['delete_event']) && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $qr_file = "qrcodes/" . $event_id . ".png";
    
    // Delete QR code file if it exists
    if (file_exists($qr_file)) {
        unlink($qr_file);
    }

    // Optional: Delete event record from database (uncomment if needed)
    // $delete_query = "DELETE FROM events WHERE event_id='$event_id'";
    // mysqli_query($conn, $delete_query);
    
    // Redirect to avoid resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// --- Handle Event Search ---
$event = null;
$qr_file = '';
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    $query = "SELECT * FROM events WHERE event_id='$event_id'";
    $result = mysqli_query($conn, $query);
    $event = mysqli_fetch_assoc($result);

    $qr_data = "http://localhost/event_attendance/attendance_register.php?event_id=" . $event_id;
    $qr_file = "qrcodes/" . $event_id . ".png";
    QRcode::png($qr_data, $qr_file, QR_ECLEVEL_H, 5);
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
        .generate-btn {
            background: #bdbdbd;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 14px 36px;
            font-size: 20px;
            margin-bottom: 20px;
            cursor: not-allowed;
        }
        .qr-section img {
            margin: 18px 0 12px 0;
            width: 170px;
            height: 170px;
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
                <input type="text" name="event_id" placeholder="Enter event ID" required>
                <button type="submit">Search</button>
            </form>
            <?php if ($event) { ?>
                <table>
                    <tr>
                        <th>Event ID</th>
                        <th>Event Name</th>
                        <th>Date & Time</th>
                        <th>Location</th>
                        <th>Status</th>
                    </tr>
                    <tr>
                        <td><?= htmlspecialchars($event['event_id']) ?></td>
                        <td><?= htmlspecialchars($event['name']) ?></td>
                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($event['datetime']))) ?><br>at <?= htmlspecialchars(date('h.i a', strtotime($event['datetime']))) ?></td>
                        <td><?= htmlspecialchars($event['location']) ?></td>
                        <td><?= htmlspecialchars($event['status']) ?></td>
                    </tr>
                </table>
                <div class="qr-section">
                    <img src="<?= htmlspecialchars($qr_file) ?>" alt="QR Code">
                    <br>
                    <a href="<?= htmlspecialchars($qr_file) ?>" download>
                        <button type="button" class="download-btn">Download QR code</button>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
