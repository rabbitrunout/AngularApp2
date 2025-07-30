<?php
session_start();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'connect.php';

$uploadDir = 'uploads/';

// We check that the folder for downloads exists.
if (!is_dir($uploadDir)) {
    http_response_code(500);
    echo json_encode(['message' => 'Upload directory does not exist']);
    exit;
}

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $originalFileName = basename($_FILES['image']['name']);
    $ext = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
    $baseName = pathinfo($originalFileName, PATHINFO_FILENAME);

    // Checking the allowed extensions
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowedExt)) {
        http_response_code(400);
        echo json_encode(['message' => 'Unsupported file extension']);
        exit;
    }

    // Checking the size (for example, no more than 5 MB)
    if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['message' => 'File size exceeds 5MB']);
        exit;
    }

    // Generating a unique file name
    $newFileName = $originalFileName;
    $targetPath = $uploadDir . $newFileName;
    $i = 1;
    while (file_exists($targetPath)) {
        $newFileName = $baseName . '_' . $i++ . '.' . $ext;
        $targetPath = $uploadDir . $newFileName;
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        http_response_code(200);
        echo json_encode(['fileName' => $newFileName]);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to upload image']);
    }
    exit;
}

http_response_code(400);
echo json_encode(['message' => 'No image uploaded']);

?>
