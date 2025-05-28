<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db.php'; 
require_once 'phpqrcode/qrlib.php'; // Include the QR Code library

// Check if student_id is provided
if (!isset($_GET['student_id']) || empty($_GET['student_id'])) {
    die("Error: Student ID not provided.");
}

$student_id = $_GET['student_id']; 

// Calculate total merit points
$sql = "SELECT SUM(m.Merit_Point) AS total_merit 
        FROM merit m 
        JOIN merit_claim mc ON m.MeritID = mc.MeritID 
        JOIN user u ON mc.UserID = u.UserID 
        WHERE u.StudentID = '$student_id' AND mc.Claim_Status = 'Approved'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// Generate QR Code
$qr_data = "Student ID: $student_id, Total Merit: " . ($row['total_merit'] ?? 0);
$qr_file = "upload/qr_$student_id.png"; // Path to save the QR code image
QRcode::png($qr_data, $qr_file, QR_ECLEVEL_L, 4); // Generate the QR code
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Student Dashboard </title>
    <!-- Place the CSS link here -->
    <link rel="stylesheet" href="css/dashboard-style.css" />
</head>
<body>
<div class="form-container">
    <h2>Dashboard for <?php echo htmlspecialchars($student_id); ?></h2>
    <p>Total Merit Points: <strong><?php echo $row['total_merit'] ?? 0; ?></strong></p>

     <!-- Display the QR Code -->
    <h3>Your QR Code:</h3>
    <img src="<?php echo $qr_file; ?>" alt="QR Code" style="width: 200px; height: 200px;"/> <!-- Adjust size as needed -->

</div>
</body>
</html>
