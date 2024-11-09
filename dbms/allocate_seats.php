<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.html');
    exit();
}

include 'db_connect.php';

// Fetch students grouped by department and sorted by USN
$student_query = "SELECT * FROM students ORDER BY department, usn";
$student_result = mysqli_query($conn, $student_query);

if (!$student_result) {
    die('Error fetching data: ' . mysqli_error($conn));
}

$students = [];
while ($row = mysqli_fetch_assoc($student_result)) {
    $students[$row['department']][] = $row;
}

// Fetch all rooms
$room_query = "SELECT * FROM rooms";
$room_result = mysqli_query($conn, $room_query);

if (!$room_result) {
    die('Error fetching data: ' . mysqli_error($conn));
}

$rooms = mysqli_fetch_all($room_result, MYSQLI_ASSOC);

if (count($students) == 0 || count($rooms) == 0) {
    die("Error: Not enough data to allocate seats.");
}

// Reset previous allocations
mysqli_query($conn, "DELETE FROM allocations");

// Prepare allocation variables
$current_seat = 1;
$current_room_index = 0;
$room_capacity = 30;
$department_indices = array_fill_keys(array_keys($students), 0);

// Allocate seats alternately by department
$departments = array_keys($students);
$students_allocated = 0;
$total_students = array_sum(array_map('count', $students));

while ($students_allocated < $total_students) {
    foreach ($departments as $department) {
        $index = $department_indices[$department];
        if ($index < count($students[$department])) {
            $student = $students[$department][$index];
            $room_id = $rooms[$current_room_index]['id'];

            $query = "INSERT INTO allocations (student_id, room_id, seat_number) VALUES ({$student['id']}, $room_id, $current_seat)";
            if (!mysqli_query($conn, $query)) {
                die('Error allocating seat: ' . mysqli_error($conn));
            }

            $department_indices[$department]++;
            $current_seat++;
            $students_allocated++;

            if ($current_seat > $room_capacity) {
                $current_seat = 1;
                $current_room_index++;
                if ($current_room_index >= count($rooms)) {
                    break 2; // Exit both loops when all rooms are filled
                }
            }
        }
    }
}

mysqli_close($conn);

header('Location: admin_dashboard.php');
?>
