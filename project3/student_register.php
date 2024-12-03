<?php
include 'db_connect.php';

$name = $_POST['name'];
$usn = $_POST['usn'];
$department = $_POST['department'];
$username = $_POST['username'];
$password = md5($_POST['password']);

$query = "INSERT INTO students (name, usn, department, username, password) VALUES ('$name', '$usn', '$department', '$username', '$password')";
if (mysqli_query($conn, $query)) {
    echo "Registration successful. <a href='student_login.html'>Login</a>";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>