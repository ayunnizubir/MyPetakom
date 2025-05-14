<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

$username = $_SESSION['username'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - MyPetakom</title>
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

        .action-buttons button {
            margin: 0 5px;
            padding: 5px 10px;
        }

        .status-select {
            border: none;
            background: transparent;
            font-weight: bold;
        }
    </style>
</head>
<body>

<nav>
    <div class="header">
        <img src="petakom_logo.png" alt="Petakom's Logo" width="120px" height="auto">
    </div>
    <a href="user_dashboard.php">Dashboard</a>
    <a href="profile_management.php">Profile Management</a>
    <a href="membership_application.php">Membership Application</a>
</nav>

<div style="flex-grow: 1;">
    <header>
        <div>MyPetakom</div>
        <div class="user-info">
            <div>Dashboard</div>
            <div><?php echo htmlspecialchars($username); ?></div>
            <form method="post" style="display:inline;">
                <button type="submit" name="logout">Sign Out</button>
            </form>
        </div>
    </header>

    <main>
        <h2>Membership Status</h2>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($membershipList as $index => $member): ?>
                <tr>
                    <td><?php echo $index + 1; ?>.</td>
                    <td><?php echo htmlspecialchars($user); ?></td>
                    <td>
                        <select class="status-select">
                            <option>Approve</option>
                            <option>Reject</option>
                        </select>
                    </td>
                    <td class="action-buttons">
                        <button>Edit</button>
                        <button>Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>

<?php
// Logout logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

</body>
</html>