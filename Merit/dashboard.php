<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit;
}

$userID = $_SESSION['UserID'];
$name = $_SESSION['Name'];
$matricID = $_SESSION['MatricID'];

try {
    $stmt = $pdo->prepare("SELECT IFNULL(SUM(Marks),0) AS TotalMerits FROM merit WHERE UserID = ?");
    $stmt->execute([$userID]);
    $totalMerits = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT MONTH(Date) AS month, COUNT(MeritID) AS event_count
        FROM merit WHERE UserID=? AND YEAR(Date)=YEAR(CURDATE())
        GROUP BY MONTH(Date) ORDER BY MONTH(Date)
    ");
    $stmt->execute([$userID]);
    $monthData = $stmt->fetchAll();

    $monthlyCounts = array_fill(1, 12, 0);
    foreach ($monthData as $row) {
        $monthlyCounts[(int)$row['month']] = (int)$row['event_count'];
    }

} catch (Exception $e) {
    exit("Error fetching merit data: " . htmlspecialchars($e->getMessage()));
}

$qrString = "MatricID: $matricID\nName: $name\nTotal Merits: $totalMerits";

ob_start();
include 'phpqrcode/qrlib.php';
QRcode::png($qrString, null, QR_ECLEVEL_M, 4);
$imageString = base64_encode(ob_get_contents());
ob_end_clean();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Dashboard - MyPetakom</title>
<link rel="stylesheet" href="styles.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #fff;
    color: #4b5563;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* Sidebar style */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 210px;
    height: 100%;
    background:rgb(219, 224, 226);
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    padding: 20px;
    z-index: 1000;
}
.sidebar img {
    width: 100%;
    max-width: 150px;
    display: block;
    margin: 0 auto 20px;
}
.sidebar h2 {
    text-align: center;
    font-weight: 700;
    color: #111827;
    margin-bottom: 20px;
}
.sidebar ul {
    list-style: none;
    padding: 0;
}
.sidebar ul li {
    margin: 15px 0;
    font-weight: 600;
}
.sidebar ul li a {
    text-decoration: none;
    color: #111827;
}
.sidebar ul li a:hover {
    color: #2563eb;
}

/* Header style */
header {
    position: fixed;
    top: 0;
    left: 250px;
    right: 0;
    height: 80px;
    background:rgb(69, 238, 227);
    padding: 0 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
}
header .logo {
    font-weight: 700;
    font-size: 1.5rem;
    color: #111827;
}
header nav a {
    margin-left: 1.5rem;
    font-weight: 600;
    font-size: 0.95rem;
    color: #111827;
    text-decoration: none;
}
header nav a:hover {
    color: #2563eb;
}

/* Main content */
main {
    margin-left: 250px;
    padding: 120px 30px 30px 30px;
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
}
main h1 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: #111827;
}
main p.lead {
    font-size: 1.125rem;
    margin-bottom: 3rem;
}

.metrics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}
.card {
    background: #f9fafb;
    border-radius: 0.75rem;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    align-items: center;
}
.metric-value {
    font-size: 3rem;
    font-weight: 800;
    color: #111827;
    margin-bottom: 0.25rem;
}
.metric-label {
    font-weight: 600;
    color: #9ca3af;
}
.qr-container {
    margin-top: 1rem;
    text-align: center;
}
.qr-container img {
    margin-top: 1rem;
    width: 160px;
    height: 160px;
    border-radius: 0.75rem;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
}
main h2 {
    font-weight: 700;
    font-size: 1.75rem;
    color: #111827;
    margin-bottom: 1rem;
}
footer {
    text-align: center;
    padding: 15px 0;
    color: #9ca3af;
    font-size: 0.9rem;
    margin-top: auto;
    
}
</style>
</head>

<body>
<div class="sidebar">
    <img src="petakom_logo.png" alt="PETAKOM Logo">
    <h2>MyPetakom</h2>
    <ul>
        <li><b>STUDENT</b></li>
        <li><a href="claim_merit.php">Claim Merit</a></li>
        <li><a href="view_claimed.php">View Claimed Merit</a></li>
    </ul>
</div>

<header>
  <div class="logo">MYPETAKOM</div>
  <nav>
    <span style="font-weight:600; margin-right:10px;"><?=htmlspecialchars($name)?></span>
    <a href="logout.php" style="border: 1px solid #000; padding: 5px 10px; text-decoration: none;">Log Out</a>
  </nav>
</header>

<main>
  <h1>Welcome, <?=htmlspecialchars($name)?></h1>
  <p class="lead">Overview of your merits and activities for the academic year.</p>

  <section class="metrics" aria-label="Key metrics" style="display: flex; gap: 30px; justify-content: center; flex-wrap: wrap;">

    <article class="card" aria-labelledby="total-merits-label">
      <div id="total-merits-value" class="metric-value"><?=$totalMerits?></div>
      <div id="total-merits-label" class="metric-label">Total Merit Points</div>
    </article>
    <article class="card" aria-labelledby="qr-code-label">
      <div id="qr-code-label" class="metric-label" style="font-weight:700;">Your Merit QR Code</div>
      <div class="qr-container" aria-describedby="qr-desc">
        <img src="data:image/png;base64,<?=$imageString?>" alt="QR Code showing your merit summary" />
        <p id="qr-desc" style="font-size:0.85rem; color:#9ca3af; margin-top:0.5rem;">Scan to view your merit summary</p>
      </div>
    </article>
  </section>

  <section aria-label="Participation Trends">
    <h2>Your Participation Trends (This Academic Year)</h2>
    <canvas id="participationChart" width="800" height="400" role="img" aria-label="Participation trend chart"></canvas>
  </section>
</main>

<footer>
  &copy; <?=date('Y')?> MyPetakom. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('participationChart').getContext('2d');
  const participationChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
      datasets: [{
        label: 'Events Participated',
        data: <?=json_encode(array_values($monthlyCounts))?>,
        borderColor: 'rgba(17,24,39,0.85)',
        backgroundColor: 'rgba(17,24,39,0.1)',
        tension: 0.3,
        pointRadius: 5,
        pointHoverRadius: 7,
        borderWidth: 3,
        fill: false,
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1, color: '#6b7280' }, grid: { color:'#e5e7eb' }},
        x: { ticks: { color:'#6b7280' }, grid: { color:'#f3f4f6' }},
      },
      plugins: {
        legend: { labels: { color: '#374151', font: { weight:'600', size:14 } }},
        tooltip: {
          enabled: true,
          mode: 'nearest',
          intersect: false,
          backgroundColor: '#111827',
          titleColor: '#f9fafb',
          bodyColor: '#f9fafb',
          cornerRadius: 6,
          caretSize: 8,
        }
      },
      interaction: { mode: 'nearest', intersect: false }
    }
  });
</script>
</body>
</html>
