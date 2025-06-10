<?php  
require_once 'db.php'; 

$error = ''; 
$success = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $role = $_POST['role'] ?? 'student'; 
    $matricID = $role === 'admin' ? null : trim($_POST['matricID'] ?? ''); 
    $name = trim($_POST['name'] ?? ''); 
    $email = trim($_POST['email'] ?? ''); 
    $password = $_POST['password'] ?? ''; 
    $confirm_password = $_POST['confirm_password'] ?? ''; 

    // Basic validation 
    if (($role === 'student' && !$matricID) || !$name || !$email || !$password || !$confirm_password) { 
        $error = 'Please fill in all required fields.'; 
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
        $error = 'Invalid email address.'; 
    } elseif ($password !== $confirm_password) { 
        $error = 'Passwords do not match.'; 
    } else { 
        // Check uniqueness based on role
        if ($role === 'student') {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE MatricID = ? OR Email = ?");
            $stmt->execute([$matricID, $email]);
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE Email = ?");
            $stmt->execute([$email]);
        }
        $exists = (int)$stmt->fetchColumn(); 
        if ($exists > 0) { 
            $error = 'Matric ID or Email already registered.'; 
        } else { 
            // Insert into database 
            $hash = password_hash($password, PASSWORD_DEFAULT); 
            $stmt = $pdo->prepare("INSERT INTO user (MatricID, Name, Email, Password, Role) VALUES (?, ?, ?, ?, ?)"); 
            $stmt->execute([$matricID, $name, $email, $hash, $role]); 
            $success = 'Registration successful! You can now <a href="login.php" class="link">log in</a>.'; 
        } 
    } 
} 
?> 

<!DOCTYPE html> 
<html lang="en"> 
<head> 
  <meta charset="UTF-8" /> 
  <meta name="viewport" content="width=device-width, initial-scale=1" /> 
  <title>Sign Up - MyPetakom</title> 
  <style> 
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap'); 

    body { 
      background: #ffffff; 
      color: #6b7280; 
      font-family: 'Poppins', sans-serif; 
      font-size: 17px; 
      margin: 0; 
      min-height: 100vh; 
      display: flex; 
      justify-content: center; 
      align-items: center; 
      padding: 20px; 
    } 

    main { 
      max-width: 400px; 
      width: 100%; 
      background: #fff; 
      border-radius: 0.75rem; 
      box-shadow: 0 2px 12px rgba(0,0,0,0.05); 
      padding: 2rem 2.5rem; 
      box-sizing: border-box; 
    } 

    h1 { 
      font-size: 48px; 
      line-height: 1.1; 
      font-weight: 700; 
      color: #111827; 
      margin-bottom: 0.75rem; 
      letter-spacing: -0.02em; 
    } 

    p.subtitle { 
      font-size: 1rem; 
      color: #6b7280; 
      margin-bottom: 2rem; 
    } 

    form { 
      display: flex; 
      flex-direction: column; 
    } 

    label { 
      font-weight: 600; 
      color: #111827; 
      margin-bottom: 0.35rem; 
    } 

    input[type="text"], input[type="email"], input[type="password"], select { 
      border-radius: 0.375rem; 
      border: 1px solid #d1d5db; 
      padding: 0.5rem 0.75rem; 
      margin-bottom: 1.25rem; 
      font-size: 1rem; 
      color: #111827; 
      transition: border-color 0.3s ease; 
    } 

    input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, select:focus { 
      outline: none; 
      border-color: #111827; 
    } 

    button { 
      background: #111827; 
      color: white; 
      font-weight: 700; 
      font-size: 1.125rem; 
      padding: 0.75rem; 
      border: none; 
      border-radius: 0.5rem; 
      cursor: pointer; 
      user-select: none; 
      transition: background-color 0.25s ease; 
    } 

    button:hover { 
      background: #374151; 
    } 

    .message { 
      margin-bottom: 1rem; 
      padding: 0.85rem 1rem; 
      border-radius: 0.625rem; 
      font-weight: 600; 
      line-height: 1.2; 
    } 

    .error { 
      background: #fee2e2; 
      color: #b91c1c; 
    } 

    .success { 
      background: #d1fae5; 
      color: #065f46; 
    } 

    .login-link { 
      margin-top: 1rem; 
      font-size: 0.9rem; 
      color: #6b7280; 
      text-align: center; 
    } 

    .login-link a, .message a.link { 
      color: #111827; 
      font-weight: 600; 
      text-decoration: none; 
      transition: color 0.3s ease; 
    } 

    .login-link a:hover, .login-link a:focus, .message a.link:hover, .message a.link:focus { 
      color: #2563eb; 
      text-decoration: underline; 
    } 

    #matricIDField {
      transition: all 0.3s ease;
    }
  </style> 
</head> 
<body> 
  <main role="main" aria-labelledby="signup-title"> 
    <h1 id="signup-title">Create your Account</h1> 
    <p class="subtitle">Join MyPetakom to start managing your merits efficiently.</p> 
    <?php if ($error): ?>
      <div class="message error" role="alert"><?=htmlspecialchars($error)?></div>
    <?php elseif ($success): ?>
      <div class="message success" role="alert"><?= $success ?></div>
    <?php endif; ?> 
    <form method="post" novalidate aria-describedby="form-desc"> 
      <p id="form-desc" class="sr-only">Sign up form to create your MyPetakom account.</p>

      <label for="role">Role</label>
      <select id="role" name="role" required onchange="toggleMatricID()">
        <option value="student" selected>Student</option>
        <option value="admin">Admin</option>
      </select>

      <div id="matricIDField">
        <label for="matricID">Matric ID</label> <br>
        <input type="text" id="matricID" name="matricID" autocomplete="off" />
      </div>

      <label for="name">Full Name</label>
      <input type="text" id="name" name="name" required autocomplete="name" />

      <label for="email">Email Address</label>
      <input type="email" id="email" name="email" required autocomplete="email" />

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required autocomplete="new-password" />

      <label for="confirm_password">Confirm Password</label>
      <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password" />

      <button type="submit" aria-label="Create Account">Sign Up</button>
    </form> 

    <p class="login-link">Already have an account? <a href="login.php">Log in</a></p> 
  </main> 

  <script>
    function toggleMatricID() {
      const roleSelect = document.getElementById('role');
      const matricField = document.getElementById('matricIDField');
      if (roleSelect.value === 'admin') {
        matricField.style.display = 'none';
        document.getElementById('matricID').required = false;
      } else {
        matricField.style.display = 'block';
        document.getElementById('matricID').required = true;
      }
    }
    window.onload = toggleMatricID;
  </script>

</body> 
</html>
