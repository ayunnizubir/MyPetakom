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
    <title>MyPetakom - Edit Profile</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background-color: #f0f0f0;
            color: white;
            padding: 20px 0;
        }
        
        .logo {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #34495e;
        }
        
        .logo img {
            width: 120px;
        }
        
        .nav-menu {
            padding: 20px;
        }
        
        .nav-menu a {
            display: block;
            color: black;
            text-decoration: none;
            padding: 10px 15px;
            margin-bottom: 5px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .nav-menu a:hover {
            background-color: #f0f0f0;
        }
        
        .main-content {
            flex: 1;
            padding: 30px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        
        .header h1 {
            color: black;
            font-size: 24px;
        }
        
        .profile-form {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <img src="petakom_logo.png" alt="Petakom Logo">
            </div>
            <nav class="nav-menu">
                <a href="udashboard.php">Dashboard</a>
                <a href="profile_management.php" class="active">Profile Management</a>
                <a href="membership_application.php">Membership Application</a>
                <a href="event_list.php">Event List</a>
                <a href="event_attendance.php">Event Attendance</a>
            </nav>
        </aside>
        
        <main class="main-content">
            <div class="header">
                <h1>Edit Profile</h1>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <div class="profile-form">
                <form method="post">
                    <div class="form-group">
                        <label for="fullname">Name:</label>
                        <input type="text" id="fullname" name="fullname" 
                               value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="matricid">Matric ID:</label>
                        <input type="text" id="matricid" name="matricid" 
                               value="<?php echo htmlspecialchars($user['matricid'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number:</label>
                        <input type="text" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="text" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                    </div>

                    <div class="form-actions">
                        <a href="profile_management.php" class="btn btn-secondary">Back</a>
                        <button type="submit" name="update" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>