<?php 
include 'header.php'; 
include 'db.php'; 

// Ensure session is started and user is logged in
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['patient_id'])) { header("Location: login.php"); exit(); }
?>

<div class="container" style="max-width: 600px;">
    <div class="card">
        <h2 style="color: #2c3e50; margin-top: 0;">📅 Book an Appointment</h2>
        <p style="color: #7f8c8d; margin-bottom: 25px;">Patient ID: #<?php echo $_SESSION['patient_id']; ?></p>
        
        <?php 
        // Gender filter logic for female patients
        $isFemale = (isset($_SESSION['gender']) && $_SESSION['gender'] === 'Female'); 
        $filterOn = isset($_GET['female_only']) && $_GET['female_only'] == '1';
        ?>

        <?php if($isFemale): ?>
            <div style="background: #fdf2e9; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fab1a0;">
                <form method="GET" id="filterForm">
                    <label style="margin:0; cursor:pointer; font-weight: 600; color: #d63031;">
                        <input type="checkbox" name="female_only" value="1" <?php if($filterOn) echo 'checked'; ?> onchange="document.getElementById('filterForm').submit();"> 
                        🌸 Show Female Doctors Only 
                    </label>
                </form>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Select Your Doctor</label>
                <select name="doctor_id" required>
                    <option value="">-- Choose a Doctor --</option>
                    <?php
                    // Fetch doctors based on gender filter if applied
                    $sql = "SELECT d.*, h.name as hosp_name FROM Doctors d LEFT JOIN Hospitals h ON d.hospital_id = h.hospital_id";
                    if ($isFemale && $filterOn) { $sql .= " WHERE d.gender = 'Female'"; }
                    
                    $docs = $conn->query($sql);
                    while($d = $docs->fetch_assoc()){
                        echo "<option value='{$d['doctor_id']}'>{$d['name']} ({$d['specialization']}) - {$d['hosp_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success" style="width:100%; font-size: 16px;">Confirm Appointment</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $patient = $_SESSION['patient_id'];
            $doctor = $_POST['doctor_id'];
            
            // Start Transaction to handle serial number increment safely
            $conn->begin_transaction();
            try {
                // 1. Calculate next Serial Number for this specific doctor
                $stmt = $conn->prepare("SELECT MAX(serial_number) as max_serial FROM Appointments WHERE doctor_id = ? FOR UPDATE");
                $stmt->bind_param("i", $doctor);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                $serial = ($row['max_serial'] ? $row['max_serial'] : 0) + 1;
                $stmt->close();

                // 2. Select a Random Room from the doctor's hospital
                $room_stmt = $conn->prepare("SELECT room_id FROM Rooms WHERE hospital_id = (SELECT hospital_id FROM Doctors WHERE doctor_id = ?) ORDER BY RAND() LIMIT 1");
                $room_stmt->bind_param("i", $doctor);
                $room_stmt->execute();
                $room_res = $room_stmt->get_result();
                $room_id = ($room_res->num_rows > 0) ? $room_res->fetch_assoc()['room_id'] : NULL;
                $room_stmt->close();

                // 3. Insert new appointment with incremented serial
                $insert_stmt = $conn->prepare("INSERT INTO Appointments (patient_id, doctor_id, room_id, serial_number, appointment_date, status) VALUES (?, ?, ?, ?, CURDATE(), 'Pending')");
                $insert_stmt->bind_param("iiii", $patient, $doctor, $room_id, $serial);
                $insert_stmt->execute();
                $new_id = $conn->insert_id;
                $insert_stmt->close();
                
                $conn->commit(); // Save changes

                echo "<div style='margin-top:25px; padding: 20px; background: #e8f8f5; border-left: 5px solid #2ecc71; text-align: center; border-radius: 8px;'>
                        <h3 style='color: #27ae60; margin-bottom: 10px;'>✅ Appointment Booked!</h3>
                        <p>Your Serial Number is: <strong style='font-size: 24px; color: #2c3e50;'>#$serial</strong></p>
                        <br>
                        <a href='print_receipt.php?id=$new_id' target='_blank' class='btn' style='background: #3498db;'>🖨️ View & Print Receipt</a>
                      </div>";

            } catch (Exception $e) {
                $conn->rollback(); // Cancel changes on error
                echo "<p style='color:red; margin-top: 20px; text-align: center;'>❌ Error booking appointment. Please try again.</p>";
            }
        }
        ?>
    </div>
</div>