<?php
include 'db_connect.php';

$name = $_POST['name'];
$department = $_POST['department'];
$email = $_POST['email'];
$username = $_POST['username'];
$password = md5($_POST['password']);

$query = "INSERT INTO faculty (name, department, email, username, password) VALUES ('$name', '$department', '$email', '$username', '$password')";
if (mysqli_query($conn, $query)) {
    echo "Registration successful. <a href='faculty_login.html'>Login</a>";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>