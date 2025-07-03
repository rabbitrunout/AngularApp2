<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

require 'connect.php';

$data = json_decode(file_get_contents("php://input"));

if (
    isset($data->location) &&
    isset($data->startTime) &&
    isset($data->endTime)
) {
    $location = mysqli_real_escape_string($con, $data->location);
    $startTime = mysqli_real_escape_string($con, $data->startTime);
    $endTime = mysqli_real_escape_string($con, $data->endTime);
    $complete = isset($data->complete) ? (int)$data->complete : 0;
    $imageName = isset($data->imageName) ? mysqli_real_escape_string($con, $data->imageName) : '';

    $sql = "INSERT INTO reservations (location, startTime, endTime, complete, imageName)
            VALUES ('$location', '$startTime', '$endTime', '$complete', '$imageName')";

    if (mysqli_query($con, $sql)) {
        http_response_code(201);
        echo json_encode(['message' => 'Reservation added']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database insert failed']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
}
?>
