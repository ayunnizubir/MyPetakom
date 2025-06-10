<?php
require_once 'db.php';
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['UserID']) || $_SESSION['Role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        // Approve claim
        $claimID = $_POST['claimID'];
        header("Location: approve_claim.php?id=$claimID");
        exit;
    } elseif (isset($_POST['reject'])) {
        // Reject claim
        $claimID = $_POST['claimID'];
        header("Location: reject_claim.php?id=$claimID");
        exit;
    }
}

// Fetch all pending and submitted claims for admin review
$stmt = $pdo->query("SELECT mc.*, u.Name, u.MatricID FROM merit_claim mc JOIN user u ON mc.UserID = u.UserID WHERE mc.Claim_Status IN ('Pending','Submitted') ORDER BY mc.Submitted_Date DESC");
$claims = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard - MyPetakom</title>
    <style>
        /* Your existing CSS styles */
    </style>
</head>
<body>
<header>
  <div class="logo">MyPetakom</div>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_merit.php">Manage Merit</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>
<main>
    <h1>Manage Merit Claims</h1>

    <?php if (count($claims) === 0): ?>
      <p>No claims to review at the moment.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Claim ID</th><th>Student</th><th>Matric ID</th><th>Event</th><th>Organizer</th><th>Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($claims as $claim): ?>
            <tr>
                <td><?= htmlspecialchars($claim['ClaimID']) ?></td>
                <td><?= htmlspecialchars($claim['Name']) ?></td>
                <td><?= htmlspecialchars($claim['MatricID']) ?></td>
                <td><?= htmlspecialchars($claim['EventName']) ?></td>
                <td><?= htmlspecialchars($claim['Organizer']) ?></td>
                <td><?= htmlspecialchars($claim['Claim_Status']) ?></td>
                <td>
                    <?php if ($claim['Claim_Status'] === 'Submitted' || $claim['Claim_Status'] === 'Pending'): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="claimID" value="<?= htmlspecialchars($claim['ClaimID']) ?>" />
                            <button type="submit" name="approve">Approve</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="claimID" value="<?= htmlspecialchars($claim['ClaimID']) ?>" />
                            <button type="submit" name="reject" style="background-color: red;">Reject</button>
                        </form>
                    <?php else: ?>
                        <em>Processed</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</main>
</body>
</html>
