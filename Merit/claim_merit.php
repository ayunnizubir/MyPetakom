<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $merit_id = $_POST['merit_id'];
    $submitted_date = date('d-m-Y');
    $supporting_doc = $_FILES['supporting_doc']['name'];

    $target_dir = "upload/";
    $target_file = $target_dir . basename($supporting_doc);
    move_uploaded_file($_FILES['supporting_doc']['tmp_name'], $target_file);

 $sql = "INSERT INTO merit_claim (Claim_Status, Submitted_Date, Supporting_Doc, UserID, MeritID)
            VALUES ('Pending', '$submitted_date', '$supporting_doc', '$user_id', '$merit_id')";

    $msg = $conn->query($sql) ? "Claim submitted successfully!" : "Error: " . $conn->error;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Claim Missing Merit</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="form-container">
    <h2>Claim Missing Merit</h2>
    <?php if (!empty($msg)) echo "<p class='msg'>$msg</p>"; ?>
    <form method="POST" enctype="multipart/form-data">
        <label>User ID:</label>
        <input type="text" name="user_id" required>

        <label>Merit ID:</label>
        <input type="text" name="merit_id" required>

        <label>Upload Supporting Document:</label>
        <input type="file" name="supporting_doc" required>

        <input type="submit" value="Submit Claim">
    </form>
</div>
</body>
</html>
