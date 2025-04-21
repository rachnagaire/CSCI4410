<?php
session_start();
require 'db_connect.php';

//check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$servername = "localhost"; //create initial connection
$username = "root";
$password = "";
$database = "library_database"; 
$conn = new mysqli($servername, $username, $password, $database);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Dashboard</title>

</head>
<body>
    <header>
        <nav>
            <ul class="navbar">
                <li><a href="home.php">Home</a></li>
                <li><a href="admin.php">Librarian Managment Page</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <h1>Librarian Managment Page</h1>

    <form method="POST">
        <input type="hidden">
        <button type="submit" name="viewUsers">View All Users</button>
        <button type="submit" name="modBookData">Modify Book Database</button>
        <button type="submit" name="Analytics">Analytics</button>
    </form>
    
    <br>

    <?php
        $default_result= "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['viewUsers'])) {
                $viewUsers_sql = "SELECT * FROM users";
                $default_result = $conn->query($viewUsers_sql);
            }

            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteUser'])) {
                $user_to_delete = $_POST['user_id'];
                $current_user = $_SESSION['user_id'];
            
                if ($user_to_delete == $current_user) {
                    echo "<script>alert('You cannot delete your own account.');</script>";
                } else {
                    $delete_sql = "DELETE FROM users WHERE user_id = $user_to_delete";
                    if ($conn->query($delete_sql)) {
                        echo "<script>alert('User deleted successfully.');</script>";
                    } else {
                        echo "<script>alert('Failed to delete user.');</script>";
                    }
                }
                $viewUsers_sql = "SELECT * FROM users"; //refetch updated list
                $default_result = $conn->query($viewUsers_sql);
            }

            if (isset($_POST['modBookData'])) {

            }


        }

        displayTable($default_result);

        function displayTable($result){
            if($result){
                echo "<table border='1'>";
                echo "<tr>";
                echo "<th>" . "User ID" . "</th>";
                echo "<th>" . "Username" . "</th>";
                echo "<th>" . "Email" . "</th>";
                echo "<th>Action</th>";
                echo "<tr>";

                while ($row = mysqli_fetch_assoc($result)) { 
                    echo "<tr>";
                    echo "<td>{$row['user_id']}</td>";
                    echo "<td>{$row['username']}</td>";
                    echo "<td>{$row['email']}</td>";
                    echo "<td><form method='POST' onsubmit=\"return confirm('Are you sure you want to delete this user?');\">
                        <input type='hidden' name='user_id' value='{$row['user_id']}'>
                        <button type='submit' name='deleteUser'>Delete User</button>
                    </form></td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
    
    ?>

</body>
</html>
