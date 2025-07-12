<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require 'connect.php';

$data = json_decode(file_get_contents("php://input"));

if (
    !isset($data->ID) ||
    !isset($data->location) ||
    !isset($data->startTime) ||
    !isset($data->endTime)
) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$id = (int)$data->ID;
$location = mysqli_real_escape_string($con, $data->location);
$startTime = mysqli_real_escape_string($con, $data->startTime);
$endTime = mysqli_real_escape_string($con, $data->endTime);
$complete = isset($data->complete) ? (int)$data->complete : 0;
$imageName = isset($data->imageName) ? mysqli_real_escape_string($con, $data->imageName) : '';

$sql = "UPDATE reservations SET
        location = '$location',
        startTime = '$startTime',
        endTime = '$endTime',
        complete = '$complete',
        imageName = '$imageName'
        WHERE id = $id";

if (mysqli_query($con, $sql)) {
    echo json_encode(['message' => 'Reservation updated']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Update failed']);
}
?>