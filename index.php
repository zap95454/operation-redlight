<?php include 'header.php'; ?>
<link rel="stylesheet" href="style.css">

<div class="navbar">🏥 Operation Redlight</div>

<div class="container">

<h1>Welcome to Operation Redlight</h1>

<?php if(isset($_SESSION['user'])){ ?>

    <div class="card">
        <h3>👋 Hello <?=$_SESSION['user']?></h3>
        <a href="dashboard.php" class="btn">Go to Dashboard</a>
        <a href="logout.php" class="btn">Logout</a>
    </div>

<?php } else { ?>

    <div class="card">
        <h3>Login to Continue</h3>
        <a href="login.php" class="btn">Login</a>
        <a href="register.php" class="btn">Register</a>
    </div>

<?php } ?>

</div>