<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit;
}

$userID = $_SESSION['UserID'];
$claimID = $_GET['id'] ?? null;
$mode = $_GET['mode'] ?? 'edit'; // either 'edit' or 'view'

$error = '';
$success = '';

// Fetch the claim
$stmt = $pdo->prepare("SELECT * FROM merit_claim WHERE ClaimID = ? AND UserID = ?");
$stmt->execute([$claimID, $userID]);
$claim = $stmt->fetch();

if (!$claim) {
    die("Invalid Claim ID or permission denied.");
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_claim'])) {
    $eventName = trim($_POST['eventName']);
    $eventDate = $_POST['eventDate'];
    $organizer = trim($_POST['organizer']);
    $position = $_POST['position'];
    $level = $_POST['level'];
    $filePath = $claim['Supporting_Doc'];

    if (!$eventName || !$eventDate || !$organizer || !$position || !$level) {
        $error = 'Please fill in all fields.';
    } else {
        // Handle file upload if provided
        if (isset($_FILES['supportingDoc']) && $_FILES['supportingDoc']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
            $fileType = $_FILES['supportingDoc']['type'];
            if (!in_array($fileType, $allowedTypes)) {
                $error = 'Invalid file type.';
            } else {
                $fileName = uniqid().'_'.basename($_FILES['supportingDoc']['name']);
                $uploadDir = 'uploads/';
                if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);
                $filePath = $uploadDir . $fileName;
                if (!move_uploaded_file($_FILES['supportingDoc']['tmp_name'], $filePath)) {
                    $error = 'Upload failed.';
                }
            }
        }

        if (!$error) {
            $stmt = $pdo->prepare("UPDATE merit_claim 
                SET EventName=?, EventDate=?, Organizer=?, Position=?, Level=?, Supporting_Doc=?, Claim_Status='pending'
                WHERE ClaimID=? AND UserID=?");
            $stmt->execute([$eventName, $eventDate, $organizer, $position, $level, $filePath, $claimID, $userID]);
            $success = "Claim updated successfully.";
            // Refresh claim data
            $stmt = $pdo->prepare("SELECT * FROM merit_claim WHERE ClaimID = ? AND UserID = ?");
            $stmt->execute([$claimID, $userID]);
            $claim = $stmt->fetch();
        }
    }
}

function field($name, $value, $type = 'text', $readonly = false) {
    $ro = $readonly ? 'readonly' : '';
    return "<input type=\"$type\" name=\"$name\" id=\"$name\" value=\"" . htmlspecialchars($value) . "\" $ro required />";
}

function selected($val, $option) {
    return $val === $option ? 'selected' : '';
}

$isView = ($mode === 'view');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Update Merit - MyPetakom</title>
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
    <a href="view_claimed.php">View Claimed Merit</a>
    <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="manage_merit.php">Manage Merit</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>
<h1><?= $isView ? 'View Merit' : 'Update Merit' ?></h1>

<?php if ($error): ?>
  <p class="message error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<?php if ($success): ?>
  <p class="message success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="card">
  <label for="eventName">Event Name</label>
  <?= field('eventName', $claim['EventName'], 'text', $isView) ?>

  <label for="eventDate">Event Date</label>
  <?= field('eventDate', $claim['EventDate'], 'date', $isView) ?>

  <label for="organizer">Organizer</label>
  <?= field('organizer', $claim['Organizer'], 'text', $isView) ?>

  <label for="position">Position</label>
  <select name="position" id="position" <?= $isView ? 'disabled' : '' ?> required>
    <option value="Main Committee" <?= selected($claim['Position'], 'Main Committee') ?>>Main Committee</option>
    <option value="Committee" <?= selected($claim['Position'], 'Committee') ?>>Committee</option>
    <option value="Participant" <?= selected($claim['Position'], 'Participant') ?>>Participant</option>
  </select>

  <label for="level">Event Level</label>
  <select name="level" id="level" <?= $isView ? 'disabled' : '' ?> required>
    <option value="International" <?= selected($claim['Level'], 'International') ?>>International</option>
    <option value="National" <?= selected($claim['Level'], 'National') ?>>National</option>
    <option value="State" <?= selected($claim['Level'], 'State') ?>>State</option>
    <option value="District" <?= selected($claim['Level'], 'District') ?>>District</option>
    <option value="UMPSA" <?= selected($claim['Level'], 'UMPSA') ?>>UMPSA</option>
  </select>

  <label for="supportingDoc">Supporting Document</label>
  <?php if ($claim['Supporting_Doc']): ?>
    <p><a href="<?= htmlspecialchars($claim['Supporting_Doc']) ?>" target="_blank">View Uploaded Document</a></p>
  <?php endif; ?>
  <?php if (!$isView): ?>
    <input type="file" name="supportingDoc" id="supportingDoc" accept=".pdf,image/jpeg,image/png" />
  <?php endif; ?>

  <?php if (!$isView): ?>
    <button type="submit" name="update_claim">Update</button>
  <?php endif; ?>
</form>

<p><a href="view_claimed.php" style="color:#2563eb; font-weight:600;">&larr; Back to Claimed Merit</a></p>
</main>
</body>
</html>
