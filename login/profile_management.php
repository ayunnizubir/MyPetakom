<?php
// edit_profile.php - Profile editing page with matching style

// Database configuration
$host = 'localhost';
$dbname = 'mypetakom';
$username = 'root';
$password = '';

// Start session
session_start();

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$logged_in_username = $_SESSION['username'] ?? 'Guest';
$current_user_id = $_SESSION['user_id'] ?? null;

// Initialize variables
$errors = [];
$success = '';
$user = [];

try {
    // Create database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get current user data using session ID
    $current_user_id = $_SESSION['user_id'] ?? null;
    if (!$current_user_id) {
        die("User not logged in");
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$current_user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found");
    }

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        // Sanitize and validate inputs
        $fullname = trim($_POST['fullname'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        // Validation
        if (empty($fullname)) $errors[] = "Full name is required";

        // Update database if no errors
        if (empty($errors)) {
            $sql = "UPDATE users SET fullname = ?, phone = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([$fullname, $phone, $current_user_id]);

            if ($result) {
                $success = "Profile updated successfully!";
                // Refresh user data
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$current_user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $errors[] = "Failed to update profile";
            }
        }
    }
} catch (PDOException $e) {
    $errors[] = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyPetakom - Profile</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            display: flex;
            min-height: 100vh;
        }
        
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background-color: #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            z-index: 100;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .nav-sidebar {
            width: 250px;
            background-color: #f0f0f0;
            height: 100vh;
            position: fixed;
            top: 60px;
            left: 0;
            padding: 20px 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .main-content {
            flex: 1;
            margin-left: 250px;
            margin-top: 60px;
            padding: 30px;
        }
        
        .nav-menu {
            list-style: none;
        }
        
        .nav-menu li {
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
        }
        
        .nav-menu li a {
            text-decoration: none;
            color: #333;
            display: block;
        }
        
        .nav-menu li:hover {
            background-color: #e0e0e0;
        }
        
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .profile-header h1 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .profile-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .detail-item {
            margin-bottom: 15px;
        }
        
        .detail-item label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        
        .detail-item span {
            display: block;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
                .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .buttons a,
        .buttons button {
            padding: 10px 20px;
            background: #007BFF;
            color: white;
            border: none;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .buttons button:hover,
        .buttons a:hover {
            background: #0056b3;
        }

        .buttons .danger {
            background: #dc3545;
        }

        .buttons .danger:hover {
            background: #b52a37;
        }

        .logout-btn {
            background-color: grey;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .logout-btn:hover {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>MyPetakom</h2>
        <div>
            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <button class="logout-btn" onclick="window.location.href='login.php'">Sign Out</button>
        </div>
    </div>
    
    <div class="nav-sidebar">
        <img src="petakom_logo.png" alt="Petakom's Logo" width="120px" height="auto">
        <ul class="nav-menu">
            <li><a href="udashboard.php">Dashboard</a></li>
            <li><a href="profile_management.php" style="font-weight: bold; color: #0066cc;">Profile Management</a></li>
            <li><a href="membership_application.php">Membership Application</a></li>
            <li><a href="events_list.php">Event List</a></li>
            <li><a href="attendance.php">Event Attendance</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="profile-container">
            <div class="profile-header">
                <h1>User Profile</h1>
            </div>
            
            <div class="profile-details">
                <div class="detail-item">
                    <label>Full Name</label>
                    <span><?php echo htmlspecialchars($user['fullname']); ?></span>
                </div>
                
                <div class="detail-item">
                    <label>Matric ID</label>
                    <span><?php echo htmlspecialchars($user['matricid']); ?></span>
                </div>
                
                <div class="detail-item">
                    <label>Email</label>
                    <span><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                
                <div class="detail-item">
                    <label>Phone Number</label>
                    <span><?php echo htmlspecialchars($user['phone']); ?></span>
                </div>
            </div>
        </div>
        <div class="buttons">
    <a href="edit.php?id=<?php echo $current_user_id; ?>">Edit</a>
    <a href="confirm_delete.php?id=<?php echo $current_user_id; ?>" class="danger">Delete</a>
        </div>
    </div>   
</body>
</html>