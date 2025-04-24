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


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Font Awesome -->

<!-- MDB -->
<!-- <link
  href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/8.0.0/mdb.min.css"
  rel="stylesheet"
/> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.3.2/mdb.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./Assets/css/main.css">
</head>
<body>
<section class="signin">
   
        <div class="signin-content">
            <div class="img-holder">
                <figure><img src="/HospitalManagementSystem/Assets/Images/signin.jpg" class="img signin-img" alt="sign up image"></figure>
            </div>
            <?php if (!empty($error)) : ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

            <form id="loginForm" class="signin-form" method="post" action="login.php">
                <!-- Username input -->
                <a class="navbar-brand" href="/HospitalManagementSystem/index.php"><img src="/HospitalManagementSystem/Assets/Images/LOGO.png" alt="" width="50"> <span class="logo-text text-primary font-weight-bold">CIMS</span></a>
                <h3 > Sign In</h3>
                 <?php if (!empty($error)) : ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
                <p>Don't have an account ? <a href="signup.php">Sign Up Now.</a></p>
                <div class="form-outline mb-4">
                    <input type="text" id="username" name="username" class="form-control" required />
                    <label class="form-label" for="username">Username</label>
                </div>

                <!-- Password input -->
                <div class="form-outline mb-4">
                    <input type="password" id="password" name="password" class="form-control" required />
                    <label class="form-label" for="password">Password</label>
                </div>

                <!-- Remember me and Forgot password -->
                <div class="row mb-4 w-100">
                    <div class="col d-flex justify-content-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe" checked />
                            <label class="form-check-label" for="rememberMe"> Remember me </label>
                        </div>
                    </div>
                    <div class="col">
                        <a href="forget_password.php">Forgot password?</a>
                    </div>
                </div>

                <!-- Submit button -->
                <button type="submit" class="btn btn-primary btn-block mb-4">Sign in</button>
                <a href="home.php" class="link mx-auto">Go to Landing Page</a>
            </form>

        </div>
    
</section>

<script>
    $(document).ready(function () {
        $('#loginForm').validate({
            rules: {
                username: {
                    required: true,
                    minlength: 3
                },
                password: {
                    required: true,
                    minlength: 3
                }
            },
            messages: {
                username: {
                    required: 'Please enter your username.',
                    minlength: 'Username must be at least 3 characters long.'
                },
                password: {
                    required: 'Please enter your password.',
                    minlength: 'Password must be at least 3 characters long.'
                }
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
    });
</script>