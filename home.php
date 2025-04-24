<?php
session_start();


if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<<<<<<< Updated upstream
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Database Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>


<div class="navbar">
    <a href="home.php">Home</a>
    <a href="login.php">Login</a>
    <a href="register.php">Register</a>
</div>


<div class="auth-container">
    <h2>📚 Welcome to the Library Database Management System</h2>
    <p style="text-align:center;font-size:16px;color:#555;margin-bottom:25px;">
        Manage book check‑outs, explore the catalogue, and more.<br>
        Please log in or register to continue.
    </p>

   
    <div style="display:flex;justify-content:center;gap:20px;">
        <a href="login.php"><input type="button" value="Login"  class="checkout-btn"></a>
        <a href="register.php"><input type="button" value="Register" class="checkout-btn"></a>
    </div>
</div>

=======

<?php include 'header.php'; ?>
    <h1>Welcome to the Library Database Management</h1>
    <a href="login.php" class="button">Login</a>
    <a href="register.php" class="button">Register</a>
>>>>>>> Stashed changes
</body>
</html>
