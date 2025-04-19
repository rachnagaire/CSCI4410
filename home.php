<?php
session_start();

//check if the user is already logged in 
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Library Database Management</title>
</head>
<body>
    <h1>Welcome to the Library Database Management</h1>
    <a href="login.php" class="button">Login</a>
    <a href="register.php" class="button">Register</a>
</body>
</html>
