<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    $originalFileName = basename($_FILES['image']['name']);
    $targetFilePath = $uploadDir . $originalFileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
        echo json_encode(['fileName' => $originalFileName]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Image upload failed']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'No image uploaded']);
}
?>
