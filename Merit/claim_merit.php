<?php 
session_start(); 
require_once 'db.php'; 

// Handle form submission for claiming merit
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $merit_id = $_POST['merit_id'];
    $submitted_date = date('Y-m-d'); // Use Y-m-d for date format
    $supporting_doc = $_FILES['supporting_doc']['name'];

    $target_dir = "upload/";
    $target_file = $target_dir . basename($supporting_doc);
    move_uploaded_file($_FILES['supporting_doc']['tmp_name'], $target_file);

    $sql = "INSERT INTO merit_claim (Claim_Status, Submitted_Date, Supporting_Doc, UserID, MeritID) 
            VALUES ('Pending', '$submitted_date', '$supporting_doc', '$user_id', '$merit_id')";
    
    $msg = $conn->query($sql) ? "Claim submitted successfully!" : "Error: " . $conn->error;
}

// Fetch existing claims for the user
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
//if ($user_id === null) {
//    die("Error: User is not logged in. Please log in to claim merit.");
//}

$sql_claims = "SELECT * FROM merit_claim WHERE UserID = '$user_id'";
$result_claims = $conn->query($sql_claims);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Claim Missing Merit</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="sidebar">
    <img src="css/petakom_logo.png" alt="PETAKOM Logo">
        <h2>MyPetakom</h2>
        <ul>
            <b> Student</a></b>
            <li><a href="manage_merit.php">Manage Merit</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="header">
            <h1>Merit Dashboard</h1>
            <div class="profile">
                <div class="profile-icon">👤</div>
                <span>User's Name</span>
                <button>Sign Out</button>
            </div>
        </div>

<div class="form-container">
    <h2>Claim Missing Merit</h2>
    <?php if (!empty($msg)) echo "<p class='msg'>$msg</p>"; ?>
    <form method="POST" enctype="multipart/form-data">
        <label>User ID:</label>
        <input type="text" name="user_id" required>

        <label>Merit ID:</label>
        <input type="text" name="merit_id" required>

        <label>Upload Supporting Document:</label>
        <input type="file" name="supporting_doc" required>

        <button type="submit">Submit</button> 
    </form>

    <br>

    <h3>Your Claims</h3>
    <table border="1" width="100%" cellpadding="10" style="background:white;">
        <tr style="background:#eee;">
            <th>Claim ID</th>
            <th>Submitted Date</th>
            <th>Document</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result_claims->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['ClaimID']; ?></td>
            <td><?php echo $row['Submitted_Date']; ?></td>
            <td><a href="upload/<?php echo $row['Supporting_Doc']; ?>" target="_blank">View</a></td>
            <td><?php echo $row['Claim_Status']; ?></td>
            <td>
                <?php if ($row['Claim_Status'] != 'Submitted'): ?>
                    <a href="update_claim.php?id=<?php echo $row['ClaimID']; ?>">Update</a> |
                    <a href="delete_claim.php?id=<?php echo $row['ClaimID']; ?>">Delete</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    </div>
</div>
</body>
</html>
