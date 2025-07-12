<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require 'connect.php';

parse_str(file_get_contents("php://input"), $DELETE);

if (!isset($DELETE['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID is required']);
    exit;
}

$id = (int)$DELETE['id'];

$sql = "DELETE FROM reservations WHERE id = $id";

if (mysqli_query($con, $sql)) {
    echo json_encode(['message' => 'Reservation deleted']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete reservation']);
}
?>