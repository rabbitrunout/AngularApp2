<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No valid file uploaded']);
    exit();
}

$originalFileName = basename($_FILES['image']['name']);
$targetFilePath = $uploadDir . $originalFileName;

if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
    echo json_encode(['message' => 'File uploaded', 'fileName' => $originalFileName]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to move uploaded file']);
}
?>