<?php
session_start();

// Database configuration
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "exam_management";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputUsername = $_POST['username'];
    $inputPassword = md5($_POST['password']); // Hash password with MD5

    // Query to check admin credentials
    $sql = "SELECT * FROM admin WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $inputUsername, $inputPassword);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        $_SESSION['admin_id'] = $admin['id']; // Save admin ID in session
        header("Location: admin_dashboard.php"); // Redirect to admin dashboard
        exit();
    } else {
        $error = "Invalid username or password.";
    }

    $stmt->close();
}
$conn->close();
?>