<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $host = "localhost";
    $dbname = "mypetakom";
    $user = "root";
    $pass = "";

    $conn = new mysqli($host, $user, $pass, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $user['fullname'];
            $_SESSION['role'] = 'user'; // Set appropriately if you use roles

            header("Location: udashboard.php");
            exit;
        } else {
            echo "<script>alert('Incorrect password.');</script>";
        }
    } else {
        echo "<script>alert('User not found.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyPetakom Login</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional: external stylesheet -->
</head>
<body>
    <header>
        <img src="umpsa_logo.png" alt="UMPSA's Logo" style="width: 25%; height: auto;">
        <img src="petakom_logo.png" alt="Petakom's Logo" style="width: 25%; height: auto;">
    </header>
    <main>
        <h1>MyPetakom</h1>
        <form id="loginForm" method="post" action="login.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="userType">User Type</label>
            <select id="userType" name="userType" required>
                <option value="admin">Admin</option>
                <option value="user">User</option>
                <option value="guest">Guest</option>
            </select>

            <button type="submit">Login</button>
            <p>New User? <a href="register.php">Register Here</a></p>
        </form>
    </main>
</body>
</html>
