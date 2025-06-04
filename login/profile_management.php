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
    <title>Profile Management - MyPetakom</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background-color: #f0f0f0;
            border-bottom: 1px solid #ccc;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info img{
            width:30px;
            height: 30px;
            border-radius: 50%;
            margin-left: 10px;
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
            margin-left: 220px;
            padding: 30px;
        }

        h2{
            text-align: center;
        }

        .profile-container{
            text-align: center;
            margin-top: 2rem;
        }

        .profile-image img{
            width: 120px;
            height: 160px;
            object-fit: cover;
            border: 1px solid #ccc;
            margin-bottom: 1rem;
        }

        .profile-info{
            max-width: 400px;
            margin: 0 auto;
            text-align: left;
            border: 1px solid #ccc;
            padding: 1rem;
            border-radius: 8px;
            background: #fafafa;
        }

        .profile-info p{
            margin: 10px 0;
        }


    .buttons {
      margin-top: 1.5rem;
      text-align: center;
    }

    .buttons a, .buttons button {
      display: inline-block;
      padding: 10px 20px;
      margin: 0 10px;
      text-decoration: none;
      color: white;
      background-color: #007BFF;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .buttons .danger {
      background-color: #dc3545;
    }

    form {
      display: inline;
    }
    </style>
</head>
<body>

<body>
<div style="display: flex; height: 100vh;"> <!-- Flex container for sidebar + main content -->

    <!-- Sidebar -->
    <nav style="width: 200px; background-color: #f0f0f0; padding: 20px; border-right: 1px solid #ccc;">
        <div class="header" style="text-align:center;">
            <img src="petakom_logo.png" alt="Petakom's Logo" width="120px" height="auto">
        </div>
        <h3>MyPetakom</h3>
        <a href="udashboard.php">Dashboard</a>
        <a href="profile_management.php">Profile Management</a>
        <a href="membership_application.php">Membership Application</a>
        <a href="event_list.php">Event List</a>
        <a href="event_attendance.php">Event Attendance</a>
    </nav>

    <!-- Main Content -->
    <div style="flex-grow: 1; display: flex; flex-direction: column;">
        <header>
            <div><strong>MyPetakom</strong></div>
            <div class="user-info">
                <div>Profile Management</div>
                <div><?php echo htmlspecialchars($username); ?></div>
                <form method="post" style="display:inline;">
                    <button type="submit" name="logout">Sign Out</button>
                </form>
            </div>
        </header>

        <main>
            <h2>Profile Information</h2>
            <div class="profile-info">
                <p><strong>User's Name:</strong> <?php echo isset($user['name']) ? htmlspecialchars($user['name']) : 'Not available'; ?></p>
                <p><strong>Matric ID:</strong> <?php echo isset($user['matric_id']) ? htmlspecialchars($user['matric_id']) : 'Not available'; ?></p>
                <p><strong>User's Email:</strong> <?php echo isset($user['email']) ? htmlspecialchars($user['email']) : 'Not available'; ?></p>
                <p><strong>User's Phone Number:</strong> <?php echo isset($user['phone']) ? htmlspecialchars($user['phone']) : 'Not available'; ?></p>
            </div>

            <div class="buttons">
                <a href="back.php">Back</a>
                <a href="edit.php?id=1">Edit</a>
                <form action="delete.php" method="post">
                    <input type="hidden" name="id" value="1">
                    <button class="danger" type="submit">Delete</button>
                </form>
            </div>
        </main>
    </div>
</div>
</body>

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