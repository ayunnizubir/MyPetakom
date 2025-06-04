<?php
include('db.php');
session_start();

$message = '';
$attendance = null;
$attendances = [];
$event_name = '';
$editing_id = null;

// Fetch all event names for the dropdown
$event_names = [];
$res = mysqli_query($conn, "SELECT DISTINCT name FROM events ORDER BY name");
while ($row = mysqli_fetch_assoc($res)) {
    $event_names[] = $row['name'];
}

// Step 1: Search for attendance records by event name
if (isset($_GET['event_name'])) {
    $event_name = $_GET['event_name'];
    // Get event_id for this event name
    $stmt = mysqli_prepare($conn, "SELECT event_id FROM events WHERE name = ?");
    mysqli_stmt_bind_param($stmt, "s", $event_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $event = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($event) {
        $event_id = $event['event_id'];
        // Get all attendance records for this event
        $stmt = mysqli_prepare($conn, "SELECT * FROM attendance WHERE event_id = ?");
        mysqli_stmt_bind_param($stmt, "s", $event_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $attendances[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}

// Step 2: Load attendance record for editing
if (isset($_GET['edit_id'])) {
    $editing_id = $_GET['edit_id'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM attendance WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $editing_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $attendance = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Step 3: Handle the update
if (isset($_POST['update_attendance'])) {
    $attendance_id = $_POST['attendance_id'];
    $new_time = $_POST['checkin_time'];
    $new_location = $_POST['location'];

    $stmt = mysqli_prepare($conn, "UPDATE attendance SET checkin_time = ?, location = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $new_time, $new_location, $attendance_id);
    if (mysqli_stmt_execute($stmt)) {
        $message = "<div class='alert alert-success'>Attendance updated successfully!</div>";
        // Refresh the edited record
        $stmt2 = mysqli_prepare($conn, "SELECT * FROM attendance WHERE id = ?");
        mysqli_stmt_bind_param($stmt2, "i", $attendance_id);
        mysqli_stmt_execute($stmt2);
        $result = mysqli_stmt_get_result($stmt2);
        $attendance = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt2);
    } else {
        $message = "<div class='alert alert-error'>Error updating attendance.</div>";
    }
    mysqli_stmt_close($stmt);
    // To keep the event list visible after update
    if ($attendance) {
        $event_id = $attendance['event_id'];
        $event_name = '';
        $stmt = mysqli_prepare($conn, "SELECT name FROM events WHERE event_id = ?");
        mysqli_stmt_bind_param($stmt, "s", $event_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $event_name = $row['name'];
        }
        mysqli_stmt_close($stmt);
        // Reload attendance list
        $stmt = mysqli_prepare($conn, "SELECT * FROM attendance WHERE event_id = ?");
        mysqli_stmt_bind_param($stmt, "s", $event_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $attendances = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $attendances[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* (Use your existing CSS from previous code for sidebar, topbar, main-content, etc.) */
        body { margin: 0; font-family: 'Segoe UI', Arial, sans-serif; background: #f4f4f4; }
        .sidebar { width: 240px; background: #f8f9fa; position: fixed; top: 0; left: 0; bottom: 0; padding: 24px 0 0 0; border-right: 1px solid #e0e0e0; }
        .sidebar .logo { display: block; margin: 0 auto 24px auto; width: 120px; }
        .sidebar h2 { font-size: 22px; margin: 0 0 20px 28px; font-weight: 700; color: #222; }
        .sidebar ul { list-style: none; padding: 0 0 0 28px; margin: 0; }
        .sidebar ul li { margin-bottom: 12px; font-weight: 500; color: #222; }
        .sidebar ul li.section { margin-top: 18px; color: #888; font-size: 14px; font-weight: 600; }
        .sidebar ul li.active, .sidebar ul li a.active { background: #d4f7f5; border-radius: 6px; color: #222; }
        .sidebar ul li a { color: inherit; text-decoration: none; display: block; padding: 6px 12px; border-radius: 5px; transition: background 0.2s; }
        .sidebar ul li a:hover { background: #e0f7fa; }
        .topbar { margin-left: 240px; height: 70px; background: #b7f8ef; display: flex; align-items: center; justify-content: space-between; padding: 0 36px; box-sizing: border-box; border-bottom: 1px solid #e0e0e0; }
        .topbar h1 { font-size: 28px; font-weight: 700; margin: 0; color: #222; }
        .topbar .user { display: flex; align-items: center; }
        .topbar .user-icon { width: 36px; height: 36px; background: #e0f7fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 14px; font-size: 20px; color: #222; }
        .topbar .username { margin-right: 18px; font-weight: 500; color: #222; }
        .topbar .signout-btn { background: #222; color: #fff; border: none; border-radius: 7px; padding: 9px 22px; font-size: 16px; font-weight: 500; cursor: pointer; transition: background 0.2s; }
        .topbar .signout-btn:hover { background: #444; }
        .main-content { margin-left: 240px; padding: 40px 0; min-height: calc(100vh - 70px); background: #f4f4f4; }
        .center-card { max-width: 800px; margin: 0 auto; background: #fff; border-radius: 14px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); padding: 36px 32px; }
        .center-card h2 { margin-top: 0; color: #2d3a4b; font-size: 24px; font-weight: 700; margin-bottom: 20px; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 18px; font-weight: 500; }
        .alert-success { background: #e8f5e9; color: #2e7d32; }
        .alert-error { background: #ffebee; color: #d32f2f; }
        form { margin-top: 18px; }
        label { font-weight: 500; display: block; margin-bottom: 5px; color: #333; }
        input, button, select { padding: 12px; margin: 8px 0 18px 0; width: 100%; box-sizing: border-box; border: 1px solid #ccc; border-radius: 8px; font-size: 16px; }
        input[disabled] { background: #f0f0f0; }
        button { background: #4CAF50; color: white; border: none; cursor: pointer; font-weight: 600; transition: background 0.2s; }
        button:hover { background: #388E3C; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f0f0f0; }
        .edit-link { color: #2196F3; text-decoration: underline; cursor: pointer; }
        @media (max-width: 900px) {
            .sidebar, .topbar { position: static; width: 100%; margin-left: 0; }
            .main-content { margin-left: 0; padding: 24px 0; }
            .center-card { padding: 22px 8px; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <img src="petakom.png" alt="Petakom Logo" class="logo">
        <h2>MyPetakom</h2>
        <ul>
            <li class="section">Petakom Coordinator</li>
            <li><a href="#">Petakom Coordinator Dashboard</a></li>
            <li class="section">Event Advisor</li>
            <li><a href="#" class="active">Edit Attendance</a></li>
            <li class="section">Student</li>
            <li><a href="#">Attendance Registration</a></li>
        </ul>
    </div>
    <!-- Topbar -->
    <div class="topbar">
        <h1>Edit Attendance</h1>
        <div class="user">
            <div class="user-icon">👤</div>
            <span class="username">User's Name</span>
            <button class="signout-btn">Sign Out</button>
        </div>
    </div>
    <!-- Main Content -->
    <div class="main-content">
        <div class="center-card">
            <h2>Edit Attendance Record</h2>
            <?php if ($message) echo $message; ?>

            <!-- Search by Event Name -->
            <form method="get" class="search-form">
                <label for="event_name">Select Event Name:</label>
                <select name="event_name" id="event_name" required>
                    <option value="">-- Select Event --</option>
                    <?php foreach ($event_names as $name): ?>
                        <option value="<?= htmlspecialchars($name) ?>" <?= ($name == $event_name) ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Search</button>
            </form>

            <!-- Attendance List -->
            <?php if ($event_name && count($attendances) > 0): ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Check-In Time</th>
                        <th>Location</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($attendances as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['student_id']) ?></td>
                            <td><?= htmlspecialchars($row['checkin_time']) ?></td>
                            <td><?= htmlspecialchars($row['location']) ?></td>
                            <td>
                                <a href="?event_name=<?= urlencode($event_name) ?>&edit_id=<?= $row['id'] ?>" class="edit-link">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php elseif ($event_name): ?>
                <div class="alert alert-error">No attendance records found for this event.</div>
            <?php endif; ?>

            <!-- Edit Form -->
            <?php if ($attendance): ?>
                <form method="post">
                    <input type="hidden" name="attendance_id" value="<?= htmlspecialchars($attendance['id']) ?>">
                    <label>Student ID:</label>
                    <input type="text" value="<?= htmlspecialchars($attendance['student_id']) ?>" disabled>
                    <label>Event ID:</label>
                    <input type="text" value="<?= htmlspecialchars($attendance['event_id']) ?>" disabled>
                    <label for="checkin_time">Check-In Time:</label>
                    <input type="datetime-local" name="checkin_time" id="checkin_time" value="<?= date('Y-m-d\TH:i', strtotime($attendance['checkin_time'])) ?>" required>
                    <label for="location">Location:</label>
                    <input type="text" name="location" id="location" value="<?= htmlspecialchars($attendance['location']) ?>" required>
                    <button type="submit" name="update_attendance">Update Attendance</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
