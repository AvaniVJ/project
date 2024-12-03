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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
            background:#ff7e5f;;
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
        <h1>Student Dashboard</h1>

        <h2>Student Details</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>USN</th>
                <th>Department</th>
            </tr>
            <tr>
                <td><?= htmlspecialchars($student['name']); ?></td>
                <td><?= htmlspecialchars($student['usn']); ?></td>
                <td><?= htmlspecialchars($student['department']); ?></td>
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
                    <?= isset($student['room_name']) ? htmlspecialchars($student['room_name']) : 'Seat not allocated yet'; ?>
                </td>
                <td>
                    <?= isset($student['seat_number']) ? htmlspecialchars($student['seat_number']) : 'Seat not allocated yet'; ?>
                </td>
            </tr>
        </table>

        <div class="button-container">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
