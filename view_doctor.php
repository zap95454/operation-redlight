<?php 
include 'header.php'; 
include 'db.php'; 

if(!isset($_GET['id'])) { header("Location: doctors.php"); exit(); }
$did = (int)$_GET['id'];

$res = $conn->query("SELECT d.*, h.name as hname, h.location FROM Doctors d LEFT JOIN Hospitals h ON d.hospital_id = h.hospital_id WHERE d.doctor_id = $did");
$d = $res->fetch_assoc();
?>

<div style="max-width: 800px; margin: 40px auto; padding: 20px;">
    <div class="card" style="display: flex; gap: 30px; align-items: center; border-top-color: #3498db; padding:30px; background:white; border-radius:10px;">
        <img src="uploads/<?php echo $d['profile_pic'] ?? 'default_doc.png'; ?>" style="width: 180px; height: 180px; border-radius: 15px; object-fit: cover; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <div>
            <h1 style="margin: 0; color: #1e3c72;"><?php echo htmlspecialchars($d['name']); ?></h1>
            <p style="font-size: 18px; color: #3498db; font-weight: bold; margin: 10px 0;"><?php echo htmlspecialchars($d['specialization']); ?></p>
            <p><strong>Experience:</strong> <?php echo $d['experience_years']; ?> Years</p>
            <p><strong>Hospital:</strong> <?php echo htmlspecialchars($d['hname']); ?></p>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-top:20px;">
        <div class="card" style="padding:25px; background:white; border-radius:10px;">
            <h3>📖 Professional Bio</h3>
            <p style="color: #555; line-height: 1.6; font-size:14px;"><?php echo nl2br(htmlspecialchars($d['bio_data'] ?? 'No bio provided.')); ?></p>
        </div>
        <div class="card" style="padding:25px; background:white; border-radius:10px;">
            <h3>🏢 Current Role</h3>
            <p style="font-size:14px;"><strong>Position:</strong> <?php echo htmlspecialchars($d['position'] ?? 'Medical Consultant'); ?></p>
            <p style="font-size:14px;"><strong>Workplace:</strong> <?php echo htmlspecialchars($d['current_workplace'] ?? $d['hname']); ?></p>
            <br>
            <?php if(!empty($d['credentials_file'])): ?>
                <a href="uploads/<?php echo $d['credentials_file']; ?>" target="_blank" class="btn btn-success" style="width: 100%; background:#2ecc71; color:white; padding:10px; display:block; text-decoration:none; text-align:center; border-radius:5px;">View Verification Document</a>
            <?php endif; ?>
            <a href="appointments.php" class="btn" style="width: 100%; margin-top: 10px; display:block; padding:10px; text-decoration:none; text-align:center; background:#3498db; color:white; border-radius:5px;">Book Appointment</a>
        </div>
    </div>
</div>