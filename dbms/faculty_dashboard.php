<?php
session_start();
if (!isset($_SESSION['faculty_id'])) {
    header('Location: faculty_login.html');
    exit();
}

include 'db_connect.php';

// Fetch faculty details and room allocations
$faculty_id = $_SESSION['faculty_id'];

// Fetch faculty details and room assignments
$query = "
    SELECT f.name, f.department, f.email, a.room_id, r.room_name
    FROM faculty f
    LEFT JOIN allocations a ON f.id = a.student_id  -- Changed to match correct foreign key
    LEFT JOIN rooms r ON a.room_id = r.id
    WHERE f.id = $faculty_id
";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('Error fetching data: ' . mysqli_error($conn));
}

$faculty = mysqli_fetch_assoc($result);

// Fetch invigilator details if the faculty is an invigilator
$invigilator_query = "
    SELECT r.room_name
    FROM invigilations i
    LEFT JOIN rooms r ON i.room_id = r.id
    WHERE i.teacher_id = $faculty_id
";
$invigilator_result = mysqli_query($conn, $invigilator_query);

if (!$invigilator_result) {
    die('Error fetching data: ' . mysqli_error($conn));
}

$invigilator_rooms = [];
while ($row = mysqli_fetch_assoc($invigilator_result)) {
    $invigilator_rooms[] = $row['room_name'];
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Faculty Dashboard</h1>
    <h2>Faculty Details</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Department</th>
            <th>Email</th>
        </tr>
        <tr>
            <td><?php echo htmlspecialchars($faculty['name']); ?></td>
            <td><?php echo htmlspecialchars($faculty['department']); ?></td>
            <td><?php echo htmlspecialchars($faculty['email']); ?></td>
        </tr>
    </table>

    <?php if (isset($faculty['room_name'])): ?>
    <h2>Examiner Room Assignment</h2>
    <table>
        <tr>
            <th>Room Name</th>
        </tr>
        <tr>
            <td><?php echo htmlspecialchars($faculty['room_name']); ?></td>
        </tr>
    </table>
    <?php else: ?>
    <p>No room assigned.</p>
    <?php endif; ?>

    <?php if (!empty($invigilator_rooms)): ?>
    <h2>Invigilator Room Assignments</h2>
    <table>
        <tr>
            <th>Room Name</th>
        </tr>
        <?php foreach ($invigilator_rooms as $room_name): ?>
            <tr>
                <td><?php echo htmlspecialchars($room_name); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>

    <a href="logout.php">Logout</a>
</body>
</html>
