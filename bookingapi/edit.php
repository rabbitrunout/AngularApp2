<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require 'connect.php';

mysqli_set_charset($con, 'utf8mb4');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

// Получение данных
$id = isset($_POST['ID']) ? (int) $_POST['ID'] : 0;
$location = $_POST['location'] ?? '';
$start_time = $_POST['start_time'] ?? '';
$end_time = $_POST['end_time'] ?? '';
$complete = isset($_POST['complete']) && $_POST['complete'] == '1' ? 1 : 0;
$existingImage = $_POST['existingImage'] ?? '';
$imageName = $existingImage;

// Директория загрузки
$uploadDir = 'uploads/';

// Проверка и загрузка нового изображения
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $originalName = basename($_FILES['image']['name']);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $baseName = pathinfo($originalName, PATHINFO_FILENAME);
    $fileName = $originalName;
    $targetPath = $uploadDir . $fileName;

    // Генерация уникального имени
    $i = 1;
    while (file_exists($targetPath)) {
        $fileName = $baseName . '_' . $i++ . '.' . $ext;
        $targetPath = $uploadDir . $fileName;
    }

    // Загрузка новой картинки
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        // Удаление старой картинки (если имя не совпадает с новым)
        if (!empty($existingImage) && $existingImage !== $fileName) {
            $oldImagePath = $uploadDir . $existingImage;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
        $imageName = $fileName;
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Ошибка при загрузке нового изображения']);
        exit;
    }
}

// Обновление записи
$sql = "UPDATE reservations 
        SET location = ?, start_time = ?, end_time = ?, complete = ?, image_name = ?
        WHERE ID = ?";

$stmt = mysqli_prepare($con, $sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Prepare failed: ' . mysqli_error($con)]);
    exit;
}

mysqli_stmt_bind_param($stmt, "sssisi", $location, $start_time, $end_time, $complete, $imageName, $id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['message' => 'Reservation updated and image handled']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Execute failed: ' . mysqli_stmt_error($stmt)]);
}

mysqli_stmt_close($stmt);
mysqli_close($con);
?>
