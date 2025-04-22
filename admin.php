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
                
                $viewUsers_sql = "SELECT users.user_id, users.username, users.email,
                                books.title AS book_title, books.author AS book_author
                                FROM users LEFT JOIN checked_out ON users.user_id = checked_out.user_id
                                LEFT JOIN books ON checked_out.book_id = books.book_id
                                ORDER BY users.user_id";

                $default_result = $conn->query($viewUsers_sql);
            }

            if (isset($_POST['deleteUser'])) {
                $user_to_delete = $_POST['user_id'];
                $current_user = $_SESSION['user_id'];
            
                if ($user_to_delete == $current_user) {
                    echo "<script>alert('You cannot delete your own account.');</script>";
                } else {

                    //delete user checkouts
                    $delete_checkouts_sql = "DELETE FROM checked_out WHERE user_id = $user_to_delete";
                    $conn->query($delete_checkouts_sql);

                    //delete user
                    $delete_sql = "DELETE FROM users WHERE user_id = $user_to_delete";
                    if ($conn->query($delete_sql)) {
                        echo "<script>alert('User deleted successfully.');</script>";
                    } else {
                        echo "<script>alert('Failed to delete user.');</script>";
                    }
                }
                
                $viewUsers_sql = "SELECT users.user_id, users.username, users.email,
                                books.title AS book_title, books.author AS book_author
                                FROM users LEFT JOIN checked_out ON users.user_id = checked_out.user_id
                                LEFT JOIN books ON checked_out.book_id = books.book_id
                                ORDER BY users.user_id"; //refetch updated list
                $default_result = $conn->query($viewUsers_sql);
            }

            if (isset($_POST['modBookData'])) {

            }


        }

        displayTable($default_result, $conn);

        function displayTable($result) {
            if ($result){
                //group users and their books
                $users = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $userId = $row['user_id'];

                    if (!isset($users[$userId])) {
                        $users[$userId] = [
                            'user_id' => $userId,
                            'username' => $row['username'],
                            'email' => $row['email'],
                            'books' => [],
                        ];
                    }

                    if (!empty($row['book_title']) && !empty($row['book_author'])) {
                        $users[$userId]['books'][] = "{$row['book_title']} by {$row['book_author']}";
                    }
                }

                //display table
                echo "<table border='1'>";
                echo "<tr><th>User ID</th><th>Username</th><th>Email</th><th>Books Checked Out</th><th>Action</th></tr>";

                foreach ($users as $user) {
                    echo "<tr>";
                    echo "<td>{$user['user_id']}</td>";
                    echo "<td>{$user['username']}</td>";
                    echo "<td>{$user['email']}</td>";

                    echo "<td><ul>";
                    if (!empty($user['books'])) {
                        foreach ($user['books'] as $book) {
                            echo "<li>$book</li>";
                        }
                    } else {
                        echo "<li>No books checked out</li>";
                    }
                    echo "</ul></td>";

                    echo "<td>
                        <form method='POST' onsubmit=\"return confirm('Are you sure you want to delete this user?');\">
                            <input type='hidden' name='user_id' value='{$user['user_id']}'>
                            <button type='submit' name='deleteUser'>Delete User</button>
                        </form>
                    </td>";
                    echo "</tr>";
                }

                echo "</table>";
            }
        }
    
    ?>
</body>
</html>
