<?php
session_start();
include 'db_connect.php';

$username = $_POST['username'];
$password = md5($_POST['password']);

$query = "SELECT * FROM students WHERE username = '$username' AND password = '$password'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 1) {
    $_SESSION['student_id'] = mysqli_fetch_assoc($result)['id'];
    header('Location: student_dashboard.php');
} else {
    echo "Invalid username or password.";
}

mysqli_close($conn);
?>
