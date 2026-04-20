<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operation Redlight</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

<nav class="navbar">
    <div class="brand">
        <a href="dashboard.php">
            🏥 Operation RedLight 
            <span class="authors">By Miraj, Asif, Riad</span>
        </a>
    </div>
    
    <div class="nav-links">
        <?php if(isset($_SESSION['role'])): ?>
            
            <a href="dashboard.php">Dashboard</a>
            
            <?php if($_SESSION['role'] === 'patient'): ?>
                <a href="doctors.php">Doctors</a>
                <a href="appointments.php">Appointments</a>
                <a href="ambulance.php">Emergency</a>
            <?php endif; ?>

            <div class="user-menu">
                <span class="user-badge">
                    👤 <?php echo htmlspecialchars($_SESSION['user']); ?> (<?php echo ucfirst($_SESSION['role']); ?>)
                </span>
                <div class="dropdown-content">
                    <?php if($_SESSION['role'] === 'doctor'): ?>
                        <a href="doctor_profile.php" class="edit-btn">Edit Profile</a>
                    <?php else: ?>
                        <a href="patient_profile.php" class="edit-btn">Edit Profile</a>
                    <?php endif; ?>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            </div>

        <?php else: ?>
            <a href="login.php" class="btn">Login</a>
        <?php endif; ?>
    </div>
</nav>