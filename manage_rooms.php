<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Rooms</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>🏥 Manage Hospital Rooms</h2>
    <a href="dashboard.php">← Back to Dashboard</a>
    <hr>

    <form method="POST">
        <h3>Add a New Room</h3>
        <label>Room Number (e.g., 101A):</label><br>
        <input type="text" name="room_number" required><br>
        
        <label>Floor Number:</label><br>
        <input type="number" name="floor_number" required><br>
        
        <input type="hidden" name="hospital_id" value="1">
        
        <button type="submit" name="add_room">Add Room to Database</button>
    </form>

    <?php
    if (isset($_POST['add_room'])) {
        $room_no = $_POST['room_number'];
        $floor = $_POST['floor_number'];
        $hosp_id = $_POST['hospital_id'];

        // Insert the room securely using prepared statements
        $stmt = $conn->prepare("INSERT INTO rooms (room_number, floor_number, hospital_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $room_no, $floor, $hosp_id);
        
        if ($stmt->execute()) {
            echo "<p style='color: green; margin-top: 10px;'>✅ Room added successfully!</p>";
        } else {
            echo "<p style='color: red; margin-top: 10px;'>❌ Error adding room.</p>";
        }
        $stmt->close();
    }
    ?>

    <hr>
    
    <h3>Available Rooms</h3>
    <ul>
    <?php
    $result = $conn->query("SELECT * FROM rooms");
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Added htmlspecialchars for XSS protection
            echo "<li><strong>Room: " . htmlspecialchars($row['room_number']) . "</strong> (Floor: " . htmlspecialchars($row['floor_number']) . ")</li>";
        }
    } else {
        echo "<li>No rooms found in the database.</li>";
    }
    ?>
    </ul>
</body>
</html>