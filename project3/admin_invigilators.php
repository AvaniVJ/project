<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.html');
    exit();
}

include 'db_connect.php';

// Fetch all faculty and rooms
$faculty_result = mysqli_query($conn, "SELECT * FROM faculty");
$rooms_result = mysqli_query($conn, "SELECT * FROM rooms");

if (!$faculty_result || !$rooms_result) {
    die('Error fetching data: ' . mysqli_error($conn));
}

$faculty = mysqli_fetch_all($faculty_result, MYSQLI_ASSOC);
$rooms = mysqli_fetch_all($rooms_result, MYSQLI_ASSOC);

if (count($faculty) == 0 || count($rooms) == 0) {
    die("Error: Not enough data to allocate invigilators or examiners.");
}

// Separate faculty into examiners and invigilators
$examiner_count = count($rooms);
$invigilator_count = ceil(count($rooms) / 2);

if (count($faculty) < $examiner_count + $invigilator_count) {
    die("Error: Not enough faculty members.");
}

$examiners = array_slice($faculty, 0, $examiner_count);
$invigilators = array_slice($faculty, $examiner_count, $invigilator_count);

$allocations = [];
$invigilations = [];

// Assign each room one examiner
foreach ($rooms as $index => $room) {
    $examiner_id = $examiners[$index]['id'];
    $allocations[] = [
        'teacher_id' => $examiner_id,
        'room_id' => $room['id']
    ];
    mysqli_query($conn, "UPDATE faculty SET role = 'Examiner' WHERE id = $examiner_id");
}

// Assign invigilators to two rooms each
for ($i = 0; $i < count($rooms); $i += 2) {
    $invigilator_index = intdiv($i, 2);
    if ($invigilator_index >= count($invigilators)) break; // Ensure not to exceed available invigilators

    $invigilator_id = $invigilators[$invigilator_index]['id'];

    // Assign invigilator to two rooms if possible
    $room_ids = [$rooms[$i]['id']];
    if ($i + 1 < count($rooms)) {
        $room_ids[] = $rooms[$i + 1]['id'];
    }

    foreach ($room_ids as $room_id) {
        $invigilations[] = [
            'teacher_id' => $invigilator_id,
            'room_id' => $room_id
        ];
    }

    mysqli_query($conn, "UPDATE faculty SET role = 'Invigilator' WHERE id = $invigilator_id");
}

// Insert allocations
mysqli_query($conn, "DELETE FROM allocations"); // Clear previous allocations
foreach ($allocations as $allocation) {
    $teacher_id = $allocation['teacher_id'];
    $room_id = $allocation['room_id'];
    $query = "INSERT INTO allocations (teacher_id, room_id) VALUES ($teacher_id, $room_id)";
    mysqli_query($conn, $query);
}

// Insert invigilations
mysqli_query($conn, "DELETE FROM invigilations"); // Clear previous invigilations
foreach ($invigilations as $invigilation) {
    $teacher_id = $invigilation['teacher_id'];
    $room_id = $invigilation['room_id'];
    $query = "INSERT INTO invigilations (teacher_id, room_id) VALUES ($teacher_id, $room_id)";
    mysqli_query($conn, $query);
}

mysqli_close($conn);

header('Location: admin_dashboard.php');
?>