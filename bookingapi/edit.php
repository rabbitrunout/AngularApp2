<?php
require 'connect.php';
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

// Получаем и чистим данные из POST
$ID           = isset($_POST['ID']) ? (int) $_POST['ID'] : 0;
$location     = mysqli_real_escape_string($con, $_POST['location'] ?? '');
$start_time   = mysqli_real_escape_string($con, $_POST['start_time'] ?? '');
$end_time     = mysqli_real_escape_string($con, $_POST['end_time'] ?? '');
$complete     = isset($_POST['complete']) ? (int) $_POST['complete'] : 0;
$existingImage = mysqli_real_escape_string($con, $_POST['existingImage'] ?? '');
$newImageName = $existingImage; // по умолчанию сохраняем старое имя

// Валидация обязательных полей
if ($ID < 1 || $location === '' || $start_time === '' || $end_time === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Проверка и загрузка нового изображения
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = './uploads/';
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $originalFileName = basename($_FILES['image']['name']);

    // Уникализируем имя файла, если уже существует
    $baseName = pathinfo($originalFileName, PATHINFO_FILENAME);
    $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
    $counter = 0;
    $newFileName = $originalFileName;
    while (file_exists($uploadDir . $newFileName)) {
        $counter++;
        $newFileName = $baseName . '_' . $counter . '.' . $extension;
    }

    $destination = $uploadDir . $newFileName;

    if (!move_uploaded_file($fileTmpPath, $destination)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to upload image']);
        exit;
    }

    // Удаляем старое изображение, если оно не плейсхолдер и отличается от нового
    if ($existingImage && $existingImage !== 'placeholder.jpg' && $existingImage !== $newFileName) {
        $oldImagePath = $uploadDir . $existingImage;
        if (file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }
    }

    $newImageName = $newFileName;
}

// Обновление записи в базе
$sql = "UPDATE reservations SET 
            location = '$location',
            start_time = '$start_time',
            end_time = '$end_time',
            complete = '$complete',
            imageName = '$newImageName'
        WHERE ID = $ID
        LIMIT 1";

if (mysqli_query($con, $sql)) {
    http_response_code(200);
    echo json_encode(['message' => 'Reservation updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database update failed']);
}
?>
