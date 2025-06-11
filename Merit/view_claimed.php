<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit;
}

$userID = $_SESSION['UserID'];
$name = $_SESSION['Name'];

$stmt = $pdo->prepare("SELECT * FROM merit_claim WHERE UserID=? ORDER BY Submitted_Date DESC");
$stmt->execute([$userID]);
$claims = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Claims - MyPetakom</title>
<link rel="stylesheet" href="style.css">
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
  form.card {
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
  input[type=text], input[type=date], select, input[type=file] {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    border: 1px solid #d1d5db;
    margin-bottom: 1rem;
    font-size: 1rem;
    color: #4b5563;
  }
  button {
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
  button:hover {
    background: #374151;
  }
  .message {
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 0.75rem;
    font-weight: 600;
  }
  .error { background: #fee2e2; color: #b91c1c; }
  .success { background: #d1fae5; color: #065f46; }
  table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 0.5rem;
  }
  th, td {
    padding: 0.75rem 1rem;
    text-align: left;
  }
  th {
    font-weight: 700;
    font-size: 1rem;
    color: #111827;
  }
  td {
    background: #f9fafb;
    border-radius: 0.75rem;
    vertical-align: middle;
  }
  td.actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
  }
  .badge {
    padding: 0.3rem 0.6rem;
    border-radius: 9999px;
    font-weight: 700;
    font-size: 0.85rem;
    color: white;
    display: inline-block;
  }
  .badge.Pending { background-color: #f97316; }
  .badge.Submitted { background-color: #2563eb; }
  .badge.Approved { background-color: #16a34a; }
  .badge.Rejected { background-color: #dc2626; }
  form.inline-form {
    display: inline-flex;
    gap: 0.3rem;
    align-items: center;
    flex-wrap: wrap;
  }
  input.inline-input {
    width: 110px;
    font-size: 0.9rem;
    padding: 0.3rem 0.5rem;
  }
  select.inline-select {
    font-size: 0.9rem;
    padding: 0.3rem 0.5rem;
  }
  @media (max-width: 768px) {
    table, thead, tbody, th, td, tr {
      display: block;
    }
    thead tr {
      display: none;
    }
    tr {
      margin-bottom: 1rem;
      border-radius: 0.75rem;
      background: #f9fafb;
      padding: 1rem;
    }
    td {
      text-align: right;
      position: relative;
      padding-left: 50%;
    }
    td::before {
      content: attr(data-label);
      position: absolute;
      left: 1rem;
      top: 0.75rem;
      color: #111827;
      font-weight: 600;
    }
    td.actions {
      justify-content: center;
      text-align: center;
    }
    .badge.Awarded { 
      background-color: #0d9488; } /* teal or custom color */
  }
</style>
</head>
<body>
<header>
  <div class="logo">MyPetakom</div>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="claim_merit.php">Claim Merit</a>
    <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="manage_merit.php">Manage Merit</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
  </nav>
</header>
<main>
  <?php if (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
  <div class="message success" id="successMessage">Claim successfully deleted.</div>
  <script>
    // Auto hide after 3 seconds
    setTimeout(function() {
      document.getElementById('successMessage').style.display = 'none';
    }, 3000);
  </script>
  <?php endif; ?>
  <h1>Claimed Merits</h1>

  <?php if (count($claims) === 0): ?>
    <p>No merit claims submitted yet.</p>
  <?php else: ?>
  <table aria-label="List of your merit claims">
  <thead>
    <tr>
      <th>Event</th>
      <th>Date</th>
      <th>Organizer</th>
      <th>Position</th>
      <th>Level</th>
      <th>Status</th>
      <th>Document</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($claims as $c): ?>
  <tr>
    <td data-label="Event"><?=htmlspecialchars($c['EventName'])?></td>
    <td data-label="Date"><?=htmlspecialchars($c['EventDate'])?></td>
    <td data-label="Organizer"><?=htmlspecialchars($c['Organizer'])?></td>
    <td data-label="Position"><?=htmlspecialchars($c['Position'])?></td>
    <td data-label="Level"><?=htmlspecialchars($c['Level'])?></td>
    <td data-label="Status">
  <?php 
    $status = ucfirst(strtolower($c['Claim_Status']));
    $badgeClass = in_array($status, ['Pending', 'Submitted', 'Approved', 'Rejected', 'Awarded']) ? $status : 'Pending';
  ?>
  <span class="badge <?=htmlspecialchars($badgeClass)?>">
    <?=htmlspecialchars($status)?>
  </span>
</td>
<td data-label="Document">
        <?php if (!empty($c['Supporting_Doc']) && file_exists($c['Supporting_Doc'])): ?>
          <a href="<?=htmlspecialchars($c['Supporting_Doc'])?>" target="_blank" rel="noopener noreferrer">View</a>
        <?php else: ?>
          <span style="color:#9ca3af;">No document</span>
        <?php endif; ?>
      </td>
      <td data-label="Action">
        <?php if ($status === 'Pending'): ?>
          <a href="update_claim.php?id=<?=urlencode($c['ClaimID'])?>" style="margin-right:0.5rem;">Update</a>
          <a href="view_form.php?id=<?=urlencode($c['ClaimID'])?>" style="margin-right:0.5rem;">View</a>
          <a href="delete_claim.php?id=<?=urlencode($c['ClaimID'])?>" onclick="return confirm('Are you sure you want to delete this claim?');" style="color:red;">Delete</a>
        <?php else: ?>
          <span style="color:#9ca3af;">No actions</span>
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
