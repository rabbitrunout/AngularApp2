<?php
require 'connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Проверка на валидность данных
if (!isset($data['ID'], $data['location'], $data['start_time'], $data['end_time'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Валидация значений
if ((int)$data['ID'] < 1 || 
    trim($data['location']) === '' || 
    trim($data['start_time']) === '' || 
    trim($data['end_time']) === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid field values']);
    exit;
}

// Очистка данных
$ID = mysqli_real_escape_string($con, (int)$data['ID']);
$location = mysqli_real_escape_string($con, trim($data['location']));
$start_time = mysqli_real_escape_string($con, trim($data['start_time']));
$end_time = mysqli_real_escape_string($con, trim($data['end_time']));
$complete = isset($data['complete']) ? (int)$data['complete'] : 0;
$imageName = isset($data['imageName']) ? mysqli_real_escape_string($con, trim($data['imageName'])) : 'placeholder.jpg';

// SQL запрос
$sql = "UPDATE reservations 
        SET location = '$location', 
            start_time = '$start_time', 
            end_time = '$end_time', 
            complete = '$complete', 
            imageName = '$imageName' 
        WHERE ID = '$ID' 
        LIMIT 1";

if (mysqli_query($con, $sql)) {
    http_response_code(200);
    echo json_encode(['message' => 'Reservation updated']);
} else {
    http_response_code(500);
    echo json_encode(['error' => mysqli_error($con)]);
}
?>
