<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['UserID']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Approve or reject a claim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        $claimID = $_POST['claimID'];
        try {
            $pdo->beginTransaction();
            // Get claim details
            $stmt = $pdo->prepare("SELECT * FROM merit_claim WHERE ClaimID=?");
            $stmt->execute([$claimID]);
            $claim = $stmt->fetch();
            if (!$claim || $claim['Claim_Status'] !== 'Pending') {
                throw new Exception("Claim not found or already processed.");
            }
            // Calculate merit points
            $meritScores = [
                'International' => ['Main Committee' => 100, 'Committee' => 70, 'Participant' => 50],
                'National' => ['Main Committee' => 80, 'Committee' => 50, 'Participant' => 40],
                'State' => ['Main Committee' => 60, 'Committee' => 40, 'Participant' => 30],
                'District' => ['Main Committee' => 40, 'Committee' => 30, 'Participant' => 15],
                'UMPSA' => ['Main Committee' => 30, 'Committee' => 20, 'Participant' => 5],
            ];
            $points = $meritScores[$claim['Level']][$claim['Position']] ?? 0;
            // Insert merit into merit table
            $stmtInsert = $pdo->prepare("INSERT INTO merit (UserID, EventName, Date, Organizer, Position, Level, Marks) VALUES (?,?,?,?,?,?,?)");
            $stmtInsert->execute([
                $claim['UserID'],
                $claim['EventName'],
                $claim['EventDate'],
                $claim['Organizer'],
                $claim['Position'],
                $claim['Level'],
                $points,
            ]);
            // Update claim status to Approved
            $stmtUpdate = $pdo->prepare("UPDATE merit_claim SET Claim_Status='Approved' WHERE ClaimID=?");
            $stmtUpdate->execute([$claimID]);
            $pdo->commit();
            $success = 'Claim approved and merit awarded.';
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Error approving claim: ' . $e->getMessage();
        }
    } elseif (isset($_POST['reject'])) {
        $claimID = $_POST['claimID'];
        $stmt = $pdo->prepare("UPDATE merit_claim SET Claim_Status='Rejected' WHERE ClaimID=?");
        $stmt->execute([$claimID]);
        $success = 'Claim rejected.';
    }
}

// Fetch pending and submitted claims for admin review
$stmt = $pdo->query("SELECT mc.*, u.Name, u.MatricID FROM merit_claim mc JOIN user u ON mc.UserID = u.UserID WHERE mc.Claim_Status IN ('Pending','Submitted') ORDER BY mc.Submitted_Date DESC");
$claims = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Manage Merit Claims - MyPetakom</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
  body {
    font-family: 'Poppins', sans-serif;
    background:#fff;
    color:#4b5563;
    max-width:1200px;
    margin:2rem auto;
    padding: 0 1rem;
  }
  header {
    position: sticky; top:0;
    background:#fff;
    padding:1rem 2rem;
    display:flex; justify-content:space-between; align-items:center;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    border-radius: 0.75rem;
    margin-bottom: 2rem;
  }
  .logo {
    font-weight:700;
    font-size:1.5rem;
    color:#111827;
  }
  nav a {
    margin-left:1.5rem;
    font-weight:600;
    font-size:0.95rem;
    color:#111827;
    text-decoration:none;
  }
  nav a:hover {
    color:#2563eb;
  }
  h1 {
    font-weight:700;
    font-size:2.5rem;
    margin-bottom:1rem;
    color:#111827;
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
  }
  button {
    background: #111827;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  button.reject {
    background: #dc2626;
  }
  button:hover {
    opacity: 0.9;
  }
</style>
</head>
<body>
<header>
  <div class="logo">MyPetakom</div>
  <nav>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="reward_merit.php" aria-current="page">Reward Merit</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>
<main>
<h1>Manage Merit Claims</h1>
<?php if ($error): ?>
  <p class="message error" role="alert"><?=htmlspecialchars($error)?></p>
<?php endif; ?>
<?php if ($success): ?>
  <p class="message success" role="alert"><?=htmlspecialchars($success)?></p>
<?php endif; ?>

<?php if (count($claims) === 0): ?>
  <p>No claims to review at the moment.</p>
<?php else: ?>
<table aria-label="Merit claims for approval">
<thead>
<tr>
  <th>Claim ID</th><th>Student</th><th>Matric ID</th><th>Event</th><th>Date</th><th>Organizer</th><th>Position</th><th>Level</th><th>Status</th><th>Document</th><th>Actions</th>
</tr>
</thead>
<tbody>
<?php foreach ($claims as $claim): ?>
<tr>
  <td><?=htmlspecialchars($claim['ClaimID'])?></td>
  <td><?=htmlspecialchars($claim['Name'])?></td>
  <td><?=htmlspecialchars($claim['MatricID'])?></td>
  <td><?=htmlspecialchars($claim['EventName'])?></td>
  <td><?=htmlspecialchars($claim['EventDate'])?></td>
  <td><?=htmlspecialchars($claim['Organizer'])?></td>
  <td><?=htmlspecialchars($claim['Position'])?></td>
  <td><?=htmlspecialchars($claim['Level'])?></td>
  <td><?=htmlspecialchars($claim['Claim_Status'])?></td>
  <td><a href="<?=htmlspecialchars($claim['Supporting_Doc'])?>" target="_blank" rel="noopener noreferrer">View</a></td>
  <td class="actions">
    <?php if($claim['Claim_Status'] === 'Pending'): ?>
      <form method="post" style="display:inline;">
        <input type="hidden" name="claimID" value="<?=htmlspecialchars($claim['ClaimID'])?>" />
        <button type="submit" name="approve">Approve</button>
      </form>
      <form method="post" style="display:inline;">
        <input type="hidden" name="claimID" value="<?=htmlspecialchars($claim['ClaimID'])?>" />
        <button type="submit" name="reject" class="reject">Reject</button>
      </form>
    <?php else: ?>
      <em><?=$claim['Claim_Status']?></em>
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
