<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit;
}

$userID = $_SESSION['UserID'];
$claimID = $_GET['id'] ?? null;

// Fetch the claim
$stmt = $pdo->prepare("SELECT * FROM merit_claim WHERE ClaimID = ? AND UserID = ?");
$stmt->execute([$claimID, $userID]);
$claim = $stmt->fetch();

if (!$claim) {
    die("Invalid Claim ID or permission denied.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>View Merit - MyPetakom</title>
  <link rel="stylesheet" href="style.css" />
  <style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
  body {
    font-family:'Poppins', sans-serif;
    background:#fff;
    color:#4b5563;
    max-width:1200px;
    margin: 2rem auto;
    padding: 0 1rem;
  }
  header {
    position: sticky; top:0;
    background:#fff;
    padding:1rem 2rem;
    display: flex; justify-content: space-between; align-items: center;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    border-radius: 0.75rem;
    margin-bottom: 2rem;
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
  h1 {
    font-weight: 700;
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: #111827;
  }
  .card {
    background: #f9fafb;
    padding: 1.5rem 2rem;
    border-radius: 0.75rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 3rem;
  }
  label {
    font-weight: 600;
    display: block;
    margin-bottom: 0.5rem;
    color: #111827;
  }
  .button {
    background: #111827;
    color: white;
    border: none;
    padding: 0.75rem 1.25rem;
    border-radius: 0.5rem;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  .button:hover {
    background: #374151;
  }
  </style>
</head>
<body>
<header>
  <div class="logo">MyPetakom</div>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="view_claimed.php">View Claimed Merit</a>
    <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="manage_merit.php">Manage Merit</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>
<h1>View Merit Details</h1>

<div class="card">
  <label for="eventName">Event Name</label>
  <p><?= htmlspecialchars($claim['EventName']) ?></p>

  <label for="eventDate">Event Date</label>
  <p><?= htmlspecialchars($claim['EventDate']) ?></p>

  <label for="organizer">Organizer</label>
  <p><?= htmlspecialchars($claim['Organizer']) ?></p>

  <label for="position">Position</label>
  <p><?= htmlspecialchars($claim['Position']) ?></p>

  <label for="level">Event Level</label>
  <p><?= htmlspecialchars($claim['Level']) ?></p>

  <label for="supportingDoc">Supporting Document</label>
  <?php if ($claim['Supporting_Doc']): ?>
    <p><a href="<?= htmlspecialchars($claim['Supporting_Doc']) ?>" target="_blank">View Uploaded Document</a></p>
  <?php else: ?>
    <p>No document uploaded.</p>
  <?php endif; ?>
</div>

<a href="view_claimed.php" class="button">OK</a>
</main>
</body>
</html>
