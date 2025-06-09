<?php
include('db.php');
include('phpqrcode/qrlib.php');

$event   = null;
$qr_file = null;

// ── If an Event ID is provided, fetch it and generate QR ──
if (isset($_GET['event_id'])) {
    $event_id = intval($_GET['event_id']);

    // Fetch event
    $stmt = mysqli_prepare($conn, "SELECT * FROM events WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $event_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $event  = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($event) {
        // QR target: attendance_register.php?id=<event_id>
        $qr_data = "http://localhost/MyPetakom/event_attendance/attendance_register.php?id=" . $event_id;
        $qr_file = "qrcodes/{$event_id}.png";

        // Only generate if missing
        if (!file_exists($qr_file)) {
            QRcode::png($qr_data, $qr_file, QR_ECLEVEL_H, 5);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Create Attendance Slot</title>
  <style>
    /* … all your existing CSS below, unchanged … */
    /* Reset & base */
    * { box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      background-color: #f7f9fc;
      color: #333;
    }
    a { text-decoration: none; color: #337eff; }
    a:hover { text-decoration: underline; }

    /* Header */
    .header {
      background-color: #a5f0e3;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      position: fixed; top: 0; left: 0; right: 0; height: 60px; z-index: 1000;
    }
    .header-left {
      display: flex; align-items: center; gap: 15px;
    }
    .header-left img {
      height: 45px; width: auto; border-radius: 6px;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }
    .header-left h2 {
      margin: 0; font-weight: 700; color: #005f56; user-select: none;
    }
    .user-info {
      display: flex; align-items: center; gap: 15px;
      font-weight: 600; color: #004d40;
    }
    .user-info img {
      width:30px; height:30px; border-radius:50%; border:1.5px solid #337eff; background:white;
    }
    .signout-btn {
      background-color: #337eff; color:white; border:none; padding:7px 15px;
      border-radius:6px; cursor:pointer; font-weight:600;
      transition: background-color 0.3s;
    }
    .signout-btn:hover { background-color: #0f52ba; }

    /* Sidebar */
    .sidebar {
      background-color: #d9d9d9; width:240px; position:fixed;
      top:60px; bottom:0; padding:30px 20px; overflow-y:auto;
      box-shadow:2px 0 6px rgba(0,0,0,0.1); font-weight:600; user-select:none;
    }
    .sidebar h3 {
      margin-top:0; margin-bottom:20px; color:#005f56; font-size:1.3rem;
    }
    .sidebar p {
      margin:15px 0 8px; color:#004d40; font-weight:700; font-size:1rem;
    }
    .sidebar a {
      display:block; margin-bottom:15px; color:#004d40; font-weight:600;
      padding-left:10px; border-left:3px solid transparent;
      transition:border-color 0.3s;
    }
    .sidebar a:hover {
      border-left-color:#337eff; background-color:#c0e8ff;
      color:#003a75; border-radius:4px;
    }

    /* Content */
    .content {
      margin-left:260px;
      padding:90px 40px 40px 40px; /* space for fixed header+sidebar */
      min-height:calc(100vh - 60px);
      background:#f7f9fc;
    }

    .event-box {
      background-color:#fff; padding:30px 40px;
      border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.07);
      max-width:900px; margin:0 auto;
    }
    .event-box h3 {
      margin-top:0; color:#005f56; font-weight:700;
      font-size:1.8rem; margin-bottom:25px; user-select:none;
    }

    /* Search box */
    .search-box {
      display:flex; margin-bottom:25px; gap:15px;
    }
    .search-box input[type="text"] {
      flex-grow:1; padding:12px 15px; font-size:1rem;
      border:2px solid #ccc; border-radius:8px;
      transition:border-color 0.3s; outline-offset:2px;
    }
    .search-box input[type="text"]:focus {
      border-color:#337eff; outline:none;
    }
    .search-box button {
      background-color:#337eff; border:none; color:white;
      font-weight:700; font-size:1.1rem; padding:12px 28px;
      border-radius:8px; cursor:pointer;
      transition:background-color 0.3s;
    }
    .search-box button:hover { background-color:#0f52ba; }

    /* Table */
    table {
      width:100%; border-collapse:collapse; margin-bottom:30px;
      user-select:none;
    }
    th, td {
      border:1px solid #ccc; padding:12px 15px;
      text-align:center; font-weight:600; color:#004d40;
      background-color:#f0f8ff;
    }
    th {
      background-color:#337eff; color:white; font-size:1.1rem;
    }

    /* Buttons */
    .generate-btn,
    .download-btn {
      display:inline-block; margin-right:15px; padding:14px 30px;
      font-size:1.1rem; font-weight:700; border-radius:10px;
      border:none; cursor:pointer;
      box-shadow:0 4px 14px rgba(51,126,255,0.4);
      transition:background-color 0.3s; user-select:none;
    }
    .generate-btn {
      background-color:grey; color:white;
    }
    .generate-btn:hover { background-color:#555; }
    .download-btn {
      background-color:#337eff; color:white;
    }
    .download-btn:hover { background-color:#0f52ba; }

    /* QR Code */
    img.qr-code {
      margin-top:20px; width:180px; height:180px;
      border-radius:16px; box-shadow:0 4px 20px rgba(51,126,255,0.3);
      user-select:none;
    }

    /* Responsive */
    @media (max-width:850px) {
      .content { margin-left:0; padding:100px 20px 40px 20px; }
      .sidebar { display:none; }
      .event-box { max-width:100%; padding:20px; }
      .search-box { flex-direction:column; }
      .search-box input[type="text"],
      .search-box button {
        width:100%; margin:0; border-radius:8px;
      }
      .search-box button { margin-top:10px; }
      .generate-btn, .download-btn {
        width:100%; margin:10px 0;
      }
    }
  </style>
</head>
<body>
  <div class="header">
    <div class="header-left">
      <img
        src="petakom.png"
        alt="Petakom Logo"
      />
      <h2>Create Attendance Slot</h2>
    </div>
    <div class="user-info">
      <span>User's Name</span>
      <img src="https://img.icons8.com/ios-filled/50/000000/user.png" alt="User Icon" />
      <button class="signout-btn" onclick="alert('Sign out clicked')">
        Sign Out
      </button>
    </div>
  </div>

  <div class="sidebar">
    <h3>MyPetakom</h3>
    <p><strong>Petakom Coordinator</strong></p>
    <a href="#">Petakom Coordinator Dashboard</a>
    <p><strong>Event Advisor</strong></p>
    <a href="#">Create Attendance Slot</a>
    <p><strong>Student</strong></p>
    <a href="#">Attendance Registration</a>
  </div>

  <div class="content">
    <div class="event-box">
      <h3>Event Attendance Slot</h3>

      <!-- SEARCH BY EVENT ID -->
      <form class="search-box" method="GET" action="">
        <input
          type="text"
          name="event_id"
          placeholder="Enter event ID"
          value="<?= isset($_GET['event_id']) ? htmlspecialchars($_GET['event_id']) : '' ?>"
          required
          autocomplete="off"
        />
        <button type="submit">Search</button>
      </form>

      <!-- IF FOUND, SHOW DYNAMIC TABLE & QR -->
      <?php if ($event): ?>
        <table>
          <thead>
            <tr>
              <th>Event ID</th>
              <th>Event Name</th>
              <th>Date &amp; Time</th>
              <th>Location</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><?= htmlspecialchars($event['id']) ?></td>
              <td><?= htmlspecialchars($event['event_name']) ?></td>
              <td>
                <?= htmlspecialchars($event['event_date']) ?>
                <br>at <?= htmlspecialchars($event['event_time']) ?>
              </td>
              <td><?= htmlspecialchars($event['location']) ?></td>
              <td><?= htmlspecialchars($event['status']) ?></td>
            </tr>
          </tbody>
        </table>

        <button class="generate-btn" disabled>Generate QR Code</button>
        <br />
        <img src="<?= htmlspecialchars($qr_file) ?>" class="qr-code" alt="QR Code" />
        <br />
        <a href="<?= htmlspecialchars($qr_file) ?>">
          <button class="download-btn">Download QR Code</button>
        </a>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
