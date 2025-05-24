<?php
$conn = new mysqli("localhost", "root", "", "db_registration");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $student_name = $_POST['student_name'];
    $student_id = $_POST['student_id'];
    $role = $_POST['role'];

    $sql = "INSERT INTO committee (event_id, student_name, student_id, role) 
            VALUES ('$event_id', '$student_name', '$student_id', '$role')";

    if ($conn->query($sql) === TRUE) {
        $success = true;
    } else {
        echo "Error: " . $conn->error;
    }
}

$events = $conn->query("SELECT id, event_name FROM events WHERE status = 'Approved'");
$committees = $conn->query("SELECT committee.*, events.event_name FROM committee JOIN events ON committee.event_id = events.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Committee Management</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="sidebar">
        <img src="../css/petakom_logo.png" alt="PETAKOM Logo">
        <h2>MyPetakom</h2>
        <ul>
            <li><a href="#">Dashboard Event Advisor</a></li>
            <li><a href="../Event/create_event.html">Create Event</a></li>
            <li><a href="../Event/event_list.php">Event List</a></li>
            <li><a href="committee.php">Committee</a></li>
            <li><a href="../Merit/merit_applications.php">Merit Application</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="header">
            <h1>Committee</h1>
            <div class="profile">
                <div class="profile-icon">👤</div>
                <span>User's Name</span>
                <button>Sign Out</button>
            </div>
        </div>

        <div class="container">
            <h2>Committee Management</h2>
            <p>Register a new committee member for an event</p>

            <form method="POST">
                <label>Event Name</label>
                <select name="event_id" required>
                    <option value="">Select Event</option>
                    <?php while ($row = $events->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['event_name'] ?></option>
                    <?php endwhile; ?>
                </select>

                <label>Student Name</label>
                <input type="text" name="student_name" required>

                <label>Student ID</label>
                <input type="text" name="student_id" required>

                <label>Role</label>
                <select name="role" required>
                    <option value="">Select Role</option>
                    <option value="Chairperson">Chairperson</option>
                    <option value="Secretary">Secretary</option>
                    <option value="Treasurer">Treasurer</option>
                    <option value="Technical">Technical</option>
                    <option value="Logistics">Logistics</option>
                </select>

                <div class="btn-group">
                    <button type="reset">Cancel</button>
                    <button class="btn-submit" type="submit">Add Member</button>
                </div>
            </form>

            <?php if (!empty($success)): ?>
                <p style="margin-top: 20px; font-weight: bold; color: green;">✅ Committee member added!</p>
                <a href="merit_applications.php?event_id=<?= $event_id ?>&type=Committee">
                    <button style="margin-top: 10px;">Apply for Merit</button>
                </a>
            <?php endif; ?>

            <h2 style="margin-top: 40px;">Committee Members</h2>
            <table>
                <tr>
                    <th>Event</th>
                    <th>Name</th>
                    <th>Student ID</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $committees->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['event_name'] ?></td>
                    <td><?= $row['student_name'] ?></td>
                    <td><?= $row['student_id'] ?></td>
                    <td><?= $row['role'] ?></td>
                    <td><a href="delete_committee.php?id=<?= $row['id'] ?>"><button class="delete">Delete</button></a></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
