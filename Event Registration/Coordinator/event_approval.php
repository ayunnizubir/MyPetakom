<?php
$conn = new mysqli("localhost", "root", "", "db_registration");
$result = $conn->query("SELECT * FROM events WHERE status = 'Upcoming'");
?>

<h2>Approve or Reject Events</h2>

<table border="1">
    <tr>
        <th>Event Name</th>
        <th>Date</th>
        <th>Time</th>
        <th>Location</th>
        <th>Description</th>
        <th>Action</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['event_name'] ?></td>
            <td><?= $row['event_date'] ?></td>
            <td><?= $row['event_time'] ?></td>
            <td><?= $row['location'] ?></td>
            <td><?= $row['description'] ?></td>
            <td>
                <form action="update_status.php" method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="status" value="Approved">
                    <input type="submit" value="Approve">
                </form>
                <form action="update_status.php" method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="status" value="Rejected">
                    <input type="submit" value="Reject">
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
