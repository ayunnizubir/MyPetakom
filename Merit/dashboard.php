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
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
  body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #fff;
    color: #4b5563;
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
  }
  header {
    position: sticky;
    top: 0;
    background: #fff;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
    border-radius: 0.75rem;
  }
  .logo {
    font-weight: 700;
    font-size: 1.5rem;
    color: #111827;
  }
  nav a {
    margin-left: 1.5rem;
    font-weight: 600;
    font-size: 0.95rem;
    color: #111827;
    text-decoration: none;
  }
  nav a:hover {
    color: #2563eb;
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
    color: #9ca3af;
    font-size: 0.9rem;
    margin-top: 6rem;
    padding-bottom: 2rem;
  }
</style>
</head>
<body>
<header>
  <div class="logo">MyPetakom</div>
  <nav>
    <a href="claim_merit.php">Claim Merit</a>
    <a href="view_claimed.php">View Claimed Merit</a>
    <?php if ($_SESSION['role'] === 'admin'): ?>
      <a href="manage_merit.php">Manage Merit</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>
  <h1>Welcome, <?=htmlspecialchars($name)?></h1>
  <p class="lead">Overview of your merits and activities for the academic year.</p>

  <section class="metrics" aria-label="Key metrics">
    <article class="card" aria-labelledby="total-merits-label">
    <br> <br> <br>
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
