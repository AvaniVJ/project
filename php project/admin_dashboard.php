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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background:  #20c997;
            color: #fff;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            color: #333;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            color: #555;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th {
            background:#ff7e5f;
            color: #fff;
            text-transform: uppercase;
        }

        td {
            padding: 10px;
            text-align: left;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        button {
            background: #ff7e5f;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
            display: inline-block;
            margin: 10px 5px;
        }

        button:hover {
            background: #20c997;
        }

        .actions {
            text-align: center;
            margin-top: 20px;
        }

        a {
            color:#ff7e5f;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        
        <h2>Registered Students</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>USN</th>
                    <th>Department</th>
                    <th>Room</th>
                    <th>Seat</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                        <td><?php echo htmlspecialchars($student['usn']); ?></td>
                        <td><?php echo htmlspecialchars($student['department']); ?></td>
                        <td><?php echo htmlspecialchars($student['room_name'] ?? 'Not Allocated'); ?></td>
                        <td><?php echo htmlspecialchars($student['seat_number'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h2>Registered Faculty</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($faculty as $teacher): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($teacher['name']); ?></td>
                        <td><?php echo htmlspecialchars($teacher['department']); ?></td>
                        <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                        <td><?php echo htmlspecialchars($teacher['role']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Invigilators</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Room</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invigilators as $invigilator): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($invigilator['teacher_name']); ?></td>
                        <td><?php echo htmlspecialchars($invigilator['room_name']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="actions">
            <form action="allocate_seats.php" method="POST" style="display:inline;">
                <button type="submit">Allocate Seats for Students</button>
            </form>
            
            <form action="allocate_invigilators.php" method="POST" style="display:inline;">
                <button type="submit">Allocate Seats for Faculty</button>
            </form>
            
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
