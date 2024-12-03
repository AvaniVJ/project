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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px;
            background: #20c997;
        }

        .container {
            background: #fff;
            margin: auto;
            max-width: 800px;
            padding: 30px;
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
            margin: 20px 0;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        table th {
            background:  #ff7e5f;
            color: #333;
        }

        table td {
            background: #ffffff;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        a {
            background: #ff7e5f;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        a:hover {
            background: #feb47b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Faculty Dashboard</h1>

        <h2>Faculty Details</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Department</th>
                <th>Email</th>
            </tr>
            <tr>
                <td><?= htmlspecialchars($faculty['name']); ?></td>
                <td><?= htmlspecialchars($faculty['department']); ?></td>
                <td><?= htmlspecialchars($faculty['email']); ?></td>
            </tr>
        </table>

        <?php if (isset($faculty['room_name'])): ?>
            <h2>Examiner Room Assignment</h2>
            <table>
                <tr>
                    <th>Room Name</th>
                </tr>
                <tr>
                    <td><?= htmlspecialchars($faculty['room_name']); ?></td>
                </tr>
            </table>
        <?php else: ?>
            <p style="text-align: center;">No room assigned.</p>
        <?php endif; ?>

        <?php if (!empty($invigilator_rooms)): ?>
            <h2>Invigilator Room Assignments</h2>
            <table>
                <tr>
                    <th>Room Name</th>
                </tr>
                <?php foreach ($invigilator_rooms as $room_name): ?>
                    <tr>
                        <td><?= htmlspecialchars($room_name); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <div class="button-container">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
