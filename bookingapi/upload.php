<?php
session_start();

header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'connect.php';

// Обработка загрузки изображения
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    $originalFileName = basename($_FILES['image']['name']);
    $ext = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
    $baseName = pathinfo($originalFileName, PATHINFO_FILENAME);

    // Генерация уникального имени файла (если уже существует)
    $newFileName = $originalFileName;
    $targetPath = $uploadDir . $newFileName;
    $i = 1;
    while (file_exists($targetPath)) {
        $newFileName = $baseName . '_' . $i++ . '.' . $ext;
        $targetPath = $uploadDir . $newFileName;
    }

    // Перемещение загруженного файла
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        echo json_encode(['fileName' => $newFileName]);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to upload image']);
    }

    exit;
}

// Если нет файла — ошибка
http_response_code(400);
echo json_encode(['message' => 'No image uploaded']);
?>
