<?php
$conn = new mysqli("localhost", "root", "", "db_registration");
$result = $conn->query("SELECT id, event_name FROM events WHERE status = 'Approved'");
?>

<h2>Merit Application</h2>

<form method="POST" action="submit_merit.php">
    <label for="event_id">Select Approved Event:</label>
    <select name="event_id" required>
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . $row['id'] . '">' . $row['event_name'] . '</option>';
            }
        } else {
            echo '<option disabled>No approved events</option>';
        }
        ?>
    </select><br>

    <label>Type:</label>
    <input type="text" name="type" required><br>

    <input type="submit" value="Apply">
</form>
