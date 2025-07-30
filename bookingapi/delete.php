<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'connect.php';

// Получаем данные из POST JSON
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->ID) || (int)$data->ID < 1) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid or missing reservation ID']);
    exit();
}

$ID = mysqli_real_escape_string($con, (int)$data->ID);

// We get the name of the image before deleting it
$getImageQuery = "SELECT imageName FROM reservations WHERE ID = '$ID' LIMIT 1";
$result = mysqli_query($con, $getImageQuery);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $imageName = $row['imageName'];

    if ($imageName && $imageName !== 'placeholder.jpg') {
        $imagePath = __DIR__ . "/uploads/" . $imageName;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Reservation not found']);
    exit();
}

$sql = "DELETE FROM reservations WHERE ID = '$ID' LIMIT 1";

if (mysqli_query($con, $sql)) {
    http_response_code(204); // No Content, successfully deleted
    exit();
} else {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to delete reservation']);
}
?>
