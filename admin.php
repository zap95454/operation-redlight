<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
include 'header.php';
include 'db.php'; 
?>
<link rel="stylesheet" href="style.css">

<div class="container">
<h2>⚙️ Admin Panel</h2>

<div class="card">
<h3>Add Hospital</h3>
<form method="POST">
<input name="hname" placeholder="Hospital Name" required>
<input name="hloc" placeholder="Location" required>
<button class="btn">Add</button>
</form>
</div>

<div class="card">
<h3>Add Doctor</h3>
<form method="POST">
<input name="dname" placeholder="Doctor Name" required>
<input name="spec" placeholder="Specialization" required>

<select name="hid" required>
<?php
$res=$conn->query("SELECT * FROM Hospitals");
while($h=$res->fetch_assoc()){
    echo "<option value='{$h['hospital_id']}'>{$h['name']}</option>";
}
?>
</select>

<button class="btn">Add Doctor</button>
</form>
</div>

<?php
// Insert Hospital Securely (SQL Injection Prevented)
if(isset($_POST['hname']) && isset($_POST['hloc'])){
    $stmt = $conn->prepare("INSERT INTO Hospitals (name, location) VALUES (?, ?)");
    $stmt->bind_param("ss", $_POST['hname'], $_POST['hloc']);
    if($stmt->execute()) {
        echo "<p style='color: green; margin-top: 10px;'>✅ Hospital added successfully!</p>";
    }
    $stmt->close();
}

// Insert Doctor Securely (SQL Injection Prevented & Schema Matched)
if(isset($_POST['dname']) && isset($_POST['spec']) && isset($_POST['hid'])){
    $stmt = $conn->prepare("INSERT INTO Doctors (name, specialization, hospital_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $_POST['dname'], $_POST['spec'], $_POST['hid']);
    if($stmt->execute()) {
        echo "<p style='color: green; margin-top: 10px;'>✅ Doctor added successfully!</p>";
    }
    $stmt->close();
}
?>
</div>