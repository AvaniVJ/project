<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header('Location: student_login.html');
    exit();
}

include 'db_connect.php';

// Fetch student details
$student_id = $_SESSION['student_id'];
$query = "
    SELECT s.name, s.usn, s.department, a.seat_number, r.room_name
    FROM students s
    LEFT JOIN allocations a ON s.id = a.student_id
    LEFT JOIN rooms r ON a.room_id = r.id
    WHERE s.id = $student_id
";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('Error fetching data: ' . mysqli_error($conn));
}

$student = mysqli_fetch_assoc($result);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Student Dashboard</h1>
    <h2>Student Details</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>USN</th>
            <th>Department</th>
        </tr>
        <tr>
            <td><?php echo htmlspecialchars($student['name']); ?></td>
            <td><?php echo htmlspecialchars($student['usn']); ?></td>
            <td><?php echo htmlspecialchars($student['department']); ?></td>
        </tr>
    </table>

    <h2>Seat Allocation</h2>
    <table>
        <tr>
            <th>Room Name</th>
            <th>Seat Number</th>
        </tr>
        <tr>
            <td>
                <?php
                if (isset($student['room_name'])) {
                    echo htmlspecialchars($student['room_name']);
                } else {
                    echo 'Seat not allocated yet';
                }
                ?>
            </td>
            <td>
                <?php
                if (isset($student['seat_number'])) {
                    echo htmlspecialchars($student['seat_number']);
                } else {
                    echo 'Seat not allocated yet';
                }
                ?>
            </td>
        </tr>
    </table>

    <a href="logout.php">Logout</a>
</body>
</html>
