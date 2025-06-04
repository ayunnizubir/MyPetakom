<?php
// confirm_delete.php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    header("Location: profile_management.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Deletion</title>
    <style>
        .confirmation-box {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .confirmation-buttons {
            margin-top: 20px;
        }
        .confirmation-buttons a {
            padding: 10px 20px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 5px;
        }
        .confirm-btn {
            background: #dc3545;
            color: white;
        }
        .cancel-btn {
            background: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="confirmation-box">
        <h2>⚠️ Delete Your Account?</h2>
        <p>Are you sure you want to permanently delete your profile? This action cannot be undone!</p>
        
        <div class="confirmation-buttons">
            <a href="delete.php?id=<?php echo $user_id; ?>" class="confirm-btn">Yes, Delete</a>
            <a href="profile_management.php" class="cancel-btn">No, Cancel</a>
        </div>
    </div>
</body>
</html>