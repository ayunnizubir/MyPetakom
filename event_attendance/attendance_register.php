<?php
include('db.php');
session_start();

$event = null;
$message = '';
$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : null;

// Fetch event details
if ($event_id) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM events WHERE event_id = ?");
    mysqli_stmt_bind_param($stmt, "s", $event_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $event = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Handle check-in form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $event_id) {
    $student_id = $_POST['student_id'];
    $password = $_POST['password'];
    $location = $_POST['location'];

    // Validate student credentials
    $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE student_id = ? AND password = ?");
    mysqli_stmt_bind_param($stmt, "ss", $student_id, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($student) {
        // Check if student is already checked in
        $stmt = mysqli_prepare($conn, "SELECT * FROM attendance WHERE event_id = ? AND student_id = ?");
        mysqli_stmt_bind_param($stmt, "ss", $event_id, $student_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $attendance = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($attendance) {
            $message = "<div class='alert alert-error'>You have already checked in to this event.</div>";
        } else {
            // Record attendance
            $stmt = mysqli_prepare($conn, "INSERT INTO attendance (event_id, student_id, checkin_time, location) VALUES (?, ?, NOW(), ?)");
            mysqli_stmt_bind_param($stmt, "sss", $event_id, $student_id, $location);
            if (mysqli_stmt_execute($stmt)) {
                $message = "<div class='alert alert-success'>Attendance recorded successfully!</div>";
            } else {
                $message = "<div class='alert alert-error'>Error recording attendance. Please try again.</div>";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $message = "<div class='alert alert-error'>Invalid credentials.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance Check-In</title>
    <style>
        :root {
            --primary: #4CAF50;
            --primary-hover: #388E3C;
            --error: #d32f2f;
            --error-bg: #ffebee;
            --success: #2e7d32;
            --success-bg: #e8f5e9;
            --card-bg: #fff;
            --card-shadow: 0 4px 16px rgba(0,0,0,0.08);
            --sidebar-bg: #e0e0e0;
            --sidebar-width: 230px;
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
        .card .event-details {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        .card .event-details p {
            margin: 8px 0;
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
        input, button {
            padding: 12px;
            margin: 8px 0;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }
        button {
            background: var(--primary);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.2s;
        }
        button:hover {
            background: var(--primary-hover);
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
    <!-- Sidebar (optional) -->
    <div class="sidebar">
        <img src="petakom.png" alt="Petakom Logo" class="logo">
        <h3>MyPetakom</h3>
        <ul>
            <li><b>Student</b>
                <ul>
                    <li><a href="#" class="active">Attendance Check-In</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <!-- Main Content -->
    <div class="main-content">
        <div class="card">
            <h2>Student Attendance Check-In</h2>
            <?php if ($event): ?>
                <div class="event-details">
                    <h3><?= htmlspecialchars($event['name']) ?></h3>
                    <p><strong>Date & Time:</strong> <?= htmlspecialchars($event['datetime']) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                    <?php if (isset($event['description'])): ?>
                        <p><strong>Description:</strong> <?= htmlspecialchars($event['description']) ?></p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-error">Event not found or no event ID provided.</div>
            <?php endif; ?>
            <?php if ($message): ?>
                <?= $message ?>
            <?php endif; ?>
            <?php if ($event): ?>
                <form method="POST">
                    <input type="hidden" id="location" name="location">
                    <input type="text" name="student_id" placeholder="Student ID" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Check In</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <script>
        navigator.geolocation.getCurrentPosition(function(pos) {
            document.getElementById('location').value = pos.coords.latitude + "," + pos.coords.longitude;
        }, function(error) {
            alert("Location access is required for attendance.");
        });
    </script>
</body>
</html>
