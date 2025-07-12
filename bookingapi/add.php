<?php

require 'connect.php';
$con = connect();

$postdata = file_get_contents("php://input");

if (!$postdata) {
    http_response_code(400);
    echo json_encode(['error' => 'No data received']);
    exit();
}

$request = json_decode($postdata);

if (!isset($request->data) ||
    empty($request->data->location) ||
    empty($request->data->startTime) ||
    empty($request->data->endTime)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit();
}

$location = mysqli_real_escape_string($con, $request->data->location);
$start = mysqli_real_escape_string($con, $request->data->startTime);
$end = mysqli_real_escape_string($con, $request->data->endTime);
$complete = (int) ($request->data->complete ?? 0);
$imageName = basename($request->data->imageName ?? 'placeholder.jpg');

$sql = "INSERT INTO reservations (location, startTime, endTime, complete, imageName)
        VALUES ('$location', '$start', '$end', $complete, '$imageName')";

if (mysqli_query($con, $sql)) {
    http_response_code(201);
    echo json_encode([
        'data' => [
            'ID' => mysqli_insert_id($con),
            'location' => $location,
            'startTime' => $start_time,
            'endTime' => $end_time,
            'complete' => $complete,
            'imageName' => $imageName
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => mysqli_error($con)]);
}
?>
