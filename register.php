<?php 
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
include 'header.php'; 
include 'db.php'; 

$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $raw_password = $_POST['password'];
    $gender = $_POST['gender'];
    
    // 1. Password Length Validation
    if (strlen($raw_password) < 6) {
        $error_msg = "Password must be at least 6 characters long.";
    } else {
        // 2. Email Uniqueness Validation (Check both tables)
        $check_stmt = $conn->prepare("SELECT email FROM Patients WHERE email = ? UNION SELECT email FROM Doctors WHERE email = ?");
        $check_stmt->bind_param("ss", $email, $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error_msg = "This email is already registered. Please login or use a different email.";
        } else {
            // 3. Proceed with Registration if validation passes
            $pass = password_hash($raw_password, PASSWORD_DEFAULT);
            
            if ($role === 'patient') {
                $age = $_POST['age'];
                $stmt = $conn->prepare("INSERT INTO Patients (name, email, password, gender, age) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssi", $name, $email, $pass, $gender, $age);
            } else {
                $hosp = $_POST['hospital_id'];
                $stmt = $conn->prepare("INSERT INTO Doctors (name, email, password, gender, hospital_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssi", $name, $email, $pass, $gender, $hosp);
            }
            
            if ($stmt->execute()) {
                header("Location: login.php?registered=success");
                exit();
            } else {
                $error_msg = "A database error occurred during registration.";
            }
        }
        $check_stmt->close();
    }
}
?>

<div class="container" style="max-width: 600px;">
    <div class="card">
        <h2 class="text-center">Create Account</h2>
        
        <?php if(!empty($error_msg)): ?>
            <div style="color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 20px;">
                ❌ <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="regForm">
            <div class="form-group">
                <label>Register As:</label>
                <select name="role" id="roleSelect" onchange="toggleFields()" required>
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                </select>
            </div>
            
            <div class="form-group"><label>Full Name</label><input name="name" type="text" required></div>
            <div class="form-group"><label>Email</label><input name="email" type="email" required></div>
            
            <div class="form-group"><label>Password</label><input name="password" type="password" minlength="6" placeholder="Minimum 6 characters" required></div>
            
            <div class="grid-2">
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group" id="ageField">
                    <label>Age</label>
                    <input name="age" type="number" id="ageInput" required>
                </div>
            </div>

            <div class="form-group" id="hospitalField" style="display:none;">
                <label>Assign to Hospital</label>
                <select name="hospital_id" id="hospInput">
                    <?php
                    $res=$conn->query("SELECT * FROM Hospitals");
                    while($h=$res->fetch_assoc()) echo "<option value='{$h['hospital_id']}'>{$h['name']}</option>";
                    ?>
                </select>
            </div>
            
            <button class="btn btn-success" style="width:100%;">Register</button>
        </form>
    </div>
</div>

<script>
function toggleFields() {
    let role = document.getElementById('roleSelect').value;
    if (role === 'doctor') {
        document.getElementById('ageField').style.display = 'none';
        document.getElementById('ageInput').required = false;
        document.getElementById('hospitalField').style.display = 'block';
    } else {
        document.getElementById('ageField').style.display = 'block';
        document.getElementById('ageInput').required = true;
        document.getElementById('hospitalField').style.display = 'none';
    }
}
</script>