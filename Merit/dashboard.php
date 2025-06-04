<?php 
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
$total_merit = $row['total_merit'] ?? 0;

//dummy value 
$totalEvents = 0;
  $upcomingEvents = 0;
    $participationRange = "0";
    $userName = "User's Name";

// Get participation data (example: monthly data)
$participationData = [];
$labels = [];
$sql = "SELECT MONTHNAME(mc.Submitted_Date) AS month, COUNT(*) AS total 
        FROM merit_claim mc 
        JOIN user u ON mc.UserID = u.UserID 
        WHERE u.StudentID = ? AND mc.Claim_Status = 'Approved' 
        GROUP BY MONTH(mc.Submitted_Date)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $labels[] = $row['month'];
    $participationData[] = $row['total'];
}

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
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .dashboard-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 30px;
    }

    .info-boxes {
      display: flex;
      gap: 20px;
      justify-content: center;
      margin-bottom: 30px;
      flex-wrap: wrap;
    }

    .info-box {
      border: 1px solid #ccc;
      padding: 20px;
      width: 250px;
      text-align: center;
      background: white;
      box-shadow: 2px 2px 8px rgba(0,0,0,0.1);
      border-radius: 8px;
    }

    .info-title {
      font-weight: bold;
      margin-bottom: 5px;
    }

    .info-subtext {
      color: #666;
      font-size: 0.9em;
      margin-bottom: 10px;
    }

    .info-value {
      font-size: 2em;
      font-weight: bold;
    }

    canvas {
      max-width: 500px;
      margin-top: 20px;
    }

    .qr-container {
    margin-top: 30px;
    text-align: center;
    }

    .qr-container img {
      width: 200px;
      height: 200px;
    }
  </style>
</head>
<body>
   
<div class="sidebar">
    <img src="css/petakom_logo.png" alt="PETAKOM Logo">
        <h2>MyPetakom</h2>
        <ul>
            <b> Student</a></b>
            <li><a href="manage_merit.php">Manage Merit</a></li>
            <li><a href="claim_merit.php">Merit Dashboard</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="header">
            <h1>Merit Dashboard</h1>
            <div class="profile">
                <div class="profile-icon">👤</div>
                <span><?php echo $userName; ?></span>
                <button>Sign Out</button>
            </div>
        </div>

    <br>

    <div class="dashboard-container">
        <h2> MyPetakom Student Dashboard Overview </h2>
        <h3>Insightful reports: Students activities summary</h3>
        <br>

           <!-- Total Merit Point -->
        <div class="max-w-md mx-auto text-center my-6 p-4 rounded-2xl shadow-md border border-gray-300 bg-white">
        <h2 class="text-lg font-bold text-gray-800">Total Merit Point</h2>
        <p class="text-sm text-gray-600 mb-2">Approved merits earned</p>
        <p class="text-3xl font-bold text-petakom-yellow"><?php echo $total_merit; ?></p>
        </div>
        <br>

        <div class="info-boxes">
        <div class="info-box">
        <div class="info-title">Total Events</div>
        <div class="info-subtext">All registered events</div>
        <div class="info-value"><?php echo $totalEvents; ?></div>
      </div>
      <div class="info-box">
        <div class="info-title">Upcoming Events</div>
        <div class="info-subtext">Event scheduled in the future</div>
        <div class="info-value"><?php echo $upcomingEvents; ?></div>
      </div>
      <div class="info-box">
        <div class="info-title">Participation</div>
        <div class="info-subtext">Total student participated</div>
        <div class="info-value"><?php echo $participationRange; ?></div>
      </div>
    </div>
    </main>

    <br> 

    <!-- Participation Trend Chart -->
<div class="max-w-2xl mx-auto my-10 p-6 bg-white rounded-2xl shadow-md border border-gray-300 text-center">
  <h2 class="text-lg font-bold text-gray-800 mb-2">Participation Trend</h2>
  <p class="text-sm text-gray-600 mb-4">Student engagement from recent months</p>
  <canvas id="participationChart"></canvas>
    
</div>

<br>

     <!-- Display the QR Code -->
    <div class="max-w-md mx-auto my-10 p-6 bg-white rounded-2xl shadow-md border border-gray-300 text-center">
  <h2 class="text-lg font-bold text-gray-800 mb-2">Your QR Code</h2>
  <p class="text-sm text-gray-600 mb-4">Scan to verify merit summary</p>
  <img src="<?php echo $qr_file; ?>" alt="QR Code" class="mx-auto w-40 h-40 rounded-md border" />
</div>

<script>
const ctx = document.getElementById('participationChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: 'Participation by Month',
            data: <?php echo json_encode($participationData); ?>,
            backgroundColor: 'rgba(255, 206, 86, 0.2)',
            borderColor: 'rgba(255, 159, 64, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                precision: 0
            }
        }
    }
});
</script>
</div>
</div>
</div>
</body>
</html>
