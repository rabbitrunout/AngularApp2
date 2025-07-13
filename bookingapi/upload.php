<?php

  session_start();

  header('Content-Type: application/json');
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  require 'connect.php';


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