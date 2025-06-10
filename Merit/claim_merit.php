<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit;
}

$userID = $_SESSION['UserID'];
$name = $_SESSION['Name'];

$error = '';
$success = '';

function calculateMerit($level, $position) {
    $scores = [
        'International' => ['Main Committee' => 100, 'Committee' => 70, 'Participant' => 50],
        'National' => ['Main Committee' => 80, 'Committee' => 50, 'Participant' => 40],
        'State' => ['Main Committee' => 60, 'Committee' => 40, 'Participant' => 30],
        'District' => ['Main Committee' => 40, 'Committee' => 30, 'Participant' => 15],
        'UMPSA' => ['Main Committee' => 30, 'Committee' => 20, 'Participant' => 5],
    ];
    return $scores[$level][$position] ?? 0;
}

$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_claim'])) {
    $eventName = trim($_POST['eventName'] ?? '');
    $eventDate = $_POST['eventDate'] ?? '';
    $organizer = trim($_POST['organizer'] ?? '');
    $position = $_POST['position'] ?? '';
    $level = $_POST['level'] ?? '';

    if (!$eventName || !$eventDate || !$organizer || !$position || !$level) {
        $error = 'Please fill in all fields.';
    } elseif (!isset($_FILES['supportingDoc']) || $_FILES['supportingDoc']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload a supporting document.';
    } else {
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        $fileType = $_FILES['supportingDoc']['type'];
        if (!in_array($fileType, $allowedTypes)) {
            $error = 'Invalid file type. PDF, JPG, PNG allowed.';
        } else {
            $fileName = uniqid() . '_' . basename($_FILES['supportingDoc']['name']);
            $filePath = $uploadDir . $fileName;
            if (!move_uploaded_file($_FILES['supportingDoc']['tmp_name'], $filePath)) {
                $error = 'Failed to upload file.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO merit_claim 
                  (UserID, EventName, EventDate, Organizer, Position, Level, Supporting_Doc, Claim_Status)
                  VALUES (?,?,?,?,?,?,?, 'Pending')");
                $stmt->execute([$userID, $eventName, $eventDate, $organizer, $position, $level, $filePath]);
                $success = "Claim submitted and pending approval.";
            }
        }
    }
}

if (isset($_POST['update_claim'])) {
    $claimID = $_POST['claimID'];
    $stmtCheck = $pdo->prepare("SELECT Claim_Status FROM merit_claim WHERE ClaimID=? AND UserID=?");
    $stmtCheck->execute([$claimID, $userID]);
    $status = $stmtCheck->fetchColumn();
    if ($status !== 'Submitted') {
        $eventName = trim($_POST['eventName']);
        $eventDate = $_POST['eventDate'];
        $organizer = trim($_POST['organizer']);
        $position = $_POST['position'];
        $level = $_POST['level'];
        if (!$eventName || !$eventDate || !$organizer || !$position || !$level) {
            $error = 'Fill all fields for update.';
        } else {
            $filePath = null;
            if (isset($_FILES['supportingDoc']) && $_FILES['supportingDoc']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
                $fileType = $_FILES['supportingDoc']['type'];
                if (!in_array($fileType, $allowedTypes)) {
                    $error = 'Invalid file type.';
                } else {
                    $fileName = uniqid().'_'.basename($_FILES['supportingDoc']['name']);
                    $filePath = $uploadDir . $fileName;
                    if (!move_uploaded_file($_FILES['supportingDoc']['tmp_name'], $filePath)) {
                        $error = 'Upload failed.';
                    }
                }
            }
            if (!$error) {
                if ($filePath) {
                    $stmt = $pdo->prepare("UPDATE merit_claim SET EventName=?, EventDate=?, Organizer=?, Position=?, Level=?, Supporting_Doc=?, Claim_Status='Pending' WHERE ClaimID=? AND UserID=?");
                    $stmt->execute([$eventName, $eventDate, $organizer, $position, $level, $filePath, $claimID, $userID]);
                } else {
                    $stmt = $pdo->prepare("UPDATE merit_claim SET EventName=?, EventDate=?, Organizer=?, Position=?, Level=?, Claim_Status='Pending' WHERE ClaimID=? AND UserID=?");
                    $stmt->execute([$eventName, $eventDate, $organizer, $position, $level, $claimID, $userID]);
                }
                $success = "Claim updated successfully.";
            }
        }
    } else {
        $error = "Cannot update a submitted claim.";
    }
}

if (isset($_POST['delete_claim'])) {
    $claimID = $_POST['claimID'];
    $stmtCheck = $pdo->prepare("SELECT Claim_Status FROM merit_claim WHERE ClaimID=? AND UserID=?");
    $stmtCheck->execute([$claimID, $userID]);
    $status = $stmtCheck->fetchColumn();
    if ($status !== 'Submitted') {
        $stmt = $pdo->prepare("DELETE FROM merit_claim WHERE ClaimID=? AND UserID=?");
        $stmt->execute([$claimID, $userID]);
        $success = "Claim deleted.";
    } else {
        $error = "Cannot delete a submitted claim.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM merit_claim WHERE UserID=? ORDER BY Submitted_Date DESC");
$stmt->execute([$userID]);
$claims = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Claim Missing Merit - MyPetakom</title>
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
  }
</style>
</head>
<body>
<header>
  <div class="logo">MyPetakom</div>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="claim_merit.php" aria-current="page">Claim Merit</a>
    <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="manage_merit.php">Manage Merit</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
  </nav>
</header>
<main>
<h1>Claim Missing Merit</h1>
<?php if ($error): ?>
  <p class="message error" role="alert"><?=htmlspecialchars($error)?></p>
<?php endif; ?>
<?php if ($success): ?>
  <p class="message success" role="alert"><?=htmlspecialchars($success)?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="card" aria-label="Submit merit claim">
  <label for="eventName">Event Name</label>
  <input type="text" name="eventName" id="eventName" required />

  <label for="eventDate">Event Date</label>
  <input type="date" name="eventDate" id="eventDate" required />

  <label for="organizer">Organizer</label>
  <input type="text" name="organizer" id="organizer" required />

  <label for="position">Position</label>
  <select id="position" name="position" required>
    <option value="" disabled selected>Select position</option>
    <option value="Main Committee">Main Committee</option>
    <option value="Committee">Committee</option>
    <option value="Participant">Participant</option>
  </select>

  <label for="level">Event Level</label>
  <select id="level" name="level" required>
    <option value="" disabled selected>Select level</option>
    <option value="International">International</option>
    <option value="National">National</option>
    <option value="State">State</option>
    <option value="District">District</option>
    <option value="UMPSA">UMPSA</option>
  </select>

  <label for="supportingDoc">Supporting Document (PDF, JPG, PNG)</label>
  <input type="file" id="supportingDoc" name="supportingDoc" accept=".pdf,image/jpeg,image/png" required />

  <button type="submit" name="submit_claim">Submit Claim</button>
</form>

<h2>Your Claims</h2>
<?php if (count($claims) === 0): ?>
  <p>No merit claims submitted yet.</p>
<?php else: ?>
<table aria-label="List of your merit claims">
<thead>
  <tr>
    <th>Event</th><th>Date</th><th>Organizer</th><th>Position</th><th>Level</th><th>Status</th><th>Document</th><th>Actions</th>
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
  <td data-label="Status"><span class="badge <?=htmlspecialchars($c['Claim_Status'])?>"><?=htmlspecialchars($c['Claim_Status'])?></span></td>
  <td data-label="Doc"><a href="<?=htmlspecialchars($c['Supporting_Doc'])?>" target="_blank" rel="noopener noreferrer">View</a></td>
  <td class="actions" data-label="Actions">
  <?php if ($c['Claim_Status'] !== 'Submitted'): ?>
    <form method="post" enctype="multipart/form-data" class="inline-form" aria-label="Edit claim #<?=$c['ClaimID']?>">
      <input type="hidden" name="claimID" value="<?=$c['ClaimID']?>" />
      <input class="inline-input" type="text" name="eventName" value="<?=htmlspecialchars($c['EventName'])?>" required />
      <input class="inline-input" type="date" name="eventDate" value="<?=htmlspecialchars($c['EventDate'])?>" required />
      <input class="inline-input" type="text" name="organizer" value="<?=htmlspecialchars($c['Organizer'])?>" required />
      <select class="inline-select" name="position" required>
        <?php foreach(['Main Committee','Committee','Participant'] as $pos): ?>
          <option value="<?=$pos?>" <?php if($pos === $c['Position']) echo 'selected'?>><?=$pos?></option>
        <?php endforeach;?>
      </select>
      <select class="inline-select" name="level" required>
        <?php foreach(['International','National','State','District','UMPSA'] as $lvl): ?>
          <option value="<?=$lvl?>" <?php if($lvl === $c['Level']) echo 'selected'?>><?=$lvl?></option>
        <?php endforeach;?>
      </select>
      <input class="inline-input" type="file" name="supportingDoc" accept=".pdf,image/jpeg,image/png" />
      <button type="submit" name="update_claim">Update</button>
    </form>
    <form method="post" onsubmit="return confirm('Delete this claim?');" style="display:inline-block;" aria-label="Delete claim #<?=$c['ClaimID']?>">
      <input type="hidden" name="claimID" value="<?=$c['ClaimID']?>" />
      <button type="submit" name="delete_claim" style="background:#dc2626;">Delete</button>
    </form>
  <?php else: ?>
    <em>Locked</em>
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
