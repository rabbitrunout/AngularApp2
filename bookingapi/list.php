<?php
require 'connect.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
mysqli_set_charset($con, 'utf8mb4');

$reservations = [];
$sql = "SELECT ID, location, start_time, end_time, complete, image_name AS imageName FROM reservations";

if ($result = mysqli_query($con, $sql)) {
    $count = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $reservations[$count]['ID'] = (int)$row['ID'];
        $reservations[$count]['location'] = $row['location'];
        $reservations[$count]['start_time'] = $row['start_time'];
        $reservations[$count]['end_time'] = $row['end_time'];
        $reservations[$count]['complete'] = (bool)$row['complete'];
        $reservations[$count]['imageName'] = $row['imageName'];

        $count++;
    }

    echo json_encode(['data' => $reservations]);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Failed to retrieve reservations']);
}
?>
