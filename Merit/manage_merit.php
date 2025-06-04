<?php
require_once 'db.php';
$sql = "SELECT mc.ClaimID, u.StudentID, m.MeritID, m.Level, mc.Submitted_Date, mc.Supporting_Doc, mc.Claim_Status 
        FROM merit_claim mc
        JOIN user u ON mc.UserID = u.UserID
        JOIN merit m ON mc.MeritID = m.MeritID
        ORDER BY mc.ClaimID DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Merit Claims</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="sidebar">
    <img src="css/petakom_logo.png" alt="PETAKOM Logo">
        <h2>MyPetakom</h2>
        <ul>
            <b> Student</a></b>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="claim_merit.php">Claim Merit</a></li>
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
        <h2>All Merit Claims</h2>
        <table border="1" width="100%" cellpadding="10" style="background:white;">
            <tr style="background:#eee;">
             <th>Claim ID</th>
                <th>Student ID</th>
             <th>Merit ID</th>
             <th>Level</th>
             <th>Date Submitted</th>
             <th>Document</th>
             <th>Status</th>
              <th>Action</th>
         </tr>
         <?php while ($row = $result->fetch_assoc()): ?>
         <tr>
            <td><?php echo $row['ClaimID']; ?></td>
            <td><?php echo $row['StudentID']; ?></td>
            <td><?php echo $row['MeritID']; ?></td>
            <td><?php echo $row['Level']; ?></td>
            <td><?php echo $row['Submitted_Date']; ?></td>
            <td><a href="upload/<?php echo $row['Supporting_Doc']; ?>" target="_blank">View</a></td>
            <td><?php echo $row['Claim_Status']; ?></td>
            <td>
                <a href="update_status.php?id=<?php echo $row['ClaimID']; ?>&status=Approved">Approve</a> |
                <a href="update_status.php?id=<?php echo $row['ClaimID']; ?>&status=Rejected">Reject</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    </div>
</div>
</body>
</html>