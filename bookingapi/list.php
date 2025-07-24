<?php
require 'connect.php';
header('Content-Type: application/json; charset=utf-8');
mysqli_set_charset($con, 'utf8mb4');

$userName = $_GET['userName'] ?? '';

if (!$userName) {
    echo json_encode(['error' => 'Missing userName']);
    exit;
}

$reservations = [];
$sql = "SELECT ID, location, start_time, end_time, complete, image_name AS imageName 
        FROM reservations WHERE userName = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $userName);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
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
?>