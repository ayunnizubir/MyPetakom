<?php 
require_once 'db.php'; 

if (!isset($_GET['id'])) {
    die("Error: Claim ID not provided.");
}

$claim_id = $_GET['id'];
$sql = "SELECT * FROM merit_claim WHERE ClaimID = '$claim_id'";
$result = $conn->query($sql);
$claim = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $merit_id = $_POST['merit_id'];
    $supporting_doc = $_FILES['supporting_doc']['name'];
    $target_dir = "upload/";
    $target_file = $target_dir . basename($supporting_doc);
    move_uploaded_file($_FILES['supporting_doc']['tmp_name'], $target_file);

    $sql_update = "UPDATE merit_claim SET MeritID='$merit_id', Supporting_Doc='$supporting_doc' WHERE ClaimID='$claim_id'";
    if ($conn->query($sql_update)) {
        header("Location: manage_merit.php");
        exit;
    } else {
        die("Error: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Claim</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="form-container">
    <h2>Update Claim</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Merit ID:</label>
        <input type="text" name="merit_id" value="<?php echo $claim['MeritID']; ?>" required>

        <label>Upload Supporting Document:</label>
        <input type="file" name="supporting_doc" required>

        <input type="submit" value="Update Claim">
    </form>
</div>
</body>
</html>
