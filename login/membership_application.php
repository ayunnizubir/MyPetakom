<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'mypetakom';

// Create connection
try {
    $db = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    // Check connection
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $db->real_escape_string($_POST['full_name']);
    $matric_id = $db->real_escape_string($_POST['matric_id']);
    $course = $db->real_escape_string($_POST['course']);
    $user_id = $_SESSION['user_id'];
    
    // Handle file upload
    $target_dir = "uploads/student_cards/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . basename($_FILES["student_card"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["student_card"]["tmp_name"]);
    if($check === false) {
        $error = "File is not an image.";
        $uploadOk = 0;
    }
    
    // Check file size (5MB max)
    if ($_FILES["student_card"]["size"] > 5000000) {
        $error = "Sorry, your file is too large (max 5MB).";
        $uploadOk = 0;
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["student_card"]["tmp_name"], $target_file)) {
            // Insert application into database
            $sql = "INSERT INTO membership_applications (id, fullname, matricid, course, student_card_path, status, application_date) 
                    VALUES ('$user_id', '$full_name', '$matric_id', '$course', '$target_file', 'pending', NOW())";
            
            if ($db->query($sql)) {
                $success = "Application submitted successfully! It will be reviewed by the Petakom Coordinator.";
            } else {
                $error = "Error submitting application: " . $db->error;
            }
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyPetakom - Membership Application</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        
        .header {
            background-color: #f0f0f0;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            color: #333;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .sign-out {
            background-color: #f0f0f0;
            color: black;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        
        .sign-out:hover {
            background-color: #c82333;
        }
        
        .container {
            display: flex;
            flex: 1;
        }
        
        .sidebar {
            background-color: #f0f0f0;
            width: 250px;
            padding: 20px 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sidebar nav ul {
            list-style: none;
        }
        
        .sidebar nav ul li {
            padding: 10px 20px;
        }
        
        .sidebar nav ul li a {
            text-decoration: none;
            color: #333;
            display: block;
            padding: 8px 10px;
            border-radius: 4px;
        }
        
        .sidebar nav ul li a:hover {
            background-color: #e0e0e0;
        }
        
        .sidebar nav ul li a.active {
            background-color: #007bff;
            color: white;
        }
        
        .main-content {
            flex: 1;
            padding: 30px;
            background-color: #fff;
        }
        
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .form-group input[type="text"],
        .form-group input[type="file"],
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .submit-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .submit-btn:hover {
            background-color: #218838;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            background-color: white;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 1em;
        }

        .form-group select:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        .note {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MyPetakom</h1>
        <div class="user-info">
            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="login.php" class="sign-out">Sign Out</a>
        </div>
    </div>
    
    <div class="container">
        <div class="sidebar">
            <img src="petakom_logo.png" alt="Petakom's Logo" width="120px" height="auto">
            <nav>
                <ul>
                    <li><a href="udashboard.php">Dashboard</a></li>
                    <li><a href="profile_management.php">Profile Management</a></li>
                    <li><a href="membership_application.php" class="active">Membership Application</a></li>
                    <li><a href="event_list.php">Event List</a></li>
                    <li><a href="event_attendance.php">Event Attendance</a></li>
                </ul>
            </nav>
        </div>
        
        <div class="main-content">
            <div class="form-container">
                <h2>Membership Application</h2>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form action="membership_application.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="matric_id">Matric ID</label>
                        <input type="text" id="matric_id" name="matric_id" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="course">Course</label>
                        <select id="course" name="course" required>
                        <option value="">-- Select Course --</option>
                        <option value="BCS" <?php echo (isset($user['course']) && $user['course'] == 'BCS' ? 'selected' : ''); ?>>BCS</option>
                        <option value="BCN" <?php echo (isset($user['course']) && $user['course'] == 'BCN' ? 'selected' : ''); ?>>BCN</option>
                        <option value="BCG" <?php echo (isset($user['course']) && $user['course'] == 'BCG' ? 'selected' : ''); ?>>BCG</option>
                        <option value="BCY" <?php echo (isset($user['course']) && $user['course'] == 'BCY' ? 'selected' : ''); ?>>BCY</option>
                        <option value="DRC" <?php echo (isset($user['course']) && $user['course'] == 'DRC' ? 'selected' : ''); ?>>DRC</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="student_card">Student Card (Image)</label>
                        <input type="file" id="student_card" name="student_card" accept="image/*" required>
                        <p class="note">Please upload a clear image of your student card for verification.</p>
                    </div>
                    
                    <button type="submit" class="submit-btn">Submit Application</button>
                </form>
                
                <div style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 4px;">
                    <h3>Application Guidelines</h3>
                    <ul style="margin-top: 10px; margin-left: 20px;">
                        <li>All FK students are eligible to apply for Petakom membership.</li>
                        <li>You must upload a clear copy of your student card for status verification.</li>
                        <li>Your application will be reviewed by the Petakom Coordinator.</li>
                        <li>You will be notified once your application is approved or rejected.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>