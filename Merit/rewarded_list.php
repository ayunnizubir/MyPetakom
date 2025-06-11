<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db.php';

// Check admin login
if (!isset($_SESSION['UserID']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch all rewarded merit claims
$stmt = $pdo->prepare("
    SELECT mc.*, u.MatricID, u.Name 
    FROM merit_claim mc 
    JOIN user u ON mc.UserID = u.UserID 
    WHERE mc.Claim_Status = 'Rewarded' 
    ORDER BY mc.EventDate DESC
");
$stmt->execute();
$rewardedClaims = $stmt->fetchAll();

// Display success message if set
$successMessage = '';
if (isset($_SESSION['success'])) {
    $successMessage = $_SESSION['success'];
    unset($_SESSION['success']); // Clear the message after displaying it
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Rewarded Merits - MyPetakom</title>
  <link rel="stylesheet" href="style.css">
  <style>
  body {
    font-family: 'Poppins', sans-serif;
    background: #fff;
    color: #4b5563;
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
  }
  header {
    position: sticky; top: 0;
    background: #fff;
    padding: 1rem 2rem;
    display: flex; justify-content: space-between; align-items: center;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
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
  nav a:hover { color: #2563eb; }
  h1 {
    font-weight: 700;
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: #111827;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 2rem;
  }
  th, td {
    padding: 0.75rem 1rem;
    text-align: left;
    border-bottom: 1px solid #ddd;
  }
  th { background-color: #f1f5f9; }
  a.view-doc { color: #2563eb; text-decoration: underline; }
  </style>
</head>
<body>

<header>
  <div class="logo">MyPetakom</div>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="reward_merit.php">Reward Merit</a>
    <a href="manage_merit_list.php">Manage Merit</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>
  <h1>List of Rewarded Merits</h1>

  <?php if (count($rewardedClaims) === 0): ?>
    <p>No rewarded merit found.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Student ID</th>
          <th>Name</th>
          <th>Event</th>
          <th>Date</th>
          <th>Organizer</th>
          <th>Position</th>
          <th>Level</th>
          <th>Supporting Document</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rewardedClaims as $claim): ?>
        <tr>
          <td><?= htmlspecialchars($claim['MatricID']) ?></td>
          <td><?= htmlspecialchars($claim['Name']) ?></td>
          <td><?= htmlspecialchars($claim['EventName']) ?></td>
          <td><?= htmlspecialchars($claim['EventDate']) ?></td>
          <td><?= htmlspecialchars($claim['Organizer']) ?></td>
          <td><?= htmlspecialchars($claim['Position']) ?></td>
          <td><?= htmlspecialchars($claim['Level']) ?></td>
          <td>
            <?php if (!empty($claim['Supporting_Doc']) && file_exists($claim['Supporting_Doc'])): ?>
              <a class="view-doc" href="<?= htmlspecialchars($claim['Supporting_Doc']) ?>" target="_blank">View</a>
            <?php else: ?>
              No document
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
