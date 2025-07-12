<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'connect.php';

header('Content-Type: application/json; charset=utf-8');
mysqli_set_charset($con, 'utf8mb4');

$reservations = [];
$sql = "SELECT ID, location, start_time, end_time, complete, image_name AS imageName FROM reservations";

if ($result = mysqli_query($con, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $reservations[] = [
            'ID' => (int)$row['ID'],
            'location' => $row['location'],
            'start_time' => $row['start_time'],
            'end_time' => $row['end_time'],
            'complete' => (bool)$row['complete'],
            'imageName' => $row['imageName']
        ];
    }

    echo json_encode(['data' => $reservations]);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Failed to retrieve reservations']);
}
?>
