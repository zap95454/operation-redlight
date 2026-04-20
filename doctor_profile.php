<?php
include 'header.php';
include 'db.php';
if ($_SESSION['role'] !== 'doctor') { die("Unauthorized"); }

$did = $_SESSION['doctor_id'];
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $spec = $_POST['specialization'];
    $exp = $_POST['experience'];
    $bio = $_POST['bio'];
    $work = $_POST['workplace'];
    $pos = $_POST['position'];
    
    // Create uploads directory if it doesn't exist to prevent warnings
    if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }

    // Handle Profile Picture Upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        $picName = "pic_" . $did . "_" . time() . "." . $ext;
        if(move_uploaded_file($_FILES['profile_pic']['tmp_name'], "uploads/" . $picName)) {
            $conn->query("UPDATE Doctors SET profile_pic='$picName' WHERE doctor_id=$did");
        }
    }

    // Handle Credentials File Upload
    if (isset($_FILES['credentials']) && $_FILES['credentials']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['credentials']['name'], PATHINFO_EXTENSION));
        $fileName = "doc_" . $did . "_" . time() . "." . $ext;
        if(move_uploaded_file($_FILES['credentials']['tmp_name'], "uploads/" . $fileName)) {
            $conn->query("UPDATE Doctors SET credentials_file='$fileName' WHERE doctor_id=$did");
        }
    }

    $stmt = $conn->prepare("UPDATE Doctors SET specialization=?, experience_years=?, bio_data=?, current_workplace=?, position=? WHERE doctor_id=?");
    $stmt->bind_param("sisssi", $spec, $exp, $bio, $work, $pos, $did);

    if ($stmt->execute()) { $msg = "Profile Updated Successfully!"; }
}

$res = $conn->query("SELECT * FROM Doctors WHERE doctor_id = $did");
$doc = $res->fetch_assoc();
?>

<div class="container" style="max-width: 700px;">
    <div class="card">
        <h2 style="text-align:center;">👨‍⚕️ Professional Profile & Credentials</h2>
        <?php if(!empty($msg)) echo "<p style='color:green; font-weight:bold; text-align:center;'>$msg</p>"; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div style="text-align: center; margin-bottom: 25px;">
                <img src="uploads/<?php echo $doc['profile_pic'] ?? 'default_doc.png'; ?>" style="width: 130px; height: 130px; border-radius: 50%; object-fit: cover; border: 3px solid #3498db; margin-bottom: 10px;">
                <label style="display: block; font-weight:bold;">Profile Picture</label>
                <input type="file" name="profile_pic" accept="image/*">
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group"><label>Specialization</label><input type="text" name="specialization" value="<?php echo htmlspecialchars($doc['specialization']); ?>" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;"></div>
                <div class="form-group"><label>Experience (Years)</label><input type="number" name="experience" value="<?php echo $doc['experience_years']; ?>" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;"></div>
            </div>
            
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top:15px;">
                <div class="form-group"><label>Current Workplace</label><input type="text" name="workplace" value="<?php echo htmlspecialchars($doc['current_workplace'] ?? ''); ?>" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;"></div>
                <div class="form-group"><label>Your Position</label><input type="text" name="position" value="<?php echo htmlspecialchars($doc['position'] ?? ''); ?>" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;"></div>
            </div>

            <div class="form-group" style="margin-top:15px;">
                <label>Professional Bio</label>
                <textarea name="bio" rows="4" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;"><?php echo htmlspecialchars($doc['bio_data'] ?? ''); ?></textarea>
            </div>

            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px dashed #3498db; margin-top:20px;">
                <label style="font-weight:bold;">📁 Upload Certificates (PDF/Images)</label>
                <input type="file" name="credentials" style="display:block; margin-top:10px;">
            </div>
            
            <button class="btn btn-success" style="width:100%; margin-top:20px; padding:12px;">Save Professional Profile</button>
        </form>
    </div>
</div>