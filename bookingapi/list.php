<?php
header('Access-Control-Allow-Origin: http://localhost:4200'); // ✅ Укажи точно

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'connect.php';

$reservations = [];
$sql = "SELECT id, location, startTime, endTime, complete, imageName FROM reservations";

if ($result = mysqli_query($con, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $reservations[] = [
            'ID' => (int)$row['id'],
            'location' => $row['location'],
            'startTime' => $row['startTime'],
            'endTime' => $row['endTime'],
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
