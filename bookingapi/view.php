<?php
require 'connect.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing ID']);
    exit;
}

$sql = "SELECT * FROM reservations WHERE ID = {$id} LIMIT 1";

$result = mysqli_query($con, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed']);
    exit;
}

if (mysqli_num_rows($result) === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Reservation not found']);
    exit;
}

$reservation = mysqli_fetch_assoc($result);
$reservation['imageName'] = $reservation['image_name'];
unset($reservation['image_name']); // To avoid duplicating the field

echo json_encode(['data' => $reservation]);

?>
