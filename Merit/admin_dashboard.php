<?php
require_once 'db.php';
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['UserID']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch counts for claim statuses
$stmt = $pdo->prepare("SELECT Claim_Status, COUNT(*) AS count FROM merit_claim GROUP BY Claim_Status");
$stmt->execute();
$claimCounts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$pendingClaimsCount = $claimCounts['Pending'] ?? 0;
$submittedClaimsCount = $claimCounts['Submitted'] ?? 0;
$approvedClaimsCount = $claimCounts['Approved'] ?? 0;
$rejectedClaimsCount = $claimCounts['Rejected'] ?? 0;

// Fetch total number of members
$stmt = $pdo->query("SELECT COUNT(*) FROM user WHERE Role != 'admin'");
$totalMembers = $stmt->fetchColumn();

// Fetch total number of events (assuming each approved claim is an event)
$stmt = $pdo->query("SELECT COUNT(*) FROM merit_claim WHERE Claim_Status = 'Approved'");
$totalEvents = $stmt->fetchColumn();

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
 .dashboard-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.summary-card {
    background-color: #f3f4f6;
    padding: 1.5rem;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.summary-value {
    font-size: 2rem;
    font-weight: bold;
    color: #1e293b;
}

.summary-label {
    font-size: 1rem;
    color: #4b5563;
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
    <a href="reward_merit.php">Reward Merit</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>
<main>
    <h1>Admin Dashboard</h1>
     <section class="dashboard-summary">
        <div class="summary-card">
            <div class="summary-value"><?= $pendingClaimsCount + $submittedClaimsCount ?></div>
            <div class="summary-label">Claims Awaiting Approval</div>
            <a href="manage_merit.php">Review Claims</a>
        </div>
        <div class="summary-card">
            <div class="summary-value"><?= $approvedClaimsCount ?></div>
            <div class="summary-label">Approved Claims</div>
        </div>
        <div class="summary-card">
            <div class="summary-value"><?= $rejectedClaimsCount ?></div>
            <div class="summary-label">Rejected Claims</div>
        </div>
         <div class="summary-card">
            <div class="summary-value"><?= $totalMembers ?></div>
            <div class="summary-label">Total Members</div>
        </div>
        <div class="summary-card">
            <div class="summary-value"><?= $totalEvents ?></div>
            <div class="summary-label">Total Events Registered</div>
        </div>
    </section>
</main>
</body>
</html>
