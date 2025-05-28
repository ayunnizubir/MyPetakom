<?php
// dashboard_advisor.php
$conn = new mysqli("localhost","root","","db_registration");

// pull counts per status
$status_counts = [];
$res = $conn->query("SELECT status, COUNT(*) AS cnt FROM events GROUP BY status");
while($r = $res->fetch_assoc()){
    $status_counts[$r['status']] = (int)$r['cnt'];
}
$total_events = array_sum($status_counts);

// pull counts per facility (location)
$location_counts = [];
$res2 = $conn->query("SELECT location, COUNT(*) AS cnt FROM events GROUP BY location");
while($r2 = $res2->fetch_assoc()){
    $location_counts[$r2['location']] = (int)$r2['cnt'];
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Event Advisor</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .cards { display: flex; gap: 20px; margin-bottom: 30px; }
        .card {
        flex: 1;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        text-align: center;
        }
        .card h3 { margin-bottom: 10px; font-size: 1rem; color: #555; }
        .card p { font-size: 2rem; margin: 0; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <img src="../css/petakom_logo.png" alt="PETAKOM Logo">
        <h2>MyPetakom</h2>
        <ul>
        <li><a href="dashboard_advisor.php">Dashboard Event Advisor</a></li>
        <li><a href="create_event.html">Create Event</a></li>
        <li><a href="event_list.php">Event List</a></li>
        <li><a href="../Committee/committee.php">Committee</a></li>
        <li><a href="../Merit/merit_applications.php">Merit Application</a></li>
        </ul>
    </div>

<<<<<<< HEAD
    <!-- Main Content -->
=======
    
>>>>>>> 6c3d878ae734b94f067428de2fbff590fde75955
    <div class="main">
        <div class="header">
        <h1>Dashboard Event Advisor</h1>
        <div class="profile">
            <div class="profile-icon">👤</div>
            <span>User's Name</span>
            <button>Sign Out</button>
        </div>
        </div>

        <div class="container">
        <h2>Summary</h2>
        <div class="cards">
            <div class="card">
            <h3>Total Events</h3>
            <p><?= $total_events ?></p>
            </div>
            <?php foreach($status_counts as $status => $cnt): ?>
            <div class="card">
            <h3><?= htmlspecialchars($status) ?></h3>
            <p><?= $cnt ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <h2>Events by Status</h2>
        <canvas id="statusChart" width="600" height="300"></canvas>

        <h2>Facility Usage</h2>
        <div class="cards">
            <?php foreach($location_counts as $loc => $cnt): ?>
            <div class="card">
            <h3><?= htmlspecialchars($loc) ?></h3>
            <p><?= $cnt ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <canvas id="facilityChart" width="600" height="300"></canvas>
        </div>
    </div>

    <script>
<<<<<<< HEAD
        // Status Chart
=======
        
>>>>>>> 6c3d878ae734b94f067428de2fbff590fde75955
        new Chart(document.getElementById('statusChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys($status_counts)) ?>,
            datasets: [{
            label: 'Number of Events',
            data: <?= json_encode(array_values($status_counts)) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
        });

<<<<<<< HEAD
        // Facility Chart
=======
        
>>>>>>> 6c3d878ae734b94f067428de2fbff590fde75955
        new Chart(document.getElementById('facilityChart'), {
        type: 'pie',
        data: {
        labels: <?= json_encode(array_keys($location_counts)) ?>,
            datasets: [{
            data: <?= json_encode(array_values($location_counts)) ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.5)',
                'rgba(75, 192, 192, 0.5)',
                'rgba(255, 205, 86, 0.5)',
                'rgba(201, 203, 207, 0.5)'
            ]
            }]
        }
        });
    </script>
</body>
</html>
