<?php
require_once 'db.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $pdo->prepare('SELECT UserID, Password, Name, MatricID, Role FROM user WHERE Email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['Password'])) {
            // Set session variables consistently
            $_SESSION['UserID'] = $user['UserID'];
            $_SESSION['Name'] = $user['Name'];
            $_SESSION['MatricID'] = $user['MatricID'];
            $_SESSION['role'] = $user['Role'];

            // Redirect based on role
            if ($user['Role'] === 'admin') {
                header('Location: admin_dashboard.php');
                exit();
            } else {
                header('Location: dashboard.php');
                exit();
          }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - MyPetakom</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
    body {
      background: #fff;
      font-family: 'Poppins', sans-serif;
      color: #4b5563;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .container {
      max-width: 400px;
      width: 100%;
      padding: 2rem;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      border-radius: 0.75rem;
    }
    h1 {
      font-weight: 700;
      font-size: 2.5rem;
      margin-bottom: 1rem;
      color: #111827;
      text-align: center;
    }
    form {
      display: flex;
      flex-direction: column;
    }
    label {
      margin-bottom: 0.5rem;
      font-weight: 600;
    }
    input[type="email"],
    input[type="password"] {
      padding: 0.5rem 0.75rem;
      margin-bottom: 1.25rem;
      font-size: 1rem;
      border: 1px solid #d1d5db;
      border-radius: 0.375rem;
    }
    button {
      background: #111827;
      color: white;
      padding: 0.75rem;
      font-weight: 700;
      font-size: 1.1rem;
      border: none;
      border-radius: 0.5rem;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    button:hover {
      background: #374151;
    }
    .error {
      color: #dc2626;
      margin-bottom: 1rem;
      text-align: center;
      font-weight: 600;
    }
    .signup-link {
      margin-top: 1rem;
      text-align: center;
      font-size: 0.95rem;
      color: #6b7280;
    }
    .signup-link a {
      color: #111827;
      font-weight: 600;
      text-decoration: none;
      transition: color 0.3s ease;
    }
    .signup-link a:hover,
    .signup-link a:focus {
      color: #2563eb;
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <div class="container" role="main">
    <h1>MyPetakom Login</h1>
    <?php if ($error): ?>
      <p class="error" role="alert"><?=htmlspecialchars($error)?></p>
    <?php endif; ?>
    <form method="post" aria-label="Login form" novalidate>
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required autocomplete="email" />
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required autocomplete="current-password" />
      <button type="submit">Log In</button>
    </form>
    <p class="signup-link"> Don't have an account? <a href="signup.php">Sign up here</a> </p> 
  </div>
</body>
</html>
