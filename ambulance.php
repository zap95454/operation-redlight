<?php 
include 'header.php'; 
include 'db.php'; 

// Ensure session is started and patient is logged in
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['patient_id'])) { header("Location: login.php"); exit(); }
?>

<div class="container">
    <h2 style="border-bottom: 2px solid #e74c3c; padding-bottom: 10px; margin-bottom: 30px;">🚑 Emergency Ambulance Fleet</h2>
    
    <?php
    // Handle the Booking/Dispatch Request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amb_id'])) {
        $amb_id = (int)$_POST['amb_id'];
        $patient_id = $_SESSION['patient_id'];
        
        $conn->begin_transaction();
        try {
            // 1. Check if the ambulance is still available to prevent double-booking
            $check = $conn->query("SELECT status FROM Ambulances WHERE ambulance_id = $amb_id FOR UPDATE");
            $status = $check->fetch_assoc()['status'];

            if ($status === 'Available') {
                // 2. Update Ambulance Status to Busy
                $conn->query("UPDATE Ambulances SET status='Busy' WHERE ambulance_id = $amb_id");

                // 3. Optional: Insert record into emergency_requests if you want to track who booked it
                $stmt = $conn->prepare("INSERT INTO emergency_requests (patient_id, ambulance_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $patient_id, $amb_id);
                $stmt->execute();

                $conn->commit();
                echo "<div class='card' style='background: #e8f8f5; border-left: 5px solid #2ecc71; color: #27ae60; padding: 15px; margin-bottom: 20px; text-align: center;'>
                        ✅ <strong>Success!</strong> Ambulance #$amb_id has been dispatched to your location.
                      </div>";
            } else {
                echo "<div class='card' style='background: #ffebee; border-left: 5px solid #e74c3c; color: #c62828; padding: 15px; margin-bottom: 20px; text-align: center;'>
                        ❌ This ambulance was just taken by another request. Please choose another.
                      </div>";
            }
        } catch (Exception $e) {
            $conn->rollback();
            echo "<p style='color:red;'>Database error. Please try again.</p>";
        }
    }
    ?>

    <div class="cards-grid">
        <?php
        // Fetch all ambulances and their associated hospital names
        $ambs = $conn->query("SELECT a.*, h.name as hosp_name FROM Ambulances a LEFT JOIN Hospitals h ON a.hospital_id = h.hospital_id ORDER BY a.status DESC");
        
        if ($ambs->num_rows > 0) {
            while($a = $ambs->fetch_assoc()) {
                $isAvailable = ($a['status'] == 'Available');
                $statusColor = $isAvailable ? '#2ecc71' : '#e74c3c';
                
                echo "<div class='card' style='border-top: 5px solid $statusColor;'>";
                echo "<h3 style='margin-top:0;'>Ambulance Unit #{$a['ambulance_id']}</h3>";
                echo "<p><strong>Driver:</strong> " . htmlspecialchars($a['driver_name']) . "</p>";
                echo "<p><strong>Base Hospital:</strong> " . htmlspecialchars($a['hosp_name'] ?? 'General Emergency') . "</p>";
                echo "<p><strong>Current Status:</strong> <span style='color: $statusColor; font-weight: bold;'>{$a['status']}</span></p>";
                
                if ($isAvailable) {
                    echo "<form method='POST' style='margin-top:15px;'>
                            <input type='hidden' name='amb_id' value='{$a['ambulance_id']}'>
                            <button type='submit' class='btn btn-danger' style='width:100%; font-weight:bold;'>Dispatch Now</button>
                          </form>";
                } else {
                    echo "<button class='btn' style='width:100%; background:#bdc3c7; cursor:not-allowed; margin-top:15px;' disabled>Unavailable</button>";
                }
                echo "</div>";
            }
        } else {
            echo "<p class='text-center'>No ambulances found in the system.</p>";
        }
        ?>
    </div>
</div>