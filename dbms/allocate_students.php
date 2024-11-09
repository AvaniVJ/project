<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.html');
    exit();
}

include 'db_connect.php';

// Fetch all CSE and ENC students
$cse_students_result = mysqli_query($conn, "SELECT * FROM students WHERE department = 'CSE'");
$enc_students_result = mysqli_query($conn, "SELECT * FROM students WHERE department = 'ENC'");
$rooms_result = mysqli_query($conn, "SELECT * FROM rooms");

if (!$cse_students_result || !$enc_students_result || !$rooms_result) {
    die('Error fetching data: ' . mysqli_error($conn));
}

$cse_students = mysqli_fetch_all($cse_students_result, MYSQLI_ASSOC);
$enc_students = mysqli_fetch_all($enc_students_result, MYSQLI_ASSOC);
$rooms = mysqli_fetch_all($rooms_result, MYSQLI_ASSOC);

if (count($cse_students) == 0 || count($enc_students) == 0 || count($rooms) == 0) {
    die("Error: Not enough data to allocate seats.");
}

$room_capacity = 30;
$allocations = [];

// Ensure alternating allocation between CSE and ENC
$students = [];
$max_length = max(count($cse_students), count($enc_students));
for ($i = 0; $i < $max_length; $i++) {
    if (isset($cse_students[$i])) {
        $students[] = $cse_students[$i];
    }
    if (isset($enc_students[$i])) {
        $students[] = $enc_students[$i];
    }
}

for ($i = 0; $i < count($students); $i++) {
    $student = $students[$i];
    $room_index = floor($i / $room_capacity);
    $seat_number = ($i % $room_capacity) + 1;
    if (isset($rooms[$room_index])) {
        $allocations[] = [
            'student_id' => $student['id'],
            'room_id' => $rooms[$room_index]['id'],
            'seat_number' => $seat_number
        ];
    }
}

// Insert allocations
mysqli_query($conn, "DELETE FROM allocations"); // Clear previous allocations
foreach ($allocations as $allocation) {
    $student_id = $allocation['student_id'];
    $room_id = $allocation['room_id'];
    $seat_number = $allocation['seat_number'];
    $query = "INSERT INTO allocations (student_id, room_id, seat_number) VALUES ($student_id, $room_id, $seat_number)";
    mysqli_query($conn, $query);
}

mysqli_close($conn);

header('Location: admin_dashboard.php');
?>
