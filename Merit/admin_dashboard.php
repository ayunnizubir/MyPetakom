<?php
require_once 'db.php';
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['UserID']) || $_SESSION['role'] !== 'admin') {
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
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
  body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #fff;
    color: #4b5563;
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
  }
  header {
    position: sticky;
    top: 0;
    background: #fff;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
    border-radius: 0.75rem;
  }
  .logo {
    font-weight: 700;
    font-size: 1.5rem;
    color: #111827;
  }
  nav a {
    margin-left: 1.5rem;
    font-weight: 600;
    font-size: 0.95rem;
    color: #111827;
    text-decoration: none;
  }
  nav a:hover {
    color: #2563eb;
  }
  main h1 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: #111827;
  }
  main p.lead {
    font-size: 1.125rem;
    margin-bottom: 3rem;
  }
  .metrics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
  }
  .card {
    background: #f9fafb;
    border-radius: 0.75rem;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .metric-value {
    font-size: 3rem;
    font-weight: 800;
    color: #111827;
    margin-bottom: 0.25rem;
  }
  .metric-label {
    font-weight: 600;
    color: #9ca3af;
  }
  .qr-container {
    margin-top: 1rem;
    text-align: center;
  }
  .qr-container img {
    margin-top: 1rem;
    width: 160px;
    height: 160px;
    border-radius: 0.75rem;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
  }
  main h2 {
    font-weight: 700;
    font-size: 1.75rem;
    color: #111827;
    margin-bottom: 1rem;
  }
  footer {
    text-align: center;
    color: #9ca3af;
    font-size: 0.9rem;
    margin-top: 6rem;
    padding-bottom: 2rem;
  }
    </style>
</head>
<body>
<header>
  <div class="logo">MyPetakom</div>
  <nav>
    <a href="admin_dashboard.php">Dashboard</a>
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
            <div class="container">
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
            </div>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</main>
</body>
</html>
