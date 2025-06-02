<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "mypetakom";

    $conn = new mysqli($host, $user, $pass, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $fullname = $_POST['fullname'] ?? '';
    $matricid = $_POST['matricid'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (fullname, matricid, email, phone, username, password)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $fullname, $matricid, $email, $phone, $username, $password);

    if ($stmt->execute()) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $fullname;
        $_SESSION['role'] = 'user';

        header("Location: udashboard.php");
        exit;
    } else {
        echo "<script>alert('Registration failed. Username or email may already exist.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyPetakom Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #ccc;
        }
        .header img {
            width: 120px;
            height: auto;
        }
        .form-container {
            margin: 40px auto;
            width: 400px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"] {
            width: 95%;
            padding: 8px;
        }
        .buttons {
            display: flex;
            justify-content: space-between;
            width: 300px;
            margin: 0 auto;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="umpsa_logo.png" alt="UMPSA's Logo">
        <img src="petakom_logo.png" alt="Petakom's Logo">
    </div>

    <div class="form-container">
        <h2>MyPetakom</h2>
        <form action="register.php" method="POST">
            <table>
                <tr><td><input type="text" name="fullname" placeholder="Full Name" required></td></tr>
                <tr><td><input type="text" name="matricid" placeholder="Matric ID" required></td></tr>
                <tr><td><input type="email" name="email" placeholder="Email" required></td></tr>
                <tr><td><input type="tel" name="phone" placeholder="Phone Number" required></td></tr>
                <tr><td><input type="text" name="username" placeholder="Username" required></td></tr>
                <tr><td><input type="password" name="password" placeholder="Password" required></td></tr>
            </table>
            <div class="buttons">
                <button type="button" onclick="window.location.href='login.php'">Back</button>
                <button type="submit">Register</button>
            </div>
        </form>
    </div>

</body>
</html>
