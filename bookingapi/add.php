<?php
require 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['data'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$booking = $data['data'];
$location = mysqli_real_escape_string($con, $booking['location']);
$startTime = mysqli_real_escape_string($con, $booking['startTime']);
$endTime = mysqli_real_escape_string($con, $booking['endTime']);
$complete = $booking['complete'] ? 1 : 0;
$imageName = isset($booking['imageName']) ? mysqli_real_escape_string($con, $booking['imageName']) : '';

$sql = "INSERT INTO reservations (location, startTime, endTime, complete, imageName)
        VALUES ('$location', '$startTime', '$endTime', $complete, '$imageName')";

if (mysqli_query($con, $sql)) {
    echo json_encode(['data' => 'Reservation added successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to add reservation']);
}
?>
