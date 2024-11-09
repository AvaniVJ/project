<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.html');
    exit();
}

include 'db_connect.php';

// Fetch students sorted by department and USN
$student_query = "SELECT s.id, s.name, s.usn, s.department, r.room_name, a.seat_number 
                  FROM students s
                  LEFT JOIN allocations a ON s.id = a.student_id
                  LEFT JOIN rooms r ON a.room_id = r.id
                  ORDER BY s.department, s.usn";
$student_result = mysqli_query($conn, $student_query);

if (!$student_result) {
    die('Error fetching data: ' . mysqli_error($conn));
}

$students = [];
while ($row = mysqli_fetch_assoc($student_result)) {
    $students[] = $row;
}

// Fetch all faculty members with roles
$faculty_query = "
    SELECT f.id, f.name, f.department, f.email, 
           IF(i.teacher_id IS NULL, 'Examiner', 'Invigilator') AS role
    FROM faculty f
    LEFT JOIN invigilations i ON f.id = i.teacher_id
    GROUP BY f.id
";
$faculty_result = mysqli_query($conn, $faculty_query);
if (!$faculty_result) {
    die('Error fetching faculty: ' . mysqli_error($conn));
}
$faculty = mysqli_fetch_all($faculty_result, MYSQLI_ASSOC);

// Fetch rooms
$rooms_query = "SELECT * FROM rooms";
$rooms_result = mysqli_query($conn, $rooms_query);
if (!$rooms_result) {
    die('Error fetching rooms: ' . mysqli_error($conn));
}
$rooms = mysqli_fetch_all($rooms_result, MYSQLI_ASSOC);


// Fetch invigilators details to avoid duplication in the faculty list
$invigilators_query = "
    SELECT t.name AS teacher_name, 
           r.room_name
    FROM invigilations i
    JOIN faculty t ON i.teacher_id = t.id
    JOIN rooms r ON i.room_id = r.id
";
$invigilators_result = mysqli_query($conn, $invigilators_query);
if (!$invigilators_result) {
    die('Error fetching invigilators: ' . mysqli_error($conn));
}
$invigilators = mysqli_fetch_all($invigilators_result, MYSQLI_ASSOC);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Admin Dashboard</h1>
    
    <h2>Registered Students</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>USN</th>
            <th>Department</th>
            <th>Room</th>
            <th>Seat</th>
        </tr>
        <?php foreach ($students as $student): ?>
            <tr>
                <td><?php echo htmlspecialchars($student['name']); ?></td>
                <td><?php echo htmlspecialchars($student['usn']); ?></td>
                <td><?php echo htmlspecialchars($student['department']); ?></td>
                <td><?php echo htmlspecialchars($student['room_name'] ?? 'Seat not allocated yet'); ?></td>
                <td><?php echo htmlspecialchars($student['seat_number'] ?? ''); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    
    <h2>Registered Faculty</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Department</th>
            <th>Email</th>
            <th>Role</th>
        </tr>
        <?php foreach ($faculty as $teacher): ?>
            <tr>
                <td><?php echo htmlspecialchars($teacher['name']); ?></td>
                <td><?php echo htmlspecialchars($teacher['department']); ?></td>
                <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                <td><?php echo htmlspecialchars($teacher['role']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    
       <h2>Invigilators</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Room</th>
        </tr>
        <?php foreach ($invigilators as $invigilator): ?>
            <tr>
                <td><?php echo htmlspecialchars($invigilator['teacher_name']); ?></td>
                <td><?php echo htmlspecialchars($invigilator['room_name']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <form action="allocate_seats.php" method="POST">
        <button type="submit">Allocate Seats for Students</button>
    </form>
    
    <form action="allocate_invigilators.php" method="POST">
        <button type="submit">Allocate Seats for Faculty</button>
    </form>
    
    <a href="logout.php">Logout</a>
</body>
</html>
