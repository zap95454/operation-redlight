<?php
include 'header.php';
include 'db.php';

// Check if user is logged in as a patient
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') { 
    header("Location: login.php"); 
    exit(); 
}

$pid = $_SESSION['patient_id'];

// Handle Profile Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $age = (int)$_POST['age'];
    $gender = $_POST['gender'];

    $stmt = $conn->prepare("UPDATE Patients SET age=?, gender=? WHERE patient_id=?");
    $stmt->bind_param("isi", $age, $gender, $pid);
    
    if($stmt->execute()) {
        $_SESSION['gender'] = $gender; // Keep session in sync with DB
        $msg = "Profile updated successfully!";
    }
    $stmt->close();
}

// Fetch current patient data
$res = $conn->query("SELECT * FROM Patients WHERE patient_id = $pid");
$pat = $res->fetch_assoc();
?>

<div class="container">
    <div class="grid-2">
        <div class="card">
            <h3>👤 Personal Information</h3>
            <p style="color: #7f8c8d; margin-bottom: 20px;">Update your basic details here.</p>
            
            <?php if(isset($msg)) echo "<p style='color:green; font-weight:bold; margin-bottom:15px;'>✅ $msg</p>"; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" value="<?php echo htmlspecialchars($pat['name']); ?>" disabled style="background:#eee;">
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="age" value="<?php echo htmlspecialchars($pat['age'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="Male" <?php if($pat['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if($pat['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                        <option value="Other" <?php if($pat['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                    </select>
                </div>
                <button type="submit" name="update_profile" class="btn btn-success" style="width:100%;">Save Changes</button>
            </form>
        </div>

        <div class="card" style="border-top-color: #3498db;">
            <h3>📅 My Appointment History</h3>
            <p style="color: #7f8c8d; margin-bottom: 20px;">View and print your previous receipts.</p>

            <div style="overflow-x: auto;">
                <table class="data-table" style="font-size: 14px;">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Doctor</th>
                            <th>Status</th>
                            <th>Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT a.appointment_id, a.appointment_date, a.status, d.name as dname 
                                  FROM Appointments a 
                                  JOIN Doctors d ON a.doctor_id = d.doctor_id 
                                  WHERE a.patient_id = $pid 
                                  ORDER BY a.appointment_date DESC";
                        $history = $conn->query($query);

                        if($history->num_rows > 0) {
                            while($row = $history->fetch_assoc()) {
                                $statusColor = ($row['status'] == 'Completed') ? '#27ae60' : (($row['status'] == 'Cancelled') ? '#e74c3c' : '#f39c12');
                                echo "<tr>
                                        <td>{$row['appointment_date']}</td>
                                        <td><strong>{$row['dname']}</strong></td>
                                        <td style='color:$statusColor; font-weight:bold;'>{$row['status']}</td>
                                        <td>
                                            <a href='print_receipt.php?id={$row['appointment_id']}' target='_blank' 
                                               style='color:#3498db; text-decoration:underline; font-weight:bold;'>
                                               🖨️ View/Print
                                            </a>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>No bookings found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>