<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

$username = $_SESSION['username'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile Management</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      flex-direction: column;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem;
      background-color: #f0f0f0;
      border-bottom: 1px solid #ccc;
    }

    .logo, .title, .page-title, .user-info {
      margin: 0 10px;
    }

    .user-info {
      display: flex;
      align-items: center;
    }

    .user-info img {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      margin-left: 10px;
    }

    .sidebar {
      position: fixed;
      top: 60px;
      left: 0;
      width: 200px;
      background-color: #e0e0e0;
      height: 100vh;
      padding-top: 20px;
      box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }

    .sidebar a {
      display: block;
      padding: 12px;
      text-decoration: none;
      color: black;
      border-bottom: 1px solid #ccc;
    }

    main {
      margin-left: 220px;
      padding: 2rem;
    }

    h2 {
      text-align: center;
    }

    .profile-container {
      text-align: center;
      margin-top: 2rem;
    }

    .profile-image img {
      width: 120px;
      height: 160px;
      object-fit: cover;
      border: 1px solid #ccc;
      margin-bottom: 1rem;
    }

    .profile-info {
      max-width: 400px;
      margin: 0 auto;
      text-align: left;
      border: 1px solid #ccc;
      padding: 1rem;
      border-radius: 8px;
      background: #fafafa;
    }

    .profile-info p {
      margin: 10px 0;
    }

    .buttons {
      margin-top: 1.5rem;
      text-align: center;
    }

    .buttons a, .buttons button {
      display: inline-block;
      padding: 10px 20px;
      margin: 0 10px;
      text-decoration: none;
      color: white;
      background-color: #007BFF;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .buttons .danger {
      background-color: #dc3545;
    }

    form {
      display: inline;
    }
  </style>
</head>
<body>

<nav>
    <div class="header">
        <img src="petakom_logo.png" alt="Petakom's Logo" width="120px" height="auto">
    </div>
    <a href="udashboard.php">Dashboard</a>
    <a href="profile_management.php">Profile Management</a>
    <a href="membership_application.php">Membership Application</a>
    <a href="event_list.php">Event List</a>
    <a href="event_attendance.php">Event Attendance</a>
</nav>

<div style="flex-grow: 1;">
    <header>
        <div><strong>MyPetakom</strong></div>
        <div class="user-info">
            <div>Profile Management</div>
            <div><?php echo htmlspecialchars($username); ?></div>
            <form method="post" style="display:inline;">
                <button type="submit" name="logout">Sign Out</button>
            </form>
        </div>
    </header>

  <!-- Main Content -->
  <main>
    <h2>View Profile Information</h2>
    <div class="profile-container">
      <div class="profile-image">
        <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image">
      </div>

      <div class="profile-info">
        <p><strong>User's Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><strong>Matric ID:</strong> <?php echo htmlspecialchars($user['matric_id']); ?></p>
        <p><strong>User's Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>User's Phone Number:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
      </div>

      <div class="buttons">
        <a href="back.php">Back</a>
        <a href="edit.php?id=1">Edit</a>
        <form action="delete.php" method="post">
          <input type="hidden" name="id" value="1">
          <button class="danger" type="submit">Delete</button>
        </form>
      </div>
    </div>
  </main>

</body>
</html>
