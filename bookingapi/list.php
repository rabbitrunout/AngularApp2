<?php
require 'connect.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://localhost:4200');
mysqli_set_charset($con, 'utf8mb4');

// Получаем имя пользователя из GET-параметра
$userName = $_GET['userName'] ?? '';

if (!$userName) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing userName']);
    exit;
}

$reservations = [];

// Подготовленный запрос для защиты от SQL-инъекций
$sql = "SELECT ID, location, start_time, end_time, complete, image_name AS imageName 
        FROM reservations WHERE userName = ?";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 's', $userName);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = $result->fetch_assoc()) {
    $reservations[] = [
        'ID'         => (int)$row['ID'],
        'location'   => $row['location'],
        'start_time' => $row['start_time'],
        'end_time'   => $row['end_time'],
        'complete'   => (bool)$row['complete'],
        'imageName'  => $row['imageName']
    ];
}

echo json_encode(['data' => $reservations]);
?>
