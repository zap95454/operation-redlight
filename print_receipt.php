<?php
session_start();
include 'db.php';
if (!isset($_GET['id'])) die("No appointment ID provided.");

$apt_id = $_GET['id'];
$query = "SELECT a.serial_number, a.appointment_date, p.name as pname, p.age, d.name as dname, d.specialization, h.name as hname, r.room_number 
          FROM Appointments a 
          JOIN Patients p ON a.patient_id = p.patient_id 
          JOIN Doctors d ON a.doctor_id = d.doctor_id 
          LEFT JOIN Hospitals h ON d.hospital_id = h.hospital_id
          LEFT JOIN Rooms r ON a.room_id = r.room_id
          WHERE a.appointment_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $apt_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
if(!$data) die("Invalid Appointment.");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Print Receipt</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; padding: 40px; color: #333; background: #f4f7f6; }
        .receipt-box { border: 2px dashed #2c3e50; padding: 30px; max-width: 500px; margin: 0 auto; background: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #3498db; padding-bottom: 15px; margin-bottom: 20px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 12px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .serial { font-size: 28px; font-weight: bold; text-align: center; background: #ebf5fb; color: #2980b9; padding: 15px; margin: 20px 0; border-radius: 5px; }
        @media print { .no-print { display: none; } body { background: white; padding: 0; } .receipt-box { box-shadow: none; border: 2px solid #000; } }
    </style>
</head>
<body>

<div class="receipt-box">
    <div class="header">
        <h2 style="margin:0; color:#1e3c72;">🏥 <?php echo htmlspecialchars($data['hname'] ?? 'Operation Redlight'); ?></h2>
        <p style="margin:5px 0; color:#7f8c8d;">Official Appointment Receipt</p>
    </div>
    
    <div class="row"><span><strong>Date:</strong></span> <span><?php echo htmlspecialchars($data['appointment_date']); ?></span></div>
    <div class="row"><span><strong>Patient:</strong></span> <span><?php echo htmlspecialchars($data['pname']); ?> (Age: <?php echo htmlspecialchars($data['age']); ?>)</span></div>
    <div class="row"><span><strong>Doctor:</strong></span> <span><?php echo htmlspecialchars($data['dname']); ?></span></div>
    <div class="row"><span><strong>Specialization:</strong></span> <span><?php echo htmlspecialchars($data['specialization']); ?></span></div>
    <div class="row"><span><strong>Room Number:</strong></span> <span style="color:#e74c3c; font-weight:bold;"><?php echo htmlspecialchars($data['room_number'] ?? '45B'); ?></span></div>
    
    <div class="serial">Serial: #<?php echo htmlspecialchars($data['serial_number']); ?></div>
    
    <p style="text-align:center; font-size: 12px; color:#95a5a6; margin-top:20px;">Please present this digital or printed receipt at the hospital reception.</p>

    <div class="no-print" style="text-align: center; margin-top: 30px;">
        <button onclick="window.print()" style="padding: 12px 25px; background: #2ecc71; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight:bold;">🖨️ Print Now</button>
    </div>
</div>

</body>
</html>