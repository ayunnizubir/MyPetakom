<?php
include('db.php');
session_start();

$delete_message = '';

// ── Load all events for the dropdown ──
$events = [];
$res    = mysqli_query($conn, "SELECT id, event_name FROM events ORDER BY event_date, event_time");
while ($row = mysqli_fetch_assoc($res)) {
    $events[] = $row;
}

// ── Handle delete attendance form submission ──
if (isset($_POST['delete_attendance'])) {
    $student_id = $_POST['student_id'];
    $event_id   = intval($_POST['event_id']);

    $stmt = mysqli_prepare(
      $conn,
      "DELETE FROM attendance
         WHERE student_id = ?
           AND event_id   = ?"
    );
    mysqli_stmt_bind_param($stmt, "si", $student_id, $event_id);

    if (mysqli_stmt_execute($stmt)) {
        $delete_message = "<div class='alert alert-success'>
                              Attendance record deleted successfully!
                           </div>";
    } else {
        $delete_message = "<div class='alert alert-error'>
                              Error deleting attendance record.
                           </div>";
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Delete Attendance</title>
    <style>
        :root {
            --success: #2e7d32;
            --success-bg: #e8f5e9;
            --error: #d32f2f;
            --error-bg: #ffebee;
            --card-bg: #fff;
            --card-shadow: 0 4px 16px rgba(0,0,0,0.08);
            --sidebar-bg: #e0e0e0;
            --sidebar-width: 230px;
            --danger: #d32f2f;
            --danger-hover: #b71c1c;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            background: #f5f5f5;
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            padding: 18px 10px;
            box-sizing: border-box;
        }
        .sidebar .logo {
            display: block;
            margin: 0 auto 25px;
            width: 120px;
        }
        .sidebar h3 {
            margin: 0 0 18px 14px;
            font-size: 20px;
            color: #333;
        }
        .sidebar ul {
            list-style: none;
            padding: 0 0 0 6px;
            margin: 0;
        }
        .sidebar ul li {
            margin-bottom: 8px;
        }
        .sidebar ul li a {
            display: block;
            color: #222;
            text-decoration: none;
            padding: 7px 16px;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: #b2dfdb;
        }
        .main-content {
            flex: 1;
            padding: 40px;
            box-sizing: border-box;
        }
        .card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 36px;
            max-width: 600px;
            margin: 0 auto;
        }
        .card h2 {
            color: #2d3a4b;
            margin-top: 0;
            margin-bottom: 24px;
        }
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin: 0 0 20px 0;
            font-weight: 500;
        }
        .alert-success {
            background: var(--success-bg);
            color: var(--success);
        }
        .alert-error {
            background: var(--error-bg);
            color: var(--error);
        }
        form {
            margin-top: 20px;
        }
        input, select, button {
            padding: 12px;
            margin: 8px 0;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }
        button {
            background: var(--danger);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.2s;
        }
        button:hover {
            background: var(--danger-hover);
        }
        @media (max-width: 900px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                padding: 12px;
            }
            .main-content {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <img src="petakom.png" alt="Petakom Logo" class="logo">
        <h3>MyPetakom</h3>
        <ul>
            <li><b>Event Advisor</b>
                <ul>
                    <li><a href="#" class="active">Delete Attendance</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <!-- Main Content -->
    <div class="main-content">
        <div class="card">
            <h2>Event Advisor: Delete Attendance</h2>

            <!-- Show result message -->
            <?= $delete_message ?>

            <form method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this attendance record?');">
                <label for="student_id">Student ID:</label>
                <input type="text" name="student_id" id="student_id" placeholder="Student ID" required>

                <label for="event_id">Event:</label>
                <select name="event_id" id="event_id" required>
                    <option value="">-- Select Event --</option>
                    <?php foreach ($events as $ev): ?>
                        <option value="<?= $ev['id'] ?>">
                            <?= htmlspecialchars($ev['event_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" name="delete_attendance">Delete Attendance</button>
            </form>
        </div>
    </div>
</body>
</html>
