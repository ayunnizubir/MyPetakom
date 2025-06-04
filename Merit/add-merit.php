<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Merit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f1f1;
            padding: 40px;
        }
        .container {
            background: #fff;
            padding: 30px;
            width: 500px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }
        h2 {
            text-align: center;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }
        input[type="text"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #aaa;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            width: 100%;
            border-radius: 5px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .success {
            color: green;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Add Merit</h2>
    <?php if (isset($_GET['success'])): ?>
        <p class="success">Merit successfully added!</p>
    <?php endif; ?>
    <form action="process-add-merit.php" method="POST">
        <label for="matric_id">Matric ID</label>
        <input type="text" name="matric_id" required>

        <label for="event_name">Event Name</label>
        <input type="text" name="event_name" required>

        <label for="date">Date</label>
        <input type="date" name="date" required>

        <label for="organizer">Organizer</label>
        <input type="text" name="organizer" required>

        <label for="position">Position</label>
        <input type="text" name="position">

        <label for="level">Level</label>
        <select name="level" required>
            <option value="College">College</option>
            <option value="University">University</option>
            <option value="State">State</option>
            <option value="National">National</option>
            <option value="International">International</option>
        </select>

        <label for="marks">Marks</label>
        <input type="text" name="marks" required>

        <input type="submit" value="Add Merit">
    </form>
</div>
</body>
</html>
