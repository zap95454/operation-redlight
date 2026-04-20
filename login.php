<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db.php';

$error = "";
$success = "";

if (isset($_GET['registered']) && $_GET['registered'] == 'success') {
    $success = "Registration successful! Please login below.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check Patients Table
        $stmt = $conn->prepare("SELECT * FROM Patients WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $p_res = $stmt->get_result();
        $patient = $p_res->fetch_assoc();

        if ($patient && password_verify($password, $patient['password'])) {
            $_SESSION['user'] = $patient['name'];
            $_SESSION['patient_id'] = $patient['patient_id'];
            $_SESSION['gender'] = $patient['gender'];
            $_SESSION['role'] = 'patient';
            header("Location: dashboard.php"); exit(); 
        } 
        
        // Check Doctors Table if not a patient
        $stmt2 = $conn->prepare("SELECT * FROM Doctors WHERE email = ?");
        $stmt2->bind_param("s", $email);
        $stmt2->execute();
        $d_res = $stmt2->get_result();
        $doctor = $d_res->fetch_assoc();

        if ($doctor && password_verify($password, $doctor['password'])) {
            $_SESSION['user'] = $doctor['name'];
            $_SESSION['doctor_id'] = $doctor['doctor_id'];
            $_SESSION['role'] = 'doctor';
            header("Location: dashboard.php"); exit();
        }
        
        $error = "Invalid email or password.";
    }
}
include 'header.php'; 
?>

<div class="container" style="max-width: 400px;">
    <div class="card">
        <h2 class="text-center" style="margin-bottom: 20px;">Welcome</h2>
        
        <?php if(!empty($success)): ?>
            <div style="color: #155724; background-color: #d4edda; padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 15px;">
                ✅ <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if(!empty($error)): ?>
            <div style="color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 15px;">
                ❌ <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group"><label>Email</label><input name="email" type="email" required></div>
            <div class="form-group"><label>Password</label><input name="password" type="password" minlength="6" required></div>
            <button class="btn btn-success" style="width:100%;">Login</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px; font-size: 14px; color: #7f8c8d;">
            Don't have an account? <a href="register.php" style="color: #3498db; font-weight: bold; text-decoration: none;">Register here</a>
        </p>
    </div>
</div>