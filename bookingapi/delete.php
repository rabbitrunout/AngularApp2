<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require 'connect.php';

parse_str(file_get_contents("php://input"), $DELETE);

if (!isset($DELETE['ID'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID is required']);
    exit;
}

$ID = (int)$DELETE['ID'];

$sql = "DELETE FROM reservations WHERE ID = $ID";

if (mysqli_query($con, $sql)) {
    echo json_encode(['message' => 'Reservation deleted']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete reservation']);
}
?>