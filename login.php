<?php
session_start();
include 'db_connect.php'; //include your database connection

//check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); //redirect to user dashboard
    exit();
}

//initialize error message
$error = '';

//handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    //prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    //check if a user was found
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        //verify the password
        if (password_verify($password, $row['password'])) {
            session_regenerate_id(true); //regenerate session ID for security
            $_SESSION['user_id'] = $row['user_id']; //store user ID in session

            header("Location: dashboard.php");
            
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>
    <header>
        <nav>
            <ul class="navbar">
                <li><a href="home.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1>Login</h1>
        <?php if (!empty($error)) : ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="login.php" method="post">
            <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" class="button">Login</button>
        </form>
    </div>
</body>
</html>