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

    <h1>Dashboard</h1>

<form method="POST">
    <input type="text" name="search" placeholder="Search books...">
    <button type="submit">Search</button>
</form>

<br>

<form method='POST'>
    <input type='hidden' name='userCheckedOut'>
    <button type='submit' name='userCheckedOut'>View My Checked Out Books</button>
</form>

<br>

<?php
    $all_books = "SELECT * FROM books ORDER BY title ASC, author ASC"; //sql query
    $default_result = mysqli_query($conn, $all_books); //fetch the result
    $flag = "normDisplay";
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['search'])) {
            $search = htmlspecialchars($_POST['search']);
            $search_sql = "SELECT * FROM books WHERE title LIKE '%$search%' OR author LIKE '%$search%' OR genre LIKE '%$search%'ORDER BY title ASC, author ASC";
            $search_result = $conn->query($search_sql);
            $default_result = $search_result;
        }

        if (isset($_POST['checkout'])) {
            $user_id = $_SESSION['user_id'];
            $book_id = $_POST['book_id'];

            $same_book_sql = "SELECT * FROM checked_out WHERE user_id = $user_id AND book_id = $book_id"; //checks if user has already checked out the same book

            if(mysqli_query($conn, $same_book_sql)->num_rows > 0){
                echo "<script>alert('You cannot check out the same book multiple times!');</script>"; //change this from alert to something else, works for now
            } else{
                $checkout_sql = "INSERT INTO checked_out (user_id, book_id) VALUES ($user_id, $book_id)";
                mysqli_query($conn, $checkout_sql);
    
                $update_checkout_sql = "UPDATE books SET copies_available = copies_available - 1 WHERE book_id = $book_id";
                mysqli_query($conn, $update_checkout_sql);
    
                header("Location: dashboard.php");
            }
        }

        if (isset($_POST['turnIn'])) {
            $user_id = $_SESSION['user_id'];
            $book_id = $_POST['book_id'];

            $turnin_sql = "DELETE FROM checked_out WHERE user_id = $user_id AND book_id = $book_id LIMIT 1";
            mysqli_query($conn, $turnin_sql);

            $update_turnin_sql = "UPDATE books SET copies_available = copies_available + 1 WHERE book_id = $book_id";
            mysqli_query($conn, $update_turnin_sql);

            $user_checked_out_sql = "SELECT b.book_id, b.title, b.author, b.description, b.length, b.genre, b.image, b.copies_available, c.due_date 
            FROM checked_out c JOIN books b ON c.book_id = b.book_id WHERE c.user_id = $user_id";
            $user_checked_out_result = $conn->query($user_checked_out_sql);
            $flag = "userCheckOutDisplay";
            $default_result = $user_checked_out_result;
           
        }

        if (isset($_POST['userCheckedOut'])) {
            $user_id = $_SESSION['user_id'];
            
            $user_checked_out_sql = "SELECT b.book_id, b.title, b.author, b.description, b.length, b.genre, b.image, b.copies_available, c.due_date 
            FROM checked_out c JOIN books b ON c.book_id = b.book_id WHERE c.user_id = $user_id";
            $user_checked_out_result = $conn->query($user_checked_out_sql);
            $flag = "userCheckOutDisplay";
            $default_result = $user_checked_out_result;
        }
    }

    displayTable($default_result, $flag);
?>

<?php
    function displayTable($result, $flag) {
        if ($result->num_rows > 0 && $flag == "normDisplay") {
            echo "<table border='1'>"; //table start

            //table heading
            echo "<tr>";
            echo "<th>" . "Title" . "</th>";
            echo "<th>" . "Author" . "</th>";
            echo "<th>" . "Description" . "</th>";
            echo "<th>" . "Page Count" . "</th>";
            echo "<th>" . "Genre" . "</th>";
            echo "<th>" . "Image" . "</th>";
            echo "<th>" . "Copies Available" . "</th>";
            echo "<th>" . "Status" . "</th>";
            echo "<th>" . "Checkout" . "</th>";
            echo "<tr>";

            while ($row = mysqli_fetch_assoc($result)) { 
                echo "<tr>";
                echo "<td>{$row['title']}</td>";
                echo "<td>{$row['author']}</td>";
                echo "<td>{$row['description']}</td>";
                echo "<td>{$row['length']}</td>";
                echo "<td>{$row['genre']}</td>";
                echo "<td>{$row['image']}</td>"; // echo "<td><img src='{$row['image']}'></td>"; -> replace with to display images
                echo "<td>{$row['copies_available']}</td>";
                if ($row['copies_available'] > 0) {
                    echo "<td>Available</td>";
                    echo "<td><form method='POST'>
                                <input type='hidden' name='book_id' value='{$row['book_id']}'>
                                <button type='submit' name='checkout'>Checkout</button>
                    </form></td>";
                } else {
                    echo "<td>All copies checked out!</td>";
                    echo "<td>Checkout unavailable</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } elseif($result->num_rows > 0 && $flag == "userCheckOutDisplay"){
            echo "<table border='1'>";
            echo "<tr>";
            echo "<th>" . "Title" . "</th>";
            echo "<th>" . "Author" . "</th>";
            echo "<th>" . "Description" . "</th>";
            echo "<th>" . "Page Count" . "</th>";
            echo "<th>" . "Genre" . "</th>";
            echo "<th>" . "Image" . "</th>";
            echo "<th>" . "Due Date" . "</th>";
            echo "<th>" . "Turn In" . "</th>";
            echo "<tr>";

            while ($row = mysqli_fetch_assoc($result)) { 
                echo "<tr>";
                echo "<td>{$row['title']}</td>";
                echo "<td>{$row['author']}</td>";
                echo "<td>{$row['description']}</td>";
                echo "<td>{$row['length']}</td>";
                echo "<td>{$row['genre']}</td>";
                echo "<td>{$row['image']}</td>"; // echo "<td><img src='{$row['image']}'></td>"; -> replace with to display images
                echo "<td>{$row['due_date']}</td>";
                echo "<td><form method='POST'>
                            <input type='hidden' name='book_id' value='{$row['book_id']}'>
                            <button type='submit' name='turnIn'>Turn In</button>
                </form></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "You currently have no checked out books!";
        }
    }
	$conn->close();
?>
</body>
</html>
