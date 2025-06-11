<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'db.php';

// Check if the user is an admin
if (!isset($_SESSION['UserID']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reward_merit'])) {
    $matricID = trim($_POST['studentID']);  // this is actually MatricID from form input
    $eventName = trim($_POST['eventName']);
    $eventDate = $_POST['eventDate'];
    $organizer = trim($_POST['organizer']);
    $position = $_POST['position'];
    $level = $_POST['level'];
    $filePath = '';

    // Validate required inputs
    if (!$matricID || !$eventName || !$eventDate || !$organizer || !$position || !$level) {
        $error = 'Please fill in all fields.';
    } else {
        // Find UserID based on MatricID
        $stmt = $pdo->prepare("SELECT UserID FROM user WHERE MatricID = ?");
        $stmt->execute([$matricID]);
        $user = $stmt->fetch();

        if (!$user) {
            $error = 'Matric ID not found in the system.';
        } else {
            $userID = $user['UserID'];

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
                // Insert the merit claim into the database
                $stmt = $pdo->prepare("INSERT INTO merit_claim 
                (UserID, EventName, EventDate, Organizer, Position, Level, Supporting_Doc, Claim_Status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Rewarded')");
                
                $stmt->execute([
                    $userID,  // now correct UserID
                    $eventName, 
                    $eventDate, 
                    $organizer, 
                    $position, 
                    $level, 
                    $filePath
                ]);

                // Set success message in session
                $_SESSION['success'] = "Merit rewarded successfully.";
                
                // Redirect to the rewarded list page
                header('Location: rewarded_list.php');
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Reward Merit - MyPetakom</title>
  <link rel="stylesheet" href="style.css" />
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
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
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
<h1>Reward Merit</h1>

<?php if ($error): ?>
  <p class="message error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<?php if ($success): ?>
  <p class="message success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="card">
  <label for="studentID">Student ID</label>
  <input type="text" name="studentID" id="studentID" required />

  <label for="eventName">Event Name</label>
  <input type="text" name="eventName" id="eventName" required />

  <label for="eventDate">Event Date</label>
  <input type="date" name="eventDate" id="eventDate" required />

  <label for="organizer">Organizer</label>
  <input type="text" name="organizer" id="organizer" required />

  <label for="position">Position</label>
  <select name="position" id="position" required>
    <option value="Main Committee">Main Committee</option>
    <option value="Committee">Committee</option>
    <option value="Participant">Participant</option>
  </select>

  <label for="level">Event Level</label>
  <select name="level" id="level" required>
    <option value="International">International</option>
    <option value="National">National</option>
    <option value="State">State</option>
    <option value="District">District</option>
    <option value="UMPSA">UMPSA</option>
  </select>

  <label for="supportingDoc">Supporting Document</label>
  <input type="file" name="supportingDoc" id="supportingDoc" accept=".pdf,image/jpeg,image/png" required />

  <button type="submit">Reward Merit</button>
</form>
<a href="rewarded_list.php">View Rewarded Merit List</a>
</main>
</body>
</html>
