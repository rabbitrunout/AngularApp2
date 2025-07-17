<?php
require 'connect.php';

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $uploadDir = 'uploads/';
    $fileName = basename($_FILES['image']['name']);
    $targetPath = $uploadDir . $fileName;

    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $baseName = pathinfo($fileName, PATHINFO_FILENAME);
    $i = 1;
    while (file_exists($targetPath)) {
        $fileName = $baseName . '_' . $i++ . '.' . $ext;
        $targetPath = $uploadDir . $fileName;
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        echo json_encode(['fileName' => $fileName]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Upload failed']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded']);
}
?>
