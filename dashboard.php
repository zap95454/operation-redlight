<?php 
include 'header.php'; 
include 'db.php'; 

// 1. Authentication Check
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

// 2. Logic: Handle Doctor Appointment Status Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $apt_id = (int)$_POST['appointment_id'];
    $new_status = $_POST['status'];
    $did = $_SESSION['doctor_id'];

    $stmt = $conn->prepare("UPDATE Appointments SET status = ? WHERE appointment_id = ? AND doctor_id = ?");
    $stmt->bind_param("sii", $new_status, $apt_id, $did);
    $stmt->execute();
    $stmt->close();
}
?>

<div class="container">
    <header class="card" style="background: linear-gradient(135deg, #e0f2fe 0%, #ffffff 100%); border: 1px solid #bae6fd; border-radius: 12px; padding: 35px 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); position: relative; overflow: hidden; margin-bottom: 30px;">
        
        <div style="position: relative; z-index: 2;">
            <h2 style="margin: 0; color: #0369a1; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">Welcome.</h2>
            <p style="margin: 8px 0 0 0; color: #64748b; font-size: 16px;">Your central hub for healthcare management.</p>
        </div>

        <div style="position: absolute; right: 20px; top: -10px; opacity: 0.04; font-size: 140px; z-index: 1; transform: rotate(15deg); pointer-events: none;">
            ⚕️
        </div>
    </header>

    <?php if($_SESSION['role'] === 'patient'): ?>
        <div class="grid-2">
            
            <div class="card" style="border: 1px solid #e1e8ed; border-top: none; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                    <div style="background: #ebf5fb; color: #3498db; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-right: 15px;">👨‍⚕️</div>
                    <h3 style="margin: 0; color: #2c3e50;">Find a Specialist</h3>
                </div>
                <p style="color: #7f8c8d; font-size: 14px; margin-bottom: 20px;">Browse our directory of top-rated clinical specialists and surgeons.</p>
                <a href="doctors.php" class="btn" style="width: 100%; background: #ebf5fb; color: #2980b9; border: 1px solid #d6eaf8; transition: all 0.3s;">View Directory</a>
            </div>

            <div class="card" style="border: 1px solid #e1e8ed; border-top: none; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                    <div style="background: #e8f8f5; color: #2ecc71; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-right: 15px;">📅</div>
                    <h3 style="margin: 0; color: #2c3e50;">Book Appointment</h3>
                </div>
                <p style="color: #7f8c8d; font-size: 14px; margin-bottom: 20px;">Schedule an in-person visit and secure your digital serial number.</p>
                <a href="appointments.php" class="btn" style="width: 100%; background: #2ecc71; color: white;">Schedule Now</a>
            </div>

            <div class="card" style="border: 1px solid #e1e8ed; border-top: none; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                    <div style="background: #fdedec; color: #e74c3c; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-right: 15px;">🚑</div>
                    <h3 style="margin: 0; color: #2c3e50;">Emergency Fleet</h3>
                </div>
                <p style="color: #7f8c8d; font-size: 14px; margin-bottom: 20px;">Dispatch an ambulance to your location immediately. 24/7 Service.</p>
                <a href="ambulance.php" class="btn" style="width: 100%; background: #fdedec; color: #c0392b; border: 1px solid #fadbd8;">View Ambulances</a>
            </div>

            <div class="card" style="border: 1px solid #e1e8ed; border-top: none; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                    <div style="background: #fef9e7; color: #f1c40f; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-right: 15px;">⭐</div>
                    <h3 style="margin: 0; color: #2c3e50;">Rate Your Doctor</h3>
                </div>
                <p style="color: #7f8c8d; font-size: 14px; margin-bottom: 20px;">Help us improve by sharing your recent consultation experience.</p>
                <a href="reviews.php" class="btn" style="width: 100%; background: #fef9e7; color: #d4ac0d; border: 1px solid #fcf3cf;">Leave Feedback</a>
            </div>
        </div>

    <?php elseif($_SESSION['role'] === 'doctor'): ?>
        
        <div class="card" style="border: 1px solid #e1e8ed; border-top: none; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            
            <div style="display: flex; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f4f7f9; padding-bottom: 15px;">
                <div style="background: #ebf5fb; padding: 10px; border-radius: 8px; margin-right: 15px; font-size: 20px;">📋</div>
                <div>
                    <h3 style="margin: 0; color: #2c3e50;">Patient Consultations</h3>
                    <p style="margin: 3px 0 0 0; font-size: 13px; color: #95a5a6;">Manage your upcoming appointments and patient statuses.</p>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="data-table" style="font-size: 14px;">
                    <thead>
                        <tr>
                            <th style="color: #7f8c8d; font-weight: 600;">Date</th>
                            <th style="color: #7f8c8d; font-weight: 600;">Patient Name</th>
                            <th style="color: #7f8c8d; font-weight: 600;">Age</th>
                            <th style="color: #7f8c8d; font-weight: 600;">Gender</th>
                            <th style="color: #7f8c8d; font-weight: 600;">Status</th>
                            <th style="color: #7f8c8d; font-weight: 600;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $did = $_SESSION['doctor_id'];
                    $apts = $conn->query("SELECT a.*, p.name, p.age, p.gender FROM Appointments a JOIN Patients p ON a.patient_id = p.patient_id WHERE a.doctor_id = $did ORDER BY a.appointment_date DESC");
                    
                    if($apts->num_rows > 0){
                        while($a = $apts->fetch_assoc()){
                            // Clinical status colors
                            $statusColor = ($a['status'] == 'Completed') ? '#27ae60' : (($a['status'] == 'Cancelled') ? '#e74c3c' : '#f39c12');
                            $statusBg = ($a['status'] == 'Completed') ? '#eaeded' : (($a['status'] == 'Cancelled') ? '#fdedec' : '#fef9e7');
                            
                            echo "<tr style='transition: background 0.2s;' onmouseover=\"this.style.background='#f8f9fa'\" onmouseout=\"this.style.background='transparent'\">
                                    <td style='color: #555;'>{$a['appointment_date']}</td>
                                    <td style='font-weight: 500; color: #2c3e50;'>" . htmlspecialchars($a['name']) . "</td>
                                    <td style='color: #555;'>{$a['age']}</td>
                                    <td style='color: #555;'>{$a['gender']}</td>
                                    <td><span style='color:$statusColor; font-weight:600; background:$statusBg; padding: 4px 10px; border-radius: 12px; font-size: 12px;'>{$a['status']}</span></td>
                                    <td>
                                        <form method='POST' style='display:flex; gap:8px;'>
                                            <input type='hidden' name='appointment_id' value='{$a['appointment_id']}'>
                                            <select name='status' style='padding:6px; border: 1px solid #dcdde1; border-radius: 5px; font-size: 13px; color: #2c3e50;'>
                                                <option value='Pending' ".($a['status']=='Pending'?'selected':'').">Pending</option>
                                                <option value='Completed' ".($a['status']=='Completed'?'selected':'').">Completed</option>
                                                <option value='Cancelled' ".($a['status']=='Cancelled'?'selected':'').">Cancelled</option>
                                            </select>
                                            <button type='submit' name='update_status' class='btn' style='padding:6px 12px; background: #3498db; color: white; border-radius: 5px; font-size: 13px;'>Save</button>
                                        </form>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align: center; padding: 30px; color: #95a5a6;'>No appointments booked at this time.</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>