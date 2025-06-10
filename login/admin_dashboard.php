<?php
session_start();

// Check authentication and admin role
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['userType'] === 'user') {
    header("Location: udashboard.php");
    exit();
}

// Database connection
$db_host = 'localhost';
$db_user = 'root'; // Replace with your database username
$db_pass = ''; // Replace with your database password
$db_name = 'mypetakom'; // Replace with your database name

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch membership applications
$membershipList = [];
if ($_SESSION['userType'] === 'admin') {
    $query = "SELECT * FROM membership_applications ORDER BY application_date DESC";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $membershipList[] = $row;
        }
    }
}

$username = $_SESSION['username'] ?? 'Admin';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Logout logic
    if (isset($_POST['logout'])) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
    
    // Update status logic for admin
    if (isset($_POST['update_status']) && $_SESSION['userType'] === 'admin') {
        $application_id = $_POST['application_id'];
        $new_status = $_POST['status'];
        
        $stmt = $conn->prepare("UPDATE membership_applications SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $application_id);
        $stmt->execute();
        $stmt->close();
        
        // Refresh the page to show updated data
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - MyPetakom</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            min-height: 100vh;
        }

        header, nav, main, table {
            box-sizing: border-box;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #f4f4f4;
            border-bottom: 1px solid #ccc;
        }

        header .logo {
            font-weight: bold;
        }

        header .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        nav {
            width: 200px;
            background-color: #f0f0f0;
            padding: 20px;
            border-right: 1px solid #ccc;
        }

        nav a {
            display: block;
            margin-bottom: 20px;
            text-decoration: none;
            color: black;
        }

        main {
            flex-grow: 1;
            padding: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #aaa;
            padding: 10px;
            text-align: center;
        }

        .action-buttons form {
            display: inline-block;
            margin: 0 5px;
        }

        .action-buttons button {
            padding: 5px 10px;
            cursor: pointer;
        }

        .status-select {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .status-pending {
            color: #8a6d3b;
            background-color: #fcf8e3;
        }

        .status-approved {
            color: #3c763d;
            background-color: #dff0d8;
        }

        .status-rejected {
            color: #a94442;
            background-color: #f2dede;
        }

        .no-applications {
            padding: 20px;
            text-align: center;
            font-style: italic;
            color: #666;
        }
        img.card-preview {
            width: 100px;
            height: auto;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<nav>
    <div class="header">
        <img src="petakom_logo.png" alt="Petakom's Logo" width="120px" height="auto">
    </div>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="profile_management.php">Profile Management</a>
    <a href="event_list.php">Event List</a>
    <a href="event_attendance.php">Event Attendance</a>
</nav>

<div style="flex-grow: 1;">
    <header>
        <div><strong>MyPetakom - Admin Panel</strong></div>
        <div class="user-info">
            <div><?php echo htmlspecialchars($username); ?></div>
            <form method="post" style="display:inline;">
                <button type="submit" name="logout">Sign Out</button>
            </form>
        </div>
    </header>

    <main>
        <h2>Membership Applications</h2>
        
        <?php if (empty($membershipList)): ?>
            <div class="no-applications">No membership applications found.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Applicant Name</th>
                        <th>Username</th>
                        <th>Student Card Photo</th>
                        <th>Application Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($membershipList as $index => $application): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($application['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($application['matricid']); ?></td>
                        <td>
                            <a href="<?php echo htmlspecialchars($row['student_card_path']); ?>" target="_blank">
                            <img src="<?php echo htmlspecialchars($row['student_card_path']); ?>" alt="Student Card" class="card-preview">
                            </a>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($application['application_date'])); ?></td>
                        <td class="status-<?php echo strtolower($application['status']); ?>">
                            <?php echo htmlspecialchars($application['status']); ?>
                        </td>
                        <td class="action-buttons">
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                <select name="status" class="status-select">
                                    <option value="Pending" <?php echo $application['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Approved" <?php echo $application['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                                    <option value="Rejected" <?php echo $application['status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                                <button type="submit" name="update_status">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</div>

</body>
</html>